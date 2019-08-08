<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\AstilleroCotizaServicio;
use AppBundle\Entity\AstilleroServicioBasico;
use AppBundle\Entity\Correo;
use AppBundle\Entity\MonederoMovimiento;
use AppBundle\Entity\Pincode;
use AppBundle\Form\AstilleroCotizacionType;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SensioLabs\Security\Exception\HttpException;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ValorSistema;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

/**
 * Astillerocotizacion controller.
 *
 * @Route("/astillero")
 */
class AstilleroCotizacionController extends Controller
{
    /**
     * Enlista todas las cotizaciones de astillero
     *
     * @Route("/", name="astillero_index")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'cotizacionAstillero');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getCode());
            }
        }
        return $this->render('astillero/cotizacion/index.html.twig', [
            'title' => 'Cotizaciones',
            'borrador' => '0'
        ]);
    }

    /**
     * Enlista todas las cotizaciones de astillero
     *
     * @Route("/borradores/", name="astillero_borrador_index")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function indexBorradorAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'cotizacionAstilleroBorrador');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getCode());
            }
        }
        return $this->render('astillero/cotizacion/index.html.twig', [
            'title' => 'Borrador Cotizaciones',
            'borrador' => '1'
        ]);
    }

    /**
     * Crea una nueva cotizacion de astillero
     * cantidades se guardan en pesos
     *
     * @Route("/nueva", name="astillero_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $astilleroCotizacion = new AstilleroCotizacion();
        $this->denyAccessUnlessGranted("ASTILLERO_COTIZACION_CREATE", $astilleroCotizacion);

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        /* Obtiene los precios de los servicios básicos de la DB */
        $queryBasico = $qb->select("sb")->from(astilleroServicioBasico::class, "sb")->getQuery();
        $preciosBasicos = $queryBasico->getArrayResult();
        
        /* Otiene los valores generales de sistema, dolar e IVA y el email template */
        $sistema = $em->getRepository("AppBundle:ValorSistema")->find(1);
        $dolar = $sistema->getDolar();
        $iva = $sistema->getIva();
        $mensaje = $sistema->getMensajeCorreoAstillero();
        $astilleroCotizacion->setDolar($dolar)->setMensaje($mensaje);

        /* Crea los servicios básicos, los setea, y los añade al objeto $astilleroCotizacion */
        $astilleroGrua = new AstilleroCotizaServicio();
        $astilleroEstadia = new AstilleroCotizaServicio();
        $astilleroRampa = new AstilleroCotizaServicio();
        $astilleroExplanada = new AstilleroCotizaServicio();
        $astilleroElectricidad = new AstilleroCotizaServicio();
        $astilleroKarcher = new AstilleroCotizaServicio();
        $astilleroLimpieza = new AstilleroCotizaServicio();
        $astilleroInspeccionar = new AstilleroCotizaServicio();

        $astilleroGrua
            ->setPrecio($preciosBasicos[0]["precio"])
            ->setDivisa("MXN");

        $astilleroEstadia
            ->setCantidad(6)
            ->setPrecio($preciosBasicos[1]["precio"])
            ->setDivisa("USD");
        
        $astilleroRampa = $this->calculaServicio($astilleroRampa, 1, $preciosBasicos[2]["precio"], $iva, "MXN");
        $astilleroKarcher = $this->calculaServicio($astilleroKarcher, 1, $preciosBasicos[3]["precio"], $iva, "MXN");
        $astilleroExplanada = $this->calculaServicio($astilleroExplanada, 1, $preciosBasicos[4]["precio"], $iva, "MXN");
        $astilleroElectricidad = $this->calculaServicio($astilleroElectricidad, 1, $preciosBasicos[5]["precio"], $iva, "MXN");
        $astilleroLimpieza = $this->calculaServicio($astilleroLimpieza, 1, $preciosBasicos[6]["precio"], $iva, "MXN");

        $astilleroInspeccionar
            ->setPrecio($preciosBasicos[7]["precio"])
            ->setDivisa("MXN");

        $astilleroCotizacion
            ->addAcservicio($astilleroGrua)
            ->addAcservicio($astilleroEstadia)
            ->addAcservicio($astilleroRampa)
            ->addAcservicio($astilleroKarcher)
            ->addAcservicio($astilleroExplanada)
            ->addAcservicio($astilleroElectricidad)
            ->addAcservicio($astilleroLimpieza)
            ->addAcservicio($astilleroInspeccionar);
        
        /* Crea los formularios dentro de la vista */
        $form = $this->createForm("AppBundle\Form\AstilleroCotizacionType", $astilleroCotizacion);
        $form->handleRequest($request);

        /* si se hace submit y todos los datos están correctos */
        if ($form->isSubmitted() && $form->isValid()) {
            $valordolar = ($astilleroCotizacion->getDolar())/100;
            $eslora = $astilleroCotizacion->getBarco()->getEslora();
            $cantidadDias = $astilleroCotizacion->getDiasEstadia();
            $sumas = ["granSubtotal"=>0, "granIva"=>0, "granTotal"=>0];

            // Uso de grua
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(1);
            $cantidad = $eslora;
            $precio = $astilleroGrua->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroGrua, $servicio, $cantidad, $precio, $iva, $sumas, $valordolar);
            // Estadía
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(2);
            $cantidad = $cantidadDias * $eslora;
            $precio = $astilleroEstadia->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroEstadia, $servicio, $cantidad, $precio, $iva, $sumas, $valordolar);
            // Uso de rampa
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(3);
            $cantidad = $astilleroRampa->getCantidad();
            $precio = $astilleroRampa->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroRampa, $servicio, $cantidad, $precio, $iva, $sumas, $valordolar);
            // Uso de karcher
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(4);
            $cantidad = $astilleroKarcher->getCantidad();
            $precio = $astilleroKarcher->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroKarcher, $servicio, $cantidad, $precio, $iva, $sumas, $valordolar);
            //uso de explanada
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(5);
            $cantidad = $astilleroExplanada->getCantidad();
            $precio = $astilleroExplanada->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroExplanada, $servicio, $cantidad, $precio, $iva, $sumas, $valordolar);
            //Conexión a electricidad
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(6);
            $cantidad = $astilleroElectricidad->getCantidad();
            $precio = $astilleroElectricidad->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroElectricidad, $servicio, $cantidad, $precio, $iva, $sumas, $valordolar);
            //Limpieza de locación
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(7);
            $cantidad = $astilleroLimpieza->getCantidad();
            $precio = $astilleroLimpieza->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroLimpieza, $servicio, $cantidad, $precio, $iva, $sumas, $valordolar);
            //Sacar para inspeccionar
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(8);
            $cantidad = $eslora;
            $precio = $astilleroInspeccionar->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroInspeccionar, $servicio, $cantidad, $precio, $iva, $sumas, $valordolar);

            /* Obtiene el precio de cada servicio que se incluye en la cotización. Ya sea
            "otros", "servicio básico", un "kit" o un "producto" todos vienen como "servicios", y debe
            inferir su tipo. */
            foreach ($astilleroCotizacion->getAcservicios() as $servAst) {
                if ($servAst->getAstilleroserviciobasico() == null) {
                    $cantidad = $servAst->getCantidad();

                    /* Busca el tipo de servicio que es */
                    if ($servAst->getServicio() != null) {
                        $divisa = $servAst->getServicio()->getDivisa();
                        $precio = $servAst->getServicio()->getPrecio();
                    } elseif($servAst->getOtroservicio() != null) {
                        $divisa = "MXN";
                        $precio = $servAst->getPrecio();
                    } elseif($servAst->getProducto() != null) {
                        $divisa = "MXN";
                        $precio = $servAst->getProducto()->getPrecio();
                    } else {
                        $divisa = "MXN";
                        $precio = 0;
                    }
                    if($divisa == "USD") {
                        $subTotal = ($cantidad * $precio * $valordolar);
                    }else {
                        $subTotal = $cantidad * $precio;
                    }

                    /* Calcular los valores totales */
                    $ivaTot = $subTotal * ($iva/100);
                    $total = $subTotal + $ivaTot;

                    /* Setea los valores */
                    $servAst->setPrecio($precio)
                            ->setSubtotal($subTotal)
                            ->setIva($ivaTot)
                            ->setTotal($total)
                            ->setDivisa($divisa)
                            ->setEstatus(true);
                    
                    /* Recupera en un objeto los totales y los va acumulando */
                    $sumas = ["granSubtotal"=>$sumas["granSubtotal"]+=$subTotal,
                              "granIva"=>$sumas["granIva"]+=$ivaTot,
                              "granTotal"=>$sumas["granTotal"]+=$total];
                }
            }

            /* Cálculo de los totales de la cotización */
            $descuento = $astilleroCotizacion->getDescuento() / 100;
            $granSubtotal = $sumas["granSubtotal"];
            $granDescuento = $granSubtotal * $descuento;
            $subtotalConDescuento = $granSubtotal - $granDescuento;
            $granIva = $subtotalConDescuento * ($iva/100);
            $granTotal = $subtotalConDescuento + $granIva;

            $fechaHoraActual = new \DateTime("now");
            $astilleroCotizacion
                ->setDolar($astilleroCotizacion->getDolar())
                ->setIva($iva)
                ->setSubtotal($granSubtotal)
                ->setDescuentototal($granDescuento)
                ->setIvatotal($granIva)
                ->setTotal($granTotal)
                ->setFecharegistro($fechaHoraActual)
                ->setEstatus(true);

            // Asignacion de cotizacion al cliente y viceversa
            $cliente = $astilleroCotizacion->getBarco()->getCliente();
            $cliente->addAstilleroCotizacione($astilleroCotizacion);
            $astilleroCotizacion->setCliente($cliente);

            $guardarEditable = $form->get("guardareditable")->isClicked();
            $guardarFinalizar = $form->get("guardarfinalizar")->isClicked();

            if ($guardarEditable) {
                $astilleroCotizacion->setBorrador(true);
                $astilleroCotizacion->setFolio(0);
            } else {
                $foliobase = $sistema->getFolioMarina();
                $folionuevo = $foliobase + 1;
                $astilleroCotizacion->setFolio($folionuevo);
                $astilleroCotizacion->setBorrador(false);
                $this->getDoctrine()
                    ->getRepository(ValorSistema::class)
                    ->find(1)
                    ->setFolioMarina($folionuevo);

                // Asignarle a la cotizacion, quien la creo (El usuario actualmente logueado)
                $astilleroCotizacion->setCreador($this->getUser());
            }

            $em->persist($astilleroCotizacion);
            $em->flush();

            if ($guardarFinalizar) {
                $pincode = $em->getRepository(Pincode::class)
                    ->getOneValid($form->get("pincode")->getViewData());
                if($pincode){
                    $em->remove($pincode);
                }
                // Buscar correos a notificar
                $notificables = $em->getRepository("AppBundle:Correo\Notificacion")->findBy([
                    "evento" => Correo\Notificacion::EVENTO_CREAR,
                    "tipo" => Correo\Notificacion::TIPO_ASTILLERO
                ]);

                $this->enviaCorreoNotificacion($mailer, $notificables, $astilleroCotizacion);
            }

            return $this->redirectToRoute("astillero_show", ["id" => $astilleroCotizacion->getId()]);
        }

        return $this->render("astillero/cotizacion/new.html.twig", [
            "title" => "Nueva cotización",
            "astilleroCotizacion" => $astilleroCotizacion,
            "valdolar" => $dolar,
            "valiva" => $iva,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/cliente.json")
     * @Route("/borradores/cliente.json")
     *
     * @return Response
     */
    public function getClientesAction()
    {
        $clientes = $this->getDoctrine()->getRepository('AppBundle:AstilleroCotizacion')->getAllClientes();

        return new JsonResponse($clientes);
    }

    /**
     * @Route("/barco.json")
     * @Route("/borradores/barco.json")
     *
     * @return Response
     */
    public function getBarcosAction()
    {
        $barcos = $this->getDoctrine()->getRepository('AppBundle:AstilleroCotizacion')->getAllBarcos();

        return new JsonResponse($barcos);
    }

    /**
     * Muestra una cotizacion de astillero
     *
     * @Route("/{id}", name="astillero_show")
     * @Method("GET")
     *
     * @param AstilleroCotizacion $astilleroCotizacion
     *
     * @return Response
     */
    public function showAction(AstilleroCotizacion $astilleroCotizacion)
    {
        $deleteForm = $this->createDeleteForm($astilleroCotizacion);

        return $this->render('astillero/cotizacion/show.html.twig', [
            'title' => 'Cotización',
            'astilleroCotizacion' => $astilleroCotizacion,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Genera el pdf de una cotizacion en base a su id
     *
     * @Route("/{id}/pdf/{tipo}", name="astillero-pdf")
     * @Method("GET")
     *
     * @param AstilleroCotizacion $ac
     * @param $tipo
     *
     * @return PdfResponse
     */
    public function displayMarinaPDFAction(AstilleroCotizacion $ac, $tipo)
    {
        $em = $this->getDoctrine()->getManager();
        $valor = $em->getRepository('AppBundle:ValorSistema')->find(1);

        $bancoPesos = $em->getRepository('AppBundle:CuentaBancaria')->findOneBy(['empresa' => 5,'moneda' => 1]);
        $bancoDolares = $em->getRepository('AppBundle:CuentaBancaria')->findOneBy(['empresa' => 5,'moneda' => 2]);


        if ($tipo == 1) { //pesos
            $html = $this->renderView('astillero/cotizacion/pdf/cotizacionpdf.html.twig', [
                'astilleroCotizacion' => $ac,
                'valor' => $valor,
                'bancoPesos' => $bancoPesos,
                'bancoDolares' => $bancoDolares
            ]);
        } else { //dolares
            $html = $this->renderView('astillero/cotizacion/pdf/cotizacion-pesospdf.html.twig', [
                'astilleroCotizacion' => $ac,
                'valor' => $valor,
                'bancoPesos' => $bancoPesos,
                'bancoDolares' => $bancoDolares
            ]);
        }

        return $this->render('astillero/cotizacion/pdf/cotizacionpdf.html.twig', [
            'astilleroCotizacion' => $ac,
            'valor' => $valor,
            'bancoPesos' => $bancoPesos,
            'bancoDolares' => $bancoDolares
        ]);

        $header = $this->renderView('astillero/cotizacion/pdf/pdfencabezado.twig', [
            'astilleroCotizacion' => $ac,
            'valor' => $valor
        ]);
        $footer = $this->renderView('astillero/cotizacion/pdf/pdfpie.twig',[
            'valor' => $valor
        ]);

        $hojapdf = $this->get('knp_snappy.pdf');

        $options = [
            'margin-top' => 19,
            'margin-right' => 0,
            'margin-left' => 0,
            'header-html' => utf8_decode($header),
            'footer-html' => utf8_decode($footer)
        ];

        return new PdfResponse(
            $hojapdf->getOutputFromHtml($html, $options),
            'Cotizacion-' . $ac->getFolio() . '-' . $ac->getFoliorecotiza() . '.pdf',
            'application/pdf',
            'inline'
        );
    }


    /**
     * @Route("/{id}/editar", name="astillero_editar")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param AstilleroCotizacion $astilleroCotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function editAction(Request $request, AstilleroCotizacion $astilleroCotizacion, \Swift_Mailer $mailer)
    {
        $this->denyAccessUnlessGranted('ASTILLERO_COTIZACION_CREATE', $astilleroCotizacion);
        if($astilleroCotizacion->getBorrador() == false){
            throw new NotFoundHttpException();
        }
        $servicios = $astilleroCotizacion->getAcservicios();
        $astilleroGrua = $servicios[0];
        $astilleroRampa = $servicios[2];
        $astilleroKarcher = $servicios[3];
        $astilleroExplanada = $servicios[4];
        $astilleroLimpieza = $servicios[6];
        $astilleroInspeccionar = $servicios[7];
        $astilleroElectricidad = $servicios[5];
        $astilleroEstadia = $servicios[1];

        $serviciosOriginales = new ArrayCollection();
        foreach ($astilleroCotizacion->getAcservicios() as $servicioOriginal){
            //if($servicioOriginal->getAstilleroserviciobasico() == null){
                $serviciosOriginales->add($servicioOriginal);
            //}
        }
        $deleteForm = $this->createDeleteForm($astilleroCotizacion);
        $form = $this->createForm(AstilleroCotizacionType::class, $astilleroCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $valordolar = $astilleroCotizacion->getDolar();
            $iva = $astilleroCotizacion->getIva();
            $eslora = $astilleroCotizacion->getBarco()->getEslora();
            $cantidadDias = $astilleroCotizacion->getDiasEstadia();
            $sumas = ['granSubtotal'=>0,'granIva'=>0,'granTotal'=>0];
            // Uso de grua
            $cantidad = $eslora;
            $precio = $astilleroGrua->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroGrua,$cantidad,$precio,$iva,$sumas,$valordolar);
            // Estadía
            $cantidad = $cantidadDias * $eslora;
            $precio = $astilleroEstadia->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroEstadia,$cantidad,$precio,$iva,$sumas,$valordolar);
            // Uso de rampa
            $cantidad = $astilleroRampa->getCantidad();
            $precio = $astilleroRampa->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroRampa,$cantidad,$precio,$iva,$sumas,$valordolar);
            // Uso de karcher
            $cantidad = $astilleroKarcher->getCantidad();
            $precio = $astilleroKarcher->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroKarcher,$cantidad,$precio,$iva,$sumas,$valordolar);
            //uso de explanada
            $cantidad = $astilleroExplanada->getCantidad();
            $precio = $astilleroExplanada->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroExplanada,$cantidad,$precio,$iva,$sumas,$valordolar);
            //Conexión a electricidad
            $cantidad = $astilleroElectricidad->getCantidad();
            $precio = $astilleroElectricidad->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroElectricidad,$cantidad,$precio,$iva,$sumas,$valordolar);
            //Limpieza de locación
            $cantidad = $astilleroLimpieza->getCantidad();
            $precio = $astilleroLimpieza->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroLimpieza,$cantidad,$precio,$iva,$sumas,$valordolar);
            //Sacar para inspeccionar
            $cantidad = $eslora;
            $precio = $astilleroInspeccionar->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroInspeccionar,$cantidad,$precio,$iva,$sumas,$valordolar);
            $em = $this->getDoctrine()->getManager();
            foreach ($serviciosOriginales as $servicioOriginal) {
                if (false === $astilleroCotizacion->getAcservicios()->contains($servicioOriginal)) {
                    $servicioOriginal->getAstillerocotizacion()->removeAcservicio($servicioOriginal);
                    //$servicioOriginal->setAstillerocotizacion(null);
                    $em->persist($servicioOriginal);
                    $em->remove($servicioOriginal);
                }
            }
            foreach ($astilleroCotizacion->getAcservicios() as $servAst){
                if ($servAst->getAstilleroserviciobasico() == null){
                    $cantidad = $servAst->getCantidad();
                    if ($servAst->getServicio() != null) {
                        $divisa = $servAst->getServicio()->getDivisa();
                        $precio = $servAst->getServicio()->getPrecio();
                    }elseif($servAst->getOtroservicio() != null){
                        $divisa = 'MXN';
                        $precio = $servAst->getPrecio();
                    }elseif($servAst->getProducto() != null){
                        $divisa = 'MXN';
                        $precio = $servAst->getProducto()->getPrecio();
                    }else{
                        $divisa = 'MXN';
                        $precio = 0;
                    }
                    if($divisa == 'USD'){
                        $subTotal = ($cantidad * $precio * $valordolar)/100;
                    }else{
                        $subTotal = $cantidad * $precio;
                    }
                    $ivaTot = ($subTotal * $iva) / 100;
                    $total = $subTotal + $ivaTot;
                    $servAst->setPrecio($precio)
                        ->setSubtotal($subTotal)
                        ->setIva($ivaTot)
                        ->setTotal($total)
                        ->setDivisa($divisa)
                        ->setEstatus(true);
                    $sumas = ['granSubtotal'=>$sumas['granSubtotal']+=$subTotal,
                        'granIva'=>$sumas['granIva']+=$ivaTot,
                        'granTotal'=>$sumas['granTotal']+=$total];
                }
            }
            $granDescuento = ($sumas['granSubtotal'] * $astilleroCotizacion->getDescuento())/100;
            $granIva = (($sumas['granSubtotal'] - $granDescuento) * $iva) / 100;
            $granTotal = $sumas['granSubtotal'] - $granDescuento + $granIva;
            //------------------------------------------------

            $fechaHoraActual = new \DateTime('now');
            $astilleroCotizacion
                ->setDolar($astilleroCotizacion->getDolar())
                ->setIva($iva)
                ->setSubtotal($sumas['granSubtotal'])
                ->setDescuentototal($granDescuento)
                ->setIvatotal($granIva)
                ->setTotal($granTotal)
                ->setFecharegistro($fechaHoraActual)
                ->setEstatus(true);

            $guardarEditable = $form->get('guardareditable')->isClicked();
            $guardarFinalizar = $form->get('guardarfinalizar')->isClicked();
            if($guardarEditable){
                $astilleroCotizacion->setBorrador(true);
                //$astilleroCotizacion->setFolio(0);

            } else {
                $astilleroCotizacion->setBorrador(false);
                //calcular folio para cotizacion nueva y recotizada
                if($astilleroCotizacion->getFolio() == 0){ // si tiene folio 0 significa que es cotizacion nueva
                    $sistema = $em->getRepository('AppBundle:ValorSistema')->find(1);
                    $foliobase = $sistema->getFolioMarina();
                    $folionuevo = $foliobase + 1;
                    $astilleroCotizacion->setFolio($folionuevo);
                    $this->getDoctrine()
                        ->getRepository(ValorSistema::class)
                        ->find(1)
                        ->setFolioMarina($folionuevo);
                }

                // Asignacion de cotizacion al cliente y viceversa
                $cliente = $astilleroCotizacion->getBarco()->getCliente();
                $cliente->addAstilleroCotizacione($astilleroCotizacion);
                $astilleroCotizacion->setCliente($cliente);

                // Asignarle a la cotizacion, quien la creo (El usuario actualmente logueado)
                $astilleroCotizacion->setCreador($this->getUser());

            }

            $em->persist($astilleroCotizacion);
            $em->flush();

            if($guardarFinalizar){
                $pincode = $em->getRepository(Pincode::class)
                    ->getOneValid($form->get('pincode')->getViewData());
                if($pincode){
                    $em->remove($pincode);
                }

                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_CREAR,
                    'tipo' => Correo\Notificacion::TIPO_ASTILLERO
                ]);
                $this->enviaCorreoNotificacion($mailer, $notificables, $astilleroCotizacion);

            }
            return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
        }
        return $this->render('astillero/cotizacion/edit.html.twig', [
            'title' => 'Borrador Astillero Cotización',
            'astilleroCotizacion' => $astilleroCotizacion,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView()
        ]);

    }

    /**
     * Confirma la respuesta de un cliente a una cotizacion
     *
     * @Route("/{id}/validar", name="astillero_validar")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param AstilleroCotizacion $astilleroCotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function validaAction(Request $request, AstilleroCotizacion $astilleroCotizacion, \Swift_Mailer $mailer)
    {
        $this->denyAccessUnlessGranted('ASTILLERO_COTIZACION_VALIDATE', $astilleroCotizacion);

        if ($astilleroCotizacion->isEstatus() == 0 ||
            $astilleroCotizacion->getValidanovo() == 1 ||
            $astilleroCotizacion->getValidacliente() == 1 ||
            $astilleroCotizacion->getValidacliente() == 2 ||
            $astilleroCotizacion->getBorrador()
        ) {
            throw new NotFoundHttpException();
        }

        $editForm = $this->createForm('AppBundle\Form\AstilleroCotizacionValidarType', $astilleroCotizacion);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $astilleroCotizacion->setNombrevalidanovo($this->getUser()->getNombre());

            if ($astilleroCotizacion->getValidanovo() === 2
                && $astilleroCotizacion->getValidacliente() !== 2
                && $astilleroCotizacion->isNotificarCliente()
            ) {
                $token = $astilleroCotizacion->getFolio() . bin2hex(random_bytes(16));
                $astilleroCotizacion->setToken($token);

                $folio = $astilleroCotizacion->getFoliorecotiza()
                    ? $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza()
                    : $astilleroCotizacion->getFolio();

                $attachment = new Swift_Attachment($this->displayMarinaPDFAction($astilleroCotizacion, 1),
                    'cotizacionMXN_' . $folio . '.pdf',
                    'application/pdf'
                );
                $attachmentMXN = new Swift_Attachment($this->displayMarinaPDFAction($astilleroCotizacion, 2),
                    'cotizacionUSD_' . $folio . '.pdf',
                    'application/pdf'
                );

                // Enviar correo de confirmacion
                $message = (new \Swift_Message('¡Cotizacion de servicios Astillero!'))
                    ->setFrom('noresponder@novonautica.com')
                    ->setTo($astilleroCotizacion->getBarco()->getCliente()->getCorreo())
                    ->setBcc('admin@novonautica.com')
                    ->setBody(
                        $this->renderView('mail/cotizacion.html.twig', [
                                'cotizacion' => $astilleroCotizacion,
                            ]
                        ),
                        'text/html'
                    )
                    ->attach($attachment)
                    ->attach($attachmentMXN);

                if ($astilleroCotizacion->getBarco()->getCorreoCapitan()) {
                    $message->addCc($astilleroCotizacion->getBarco()->getCorreoCapitan());
                }

                if ($astilleroCotizacion->getBarco()->getCorreoResponsable()) {
                    $message->addCc($astilleroCotizacion->getBarco()->getCorreoResponsable());
                }

                $mailer->send($message);

                $tipoCorreo = $astilleroCotizacion->getFoliorecotiza() === 0
                    ? 'Cotización servicio Astillero'
                    : 'Recotización Servicio Astillero';

                // Guardar correo en el log de correos
                $historialCorreo = new Correo();
                $historialCorreo
                    ->setFecha(new \DateTime('now'))
                    ->setTipo($tipoCorreo)
                    ->setDescripcion('Envio de cotización de Astillero con folio: ' . $folio)
                    ->setFolioCotizacion($folio)
                    ->setAcotizacion($astilleroCotizacion);

                $em->persist($historialCorreo);

                // Guardar la fecha en la que se valido la cotización por novonautica y agregar fecha límite para
                // aceptación por el cliente
                $sistema = $em->getRepository('AppBundle:ValorSistema')->find(1);
                $diasAstillero = $sistema->getDiasHabilesAstilleroCotizacion();
                $astilleroCotizacion
                    ->setRegistroValidaNovo(new \DateTimeImmutable())
                    ->setLimiteValidaCliente((new \DateTime('now'))->modify('+ '.$diasAstillero.' day'));

                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_VALIDAR,
                    'tipo' => Correo\Notificacion::TIPO_ASTILLERO
                ]);

                $this->enviaCorreoNotificacion($mailer, $notificables, $astilleroCotizacion);
            }

            if ($astilleroCotizacion->getValidacliente() === 2) {
                // Guardar la fecha en la que se valido la cotizacion por el cliente
                $astilleroCotizacion->setRegistroValidaCliente(new \DateTimeImmutable());
                // Guardar quien acepto la cotizacion en el caso de un usuario de novo
                $astilleroCotizacion->setQuienAcepto($this->getUser()->getNombre());

                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_ACEPTAR,
                    'tipo' => Correo\Notificacion::TIPO_ASTILLERO
                ]);

                $this->enviaCorreoNotificacion($mailer, $notificables, $astilleroCotizacion);
            }

            $em->persist($astilleroCotizacion);
            $em->flush();

            return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
        }

        return $this->render('astillero/cotizacion/validar.html.twig', [
            'title' => 'Validación',
            'astilleroCotizacion' => $astilleroCotizacion,
            'edit_form' => $editForm->createView()
        ]);
    }

    /**
     * Muestra una cotizacion para recotizar
     *
     * @Route("/{id}/recotizar", name="astillero_recotizar")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param AstilleroCotizacion $astilleroCotizacionAnterior
     * @return RedirectResponse|Response
     */
    public function recotizaAction(Request $request, AstilleroCotizacion $astilleroCotizacionAnterior)
    {
        $this->denyAccessUnlessGranted('ASTILLERO_COTIZACION_REQUOTE', $astilleroCotizacionAnterior);

        if ($astilleroCotizacionAnterior->getEstatus() == 0 ||
            $astilleroCotizacionAnterior->getValidacliente() == 2 ||
            $astilleroCotizacionAnterior->getValidanovo() == 0 ||
            ($astilleroCotizacionAnterior->getValidanovo() == 2 && $astilleroCotizacionAnterior->getValidacliente() == 0)
        ) {
            throw new NotFoundHttpException();
        }
        $astilleroCotizacion = new AstilleroCotizacion();
        $barco = $astilleroCotizacionAnterior->getBarco();
        $cliente = $astilleroCotizacionAnterior->getCliente();
        $iva = $astilleroCotizacionAnterior->getIva();
        $dolar = $astilleroCotizacionAnterior->getDolar();
        $astilleroCotizacion
            ->setFechaLlegada($astilleroCotizacionAnterior->getFechaLlegada())
            ->setFechaSalida($astilleroCotizacionAnterior->getFechaSalida())
            ->setDiasEstadia($astilleroCotizacionAnterior->getDiasEstadia())
            ->setBarco($barco)
            ->setCliente($cliente)
            ->setTotal($astilleroCotizacionAnterior->getTotal())
            ->setIva($astilleroCotizacionAnterior->getIva())
            ->setDescuento($astilleroCotizacionAnterior->getDescuento())
            ->setSubtotal($astilleroCotizacionAnterior->getSubtotal())
            ->setDescuentototal($astilleroCotizacionAnterior->getDescuentototal())
            ->setIvatotal($astilleroCotizacionAnterior->getIvatotal())
            ->setFolio($astilleroCotizacionAnterior->getFolio());
        $astilleroCotizacion
            ->setDolar($dolar)
            ->setMensaje($astilleroCotizacionAnterior->getMensaje());
        $astilleroCotizacion
            ->setValidanovo(0);
        $astilleroCotizacion
            ->setValidacliente(0);
        $servicios = $astilleroCotizacionAnterior->getAcservicios();
        $astilleroGrua = new AstilleroCotizaServicio();
        $astilleroGrua = $this->llenarServicio($astilleroGrua, $servicios[0]);
        $astilleroRampa = new AstilleroCotizaServicio();
        $astilleroRampa = $this->llenarServicio($astilleroRampa, $servicios[2]);
        $astilleroKarcher = new AstilleroCotizaServicio();
        $astilleroKarcher = $this->llenarServicio($astilleroKarcher, $servicios[3]);
        $astilleroExplanada = new AstilleroCotizaServicio();
        $astilleroExplanada = $this->llenarServicio($astilleroExplanada, $servicios[4]);
        $astilleroLimpieza = new AstilleroCotizaServicio();
        $astilleroLimpieza = $this->llenarServicio($astilleroLimpieza, $servicios[6]);
        $astilleroInspeccionar = new AstilleroCotizaServicio();
        $astilleroInspeccionar = $this->llenarServicio($astilleroInspeccionar, $servicios[7]);
        $astilleroElectricidad = new AstilleroCotizaServicio();
        $astilleroElectricidad = $this->llenarServicio($astilleroElectricidad, $servicios[5]);
        $astilleroEstadia = new AstilleroCotizaServicio();
        $astilleroEstadia = $this->llenarServicio($astilleroEstadia, $servicios[1]);

        $astilleroCotizacion
            ->addAcservicio($astilleroGrua)
            ->addAcservicio($astilleroEstadia)
            ->addAcservicio($astilleroRampa)
            ->addAcservicio($astilleroKarcher)
            ->addAcservicio($astilleroExplanada)
            ->addAcservicio($astilleroElectricidad)
            ->addAcservicio($astilleroLimpieza)
            ->addAcservicio($astilleroInspeccionar)
            ;

        if(isset($servicios[8]) && $servicios[8]->getAstilleroServicioBasico()){
            $astilleroDiasAdicionales = new AstilleroCotizaServicio();
            $servicios[8]->setCantidad($servicios[8]->getCantidad()/$barco->getEslora());
            $astilleroDiasAdicionales = $this->llenarServicio($astilleroDiasAdicionales, $servicios[8]);
            $astilleroCotizacion->addAcservicio($astilleroDiasAdicionales);
        }
        foreach ($servicios as $servAst) {
            if ($servAst->getAstilleroserviciobasico() == null) {
                $copiaServicio = new AstilleroCotizaServicio();
                $copiaServicio
                    ->setOtroservicio($servAst->getOtroservicio())
                    ->setAstilleroserviciobasico($servAst->getAstilleroserviciobasico())
                    ->setProducto($servAst->getProducto())
                    ->setServicio($servAst->getServicio())
                    ->setPrecio($servAst->getPrecio())
                    ->setDivisa($servAst->getDivisa())
                    ->setEstatus($servAst->getEstatus())
                    ->setCantidad($servAst->getCantidad())
                    ->setSubtotal($servAst->getSubtotal())
                    ->setIva($servAst->getIva())
                    ->setTotal($servAst->getTotal())
                    ->setTipoCantidad($servAst->getTipoCantidad())
                    ->setPromedio($servAst->getPromedio())
                    ->setGrupo($servAst->getGrupo());
                $astilleroCotizacion->addAcservicio($copiaServicio);
            }
        }

        $form = $this->createForm(AstilleroCotizacionType::class, $astilleroCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $valordolar = $astilleroCotizacion->getDolar();
            $eslora = $astilleroCotizacion->getBarco()->getEslora();
            $cantidadDias = $astilleroCotizacion->getDiasEstadia();
            $sumas = ['granSubtotal' => 0, 'granIva' => 0, 'granTotal' => 0];
            // Uso de grua
            $cantidad = $eslora;
            $precio = $astilleroGrua->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroGrua, $cantidad, $precio, $iva, $sumas, $valordolar);
            // Estadía
            $cantidad = $cantidadDias * $eslora;
            $precio = $astilleroEstadia->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroEstadia, $cantidad, $precio, $iva, $sumas, $valordolar);
            // Uso de rampa
            $cantidad = $astilleroRampa->getCantidad();
            $precio = $astilleroRampa->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroRampa, $cantidad, $precio, $iva, $sumas, $valordolar);
            // Uso de karcher
            $cantidad = $astilleroKarcher->getCantidad();
            $precio = $astilleroKarcher->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroKarcher, $cantidad, $precio, $iva, $sumas, $valordolar);
            //uso de explanada
            $cantidad = $astilleroExplanada->getCantidad();
            $precio = $astilleroExplanada->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroExplanada, $cantidad, $precio, $iva, $sumas, $valordolar);
            //Conexión a electricidad
            $cantidad = $astilleroElectricidad->getCantidad();
            $precio = $astilleroElectricidad->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroElectricidad, $cantidad, $precio, $iva, $sumas, $valordolar);
            //Limpieza de locación
            $cantidad = $astilleroLimpieza->getCantidad();
            $precio = $astilleroLimpieza->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroLimpieza, $cantidad, $precio, $iva, $sumas, $valordolar);
            //Sacar para inspeccionar
            $cantidad = $eslora;
            $precio = $astilleroInspeccionar->getPrecio();
            $sumas = $this->guardarServicioBasicoRecotizado($astilleroInspeccionar, $cantidad, $precio, $iva, $sumas, $valordolar);
            if (isset($astilleroDiasAdicionales)) {
                $cantidad = $astilleroDiasAdicionales->getCantidad() * $eslora;
                $precio = $astilleroDiasAdicionales->getPrecio();
                $sumas = $this->guardarServicioBasicoRecotizado($astilleroDiasAdicionales, $cantidad, $precio, $iva, $sumas, $valordolar);
            }
            foreach ($astilleroCotizacion->getAcservicios() as $servAst) {
                if ($servAst->getAstilleroserviciobasico() == null) {
                    $cantidad = $servAst->getCantidad();

                    if ($servAst->getPrecio()) {
                        $precio = $servAst->getPrecio();
                        if ($servAst->getDivisa()) {
                            $divisa = $servAst->getDivisa();
                        } else {
                            $divisa = 'MXN';
                        }
                    } else {
                        if ($servAst->getServicio() != null) {
                            $divisa = $servAst->getServicio()->getDivisa();
                            $precio = $servAst->getServicio()->getPrecio();
                        } elseif ($servAst->getOtroservicio() != null) {
                            $divisa = 'MXN';
                            $precio = $servAst->getPrecio();
                        } elseif ($servAst->getProducto() != null) {
                            $divisa = 'MXN';
                            $precio = $servAst->getProducto()->getPrecio();
                        } else {
                            $divisa = 'MXN';
                            $precio = 0;
                        }
                    }
                    if ($divisa == 'USD') {
                        $subTotal = ($cantidad * $precio * $valordolar) / 100;
                    } else {
                        $subTotal = $cantidad * $precio;
                    }
                    $ivaTot = ($subTotal * $iva) / 100;
                    $total = $subTotal + $ivaTot;
                    $servAst->setPrecio($precio)
                        ->setSubtotal($subTotal)
                        ->setIva($ivaTot)
                        ->setTotal($total)
                        ->setDivisa($divisa)
                        ->setEstatus(true);
                    $sumas = ['granSubtotal' => $sumas['granSubtotal'] += $subTotal,
                        'granIva' => $sumas['granIva'] += $ivaTot,
                        'granTotal' => $sumas['granTotal'] += $total];

                }
            }
            $granDescuento = ($sumas['granSubtotal'] * $astilleroCotizacion->getDescuento()) / 100;
            $granIva = (($sumas['granSubtotal'] - $granDescuento) * $iva) / 100;
            $granTotal = $sumas['granSubtotal'] - $granDescuento + $granIva;
            //------------------------------------------------
            $fechaHoraActual = new \DateTime('now');

            $astilleroCotizacion
                ->setDolar($astilleroCotizacion->getDolar())
                ->setSubtotal($sumas['granSubtotal'])
                ->setDescuentototal($granDescuento)
                ->setIvatotal($granIva)
                ->setTotal($granTotal)
                ->setFecharegistro($fechaHoraActual)
                ->setEstatus(true);
            $astilleroCotizacionAnterior->setEstatus(false);
            $foliorecotizado = $astilleroCotizacionAnterior->getFoliorecotiza() + 1;
            $astilleroCotizacion->setFoliorecotiza($foliorecotizado);

            $guardarEditable = $form->get('guardareditable')->isClicked();
            //$guardarFinalizar = $form->get('guardarfinalizar')->isClicked();
            if (isset($guardarEditable) && $guardarEditable) {
                $astilleroCotizacion->setBorrador(true);
            } else {
                $astilleroCotizacion->setBorrador(false);
                // Asignarle la recotizacion a quien la creo
                $astilleroCotizacion->setCreador($this->getUser());
                // Remover el pincode usado para descuento
                $pincode = $em->getRepository(Pincode::class)
                    ->getOneValid($form->get('pincode')->getViewData());
                if($pincode) {
                    $em->remove($pincode);
                }
            }

            $em->persist($astilleroCotizacionAnterior);
            $em->persist($astilleroCotizacion);
            $em->flush();

            return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);

        }

        return $this->render('astillero/cotizacion/recotizar.html.twig', [
            'title' => 'Recotización',
            'idanterior' => $astilleroCotizacionAnterior->getId(),
            'astilleroCotizacion' => $astilleroCotizacion,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/pago", name="astillero_cotizacion_pago_edit")
     * @Method({"GET", "POST"})
     *
     * @Security("has_role('ROLE_ASTILLERO_PAGO')")
     *
     * @param Request $request
     * @param AstilleroCotizacion $astilleroCotizacion
     *
     * @return RedirectResponse|Response
     */
    public function editPagoAction(Request $request, AstilleroCotizacion $astilleroCotizacion)
    {
        $totPagado = 0;
        $totPagadoMonedero = 0;
        $listaPagos = new ArrayCollection();
        foreach ($astilleroCotizacion->getPagos() as $pago) {
            if ($pago->getDivisa() == 'USD') {
                $dolares = ($pago->getCantidad() / $pago->getDolar()) * 100;
                $pago->setCantidad($dolares);
            }
            $listaPagos->add($pago);
        }
        $folioCotizacion = $astilleroCotizacion->getFoliorecotiza()?$astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza():$astilleroCotizacion->getFolio();
        $form = $this->createForm('AppBundle\Form\AstilleroRegistraPagoType', $astilleroCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $total = $astilleroCotizacion->getTotal(); //en pesos
            $monedero = $astilleroCotizacion->getCliente()->getMonederoAstillero();
            $em = $this->getDoctrine()->getManager();
            $monederoDevuelto = 0;
            foreach ($listaPagos as $pago) {
                if (false === $astilleroCotizacion->getPagos()->contains($pago)) {
                    if($pago->getMetodopago() === 'Monedero'){
                        $monederoDevuelto+= $pago->getCantidad();
                        $monederoMovimiento = new MonederoMovimiento();
                        $monederoMovimiento
                            ->setCliente($astilleroCotizacion->getCliente())
                            ->setFecha(new \DateTime('now'))
                            ->setMonto($pago->getCantidad())
                            ->setOperacion(1)
                            ->setResultante($astilleroCotizacion->getCliente()->getMonederoAstillero() + $monederoDevuelto)
                            ->setTipo(2)
                            ->setDescripcion('Devolución de pago de cotización. Folio: '.$folioCotizacion);
                        $em->persist($monederoMovimiento);
                    }
                    $pago->getAcotizacion()->removePago($pago);
                    $em->persist($pago);
                    $em->remove($pago);
                }
            }
            foreach ($astilleroCotizacion->getPagos() as $pago) {
                if ($pago->getDivisa() == 'USD') {
                    $unpago = ($pago->getCantidad() * $pago->getDolar()) / 100;
                    $pago->setCantidad($unpago);
                } else {
                    $unpago = $pago->getCantidad();
                }
                $totPagado += $unpago; //guardando en pesos
                if ($pago->getMetodopago() == 'Monedero' && $pago->getId() == null) { //Si es un nuevo pago de monedero
                    $totPagadoMonedero += $unpago;
                    $monederotot = $monedero - $totPagadoMonedero;
                    $monederoMovimiento = new MonederoMovimiento();
                    $monederoMovimiento
                        ->setCliente($astilleroCotizacion->getCliente())
                        ->setFecha(new \DateTime('now'))
                        ->setMonto($unpago)
                        ->setOperacion(2)
                        ->setResultante($monederotot)
                        ->setTipo(2)
                        ->setDescripcion('Pago de servicios de astillero. Folio cotización: ' . $folioCotizacion);
                    $em->persist($monederoMovimiento);
                }
            }
            if (($total+1) < $totPagado) {
                $this->addFlash('notice', 'Error! Se ha intentado pagar más del total.');
            } else {
                if ($monedero < $totPagadoMonedero) {
                    $this->addFlash('notice', 'Error! Fondos insuficientes en el monedero.');
                }else {
                    $faltante = $total - $totPagado;
                    if($faltante < 1 && $faltante > -1){
                        $astilleroCotizacion->setRegistroPagoCompletado(new \DateTimeImmutable());
                        $astilleroCotizacion->setEstatuspago(2);
                    } else {
                        $astilleroCotizacion->setEstatuspago(1);
                    }
                    $monederoRestante = $monedero - $totPagadoMonedero;
                    $astilleroCotizacion->setPagado($totPagado);
                    $astilleroCotizacion->getCliente()->setMonederoAstillero($monederoRestante + $monederoDevuelto);
                    $em->persist($astilleroCotizacion);
                    $em->flush();
                    return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
                }
            }
        }
        return $this->render('astillero/cotizacion/pago/edit.html.twig', array(
            'title' => 'Registrar pagos',
            'astilleroCotizacion' => $astilleroCotizacion,
            'form' => $form->createView(),
            'folio' => $folioCotizacion
        ));
    }

    /**
     * @Route("/{id}/reenviar", name="astillero_reenviar")
     * @Method({"GET", "POST"})
     *
     * @param AstilleroCotizacion $astilleroCotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse
     */
    public function reenviaCoreoAction(AstilleroCotizacion $astilleroCotizacion, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();

        $folio = $astilleroCotizacion->getFoliorecotiza()
            ? $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza()
            : $astilleroCotizacion->getFolio();

        $attachment = new Swift_Attachment($this->displayMarinaPDFAction($astilleroCotizacion, 1),
            'cotizacionMXN_' . $folio . '.pdf',
            'application/pdf'
        );
        $attachmentMXN = new Swift_Attachment($this->displayMarinaPDFAction($astilleroCotizacion, 2),
            'cotizacionUSD_' . $folio . '.pdf',
            'application/pdf'
        );

        // Enviar correo de confirmacion
        $message = (new \Swift_Message('¡Cotizacion de servicios Astillero!'))
            ->setFrom('noresponder@novonautica.com')
            ->setTo($astilleroCotizacion->getBarco()->getCliente()->getCorreo())
            ->setBcc('admin@novonautica.com')
            ->setBody(
                $this->renderView('mail/cotizacion.html.twig', ['cotizacion' => $astilleroCotizacion]),
                'text/html'
            )
            ->attach($attachment)
            ->attach($attachmentMXN);

        if ($astilleroCotizacion->getBarco()->getCorreoCapitan()) {
            $message->addCc($astilleroCotizacion->getBarco()->getCorreoCapitan());
        }

        if ($astilleroCotizacion->getBarco()->getCorreoResponsable()) {
            $message->addCc($astilleroCotizacion->getBarco()->getCorreoResponsable());
        }

        $mailer->send($message);

        $historialCorreo = new Correo();
        $historialCorreo
            ->setFecha(new \DateTime('now'))
            ->setTipo('Cotización servicio Astillero')
            ->setDescripcion('Reenvio de cotización de Astillero')
            ->setFolioCotizacion($astilleroCotizacion->getFolio())
            ->setAcotizacion($astilleroCotizacion);

        $em->persist($historialCorreo);

        $em->flush();

        return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
    }

    /**
     * @Route ("/{id}/adicionales", name="astillero_cotizacion_adicionales")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param AstilleroCotizacion $astilleroCotizacion
     * @return Response
     */
    public function adicionalesAction(Request $request, AstilleroCotizacion $astilleroCotizacionAnterior){
        $astilleroCotizacion = new AstilleroCotizacion();
        $this->denyAccessUnlessGranted('ASTILLERO_COTIZACION_CREATE', $astilleroCotizacion);

        if($astilleroCotizacionAnterior->isEstatus() == 0 || $astilleroCotizacionAnterior->getBorrador()
           || $astilleroCotizacionAnterior->getValidacliente() == 0 || $astilleroCotizacionAnterior->getValidacliente() == 1 ){
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $sistema = $em->getRepository('AppBundle:ValorSistema')->find(1);
        $mensaje = $sistema->getMensajeCorreoAstillero();
        $barco = $astilleroCotizacionAnterior->getBarco();
        $cliente = $astilleroCotizacionAnterior->getCliente();
        $iva = $astilleroCotizacionAnterior->getIva();
        $dolar = $astilleroCotizacionAnterior->getDolar();
        // Días adicionales
        $qb = $em->createQueryBuilder();
        $queryBasico = $qb->select('sb')->from(astilleroServicioBasico::class,'sb')->getQuery();
        $preciosBasicos = $queryBasico->getArrayResult();

        //$servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(9);


        $astilleroGrua = new AstilleroCotizaServicio();
        $astilleroEstadia = new AstilleroCotizaServicio();
        $astilleroRampa = new AstilleroCotizaServicio();
        $astilleroKarcher = new AstilleroCotizaServicio();
        $astilleroExplanada = new AstilleroCotizaServicio();
        $astilleroElectricidad = new AstilleroCotizaServicio();
        $astilleroLimpieza = new AstilleroCotizaServicio();
        $astilleroInspeccionar = new AstilleroCotizaServicio();
        $astilleroDiasAdicionales = new AstilleroCotizaServicio();

//        $astilleroGrua->setPrecio($preciosBasicos[0]['precio']);
        $cantidad = $barco->getEslora();
        $precio = $preciosBasicos[0]['precio'];
        $divisa = 'MXN';
        $astilleroGrua = $this->calculaServicio($astilleroGrua,$cantidad,$precio,$iva,$divisa);
        $cantidad = 1;
        $precio = $preciosBasicos[2]['precio'];
        $divisa = 'MXN';
        $astilleroRampa = $this->calculaServicio($astilleroRampa,$cantidad,$precio,$iva,$divisa);
        $cantidad = 1;
        $precio = $preciosBasicos[3]['precio'];
        $divisa = 'MXN';
        $astilleroKarcher = $this->calculaServicio($astilleroKarcher,$cantidad,$precio,$iva,$divisa);
        $cantidad = 1;
        $precio = $preciosBasicos[4]['precio'];
        $divisa = 'MXN';
        $astilleroExplanada = $this->calculaServicio($astilleroExplanada,$cantidad,$precio,$iva,$divisa);
        $cantidad = 1;
        $precio = $preciosBasicos[5]['precio'];
        $divisa = 'MXN';
        $astilleroElectricidad = $this->calculaServicio($astilleroElectricidad,$cantidad,$precio,$iva,$divisa);
        $cantidad = 1;
        $precio = $preciosBasicos[6]['precio'];
        $divisa = 'MXN';
        $astilleroLimpieza = $this->calculaServicio($astilleroLimpieza,$cantidad,$precio,$iva,$divisa);
        $cantidad = $barco->getEslora();
        $precio = $preciosBasicos[7]['precio'];
        $divisa = 'MXN';
        $astilleroInspeccionar = $this->calculaServicio($astilleroInspeccionar,$cantidad,$precio,$iva,$divisa);
        $astilleroDiasAdicionales
            ->setPrecio($preciosBasicos[8]['precio'])
            ->setDivisa('USD');
        $astilleroCotizacion
            ->setFechaLlegada($astilleroCotizacionAnterior->getFechaLlegada())
            ->setFechaSalida($astilleroCotizacionAnterior->getFechaSalida())
            ->setDiasEstadia($astilleroCotizacionAnterior->getDiasEstadia())
            ->setBarco($barco)
            ->setCliente($cliente)
            ->setDolar($dolar)
            ->setIva($iva)
            ->setMensaje($mensaje)
            ->addAcservicio($astilleroGrua)
            ->addAcservicio($astilleroEstadia)
            ->addAcservicio($astilleroRampa)
            ->addAcservicio($astilleroKarcher)
            ->addAcservicio($astilleroExplanada)
            ->addAcservicio($astilleroElectricidad)
            ->addAcservicio($astilleroLimpieza)
            ->addAcservicio($astilleroInspeccionar)
            ->addAcservicio($astilleroDiasAdicionales);
        $form = $this->createForm('AppBundle\Form\AstilleroCotizacionType', $astilleroCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $valordolar = $astilleroCotizacion->getDolar();
            $eslora = $astilleroCotizacion->getBarco()->getEslora();
            $sumas = ['granSubtotal'=>0,'granIva'=>0,'granTotal'=>0];
            // Uso de grua
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(1);
            $cantidad = $eslora;
            $precio = $astilleroGrua->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroGrua,$servicio,$cantidad,$precio,$iva,$sumas,$valordolar);
            // Estadía
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(2);
            $cantidad=0;
            $precio=0;
            $sumas = $this->guardarServicioBasico($astilleroEstadia,$servicio,$cantidad,$precio,$iva,$sumas,$valordolar);
            // Uso de rampa
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(3);
            $cantidad = $astilleroRampa->getCantidad();
            $precio = $astilleroRampa->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroRampa,$servicio,$cantidad,$precio,$iva,$sumas,$valordolar);
            // Uso de karcher
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(4);
            $cantidad = $astilleroKarcher->getCantidad();
            $precio = $astilleroKarcher->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroKarcher,$servicio,$cantidad,$precio,$iva,$sumas,$valordolar);
            //uso de explanada
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(5);
            $cantidad = $astilleroExplanada->getCantidad();
            $precio = $astilleroExplanada->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroExplanada,$servicio,$cantidad,$precio,$iva,$sumas,$valordolar);
            //Conexión a electricidad
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(6);
            $cantidad = $astilleroElectricidad->getCantidad();
            $precio = $astilleroElectricidad->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroElectricidad,$servicio,$cantidad,$precio,$iva,$sumas,$valordolar);
            //Limpieza de locación
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(7);
            $cantidad = $astilleroLimpieza->getCantidad();
            $precio = $astilleroLimpieza->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroLimpieza,$servicio,$cantidad,$precio,$iva,$sumas,$valordolar);
            //Sacar para inspeccionar
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(8);
            $cantidad = $astilleroInspeccionar->getCantidad();
            $precio = $astilleroInspeccionar->getPrecio();
            $sumas = $this->guardarServicioBasico($astilleroInspeccionar,$servicio,$cantidad,$precio,$iva,$sumas,$valordolar);
            //dias adicionales
            $servicio = $this->getDoctrine()->getRepository(AstilleroServicioBasico::class)->find(9);
            $cantidad = $astilleroDiasAdicionales->getCantidad() * $eslora;
            $precio = $astilleroDiasAdicionales->getPrecio(); //esta en dolares
            $sumas = $this->guardarServicioBasico($astilleroDiasAdicionales,$servicio,$cantidad,$precio,$iva,$sumas,$valordolar);
            foreach ($astilleroCotizacion->getAcservicios() as $servAst) {
                if ($servAst->getAstilleroserviciobasico() == null) {
                    $cantidad = $servAst->getCantidad();
                    if ($servAst->getServicio() != null) {
                        $divisa = $servAst->getServicio()->getDivisa();
                        $precio = $servAst->getServicio()->getPrecio();
                    }elseif($servAst->getOtroservicio() != null){
                        $divisa = 'MXN';
                        $precio = $servAst->getPrecio();
                    }elseif($servAst->getProducto() != null){
                        $divisa = 'MXN';
                        $precio = $servAst->getProducto()->getPrecio();
                    }else{
                        $divisa = 'MXN';
                        $precio = 0;
                    }
                    if($divisa == 'USD'){
                        $subTotal = ($cantidad * $precio * $valordolar)/100;
                    }else{
                        $subTotal = $cantidad * $precio;
                    }
                    $ivaTot = ($subTotal * $iva) / 100;
                    $total = $subTotal + $ivaTot;
                    $servAst->setPrecio($precio)
                        ->setSubtotal($subTotal)
                        ->setIva($ivaTot)
                        ->setTotal($total)
                        ->setDivisa($divisa)
                        ->setEstatus(true);
                    $sumas = ['granSubtotal'=>$sumas['granSubtotal']+=$subTotal,
                        'granIva'=>$sumas['granIva']+=$ivaTot,
                        'granTotal'=>$sumas['granTotal']+=$total];
                }
            }
            $granDescuento = ($sumas['granSubtotal'] * $astilleroCotizacion->getDescuento())/100;
            $granIva = (($sumas['granSubtotal'] - $granDescuento) * $iva) / 100;
            $granTotal = $sumas['granSubtotal'] - $granDescuento + $granIva;
            //------------------------------------------------
            $fechaHoraActual = new \DateTime('now');
            $foliobase = $sistema->getFolioMarina();
            $folionuevo = $foliobase + 1;

            $astilleroCotizacion
                ->setDolar($valordolar)
                ->setIva($iva)
                ->setSubtotal($sumas['granSubtotal'])
                ->setDescuentototal($granDescuento)
                ->setIvatotal($granIva)
                ->setTotal($granTotal)
                ->setFecharegistro($fechaHoraActual)
                ->setBorrador(false)
                ->setEstatus(true);
            $astilleroCotizacion->setValidanovo(0);
            $astilleroCotizacion->setValidacliente(0);
            $astilleroCotizacion->setFolio($folionuevo);
            $astilleroCotizacion->setFoliorecotiza(0);
            $this->getDoctrine()
                ->getRepository(ValorSistema::class)
                ->find(1)
                ->setFolioMarina($folionuevo);
            // Asignacion de cotizacion al cliente y viceversa
            $cliente = $astilleroCotizacion->getBarco()->getCliente();
            $cliente->addAstilleroCotizacione($astilleroCotizacion);
            $astilleroCotizacion->setCliente($cliente);

            // Asignarle a la cotizacion, quien la creo (El usuario actualmente logueado)
            $astilleroCotizacion->setCreador($this->getUser());
            // Remover el pincode si existe un descuento
            $pincode = $em->getRepository(Pincode::class)
                ->getOneValid($form->get('pincode')->getViewData());
            if($pincode){
                $em->remove($pincode);
            }

            $em->persist($astilleroCotizacion);
            $em->flush();

            return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
        }
        return $this->render('astillero/cotizacion/adicionales.html.twig', [
            'title' => 'Astillero Adicionales',
            'idanterior' => $astilleroCotizacionAnterior->getId(),
            'astilleroCotizacion' => $astilleroCotizacion,
            'form' => $form->createView()
        ]);
    }

    /**
     * Elimina una cotizacion
     *
     * @Route("/{id}", name="astillero_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param AstilleroCotizacion $astilleroCotizacion
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, AstilleroCotizacion $astilleroCotizacion)
    {
        $this->denyAccessUnlessGranted('ASTILLERO_COTIZACION_DELETE', $astilleroCotizacion);
        $form = $this->createDeleteForm($astilleroCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($astilleroCotizacion->getBorrador() || $astilleroCotizacion->getValidanovo() == 0){
                $folioRecotiza = $astilleroCotizacion->getFoliorecotiza();
                if($folioRecotiza > 0){
                    $folioRecotizaPrincipal = $folioRecotiza-1;
                    $this->getDoctrine()
                        ->getRepository(AstilleroCotizacion::class)
                        ->findOneBy(['folio' => $astilleroCotizacion->getFolio(),'foliorecotiza' => $folioRecotizaPrincipal])
                        ->setEstatus(true);
                    //dump($folioRecotiza);
                }
                $em = $this->getDoctrine()->getManager();
                $em->remove($astilleroCotizacion);
                $em->flush();
            }
        }

        return $this->redirectToRoute('astillero_index');
    }

    /**
     * Creates a form to delete a astilleroCotizacion entity.
     *
     * @param AstilleroCotizacion $astilleroCotizacion The astilleroCotizacion entity
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(AstilleroCotizacion $astilleroCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('astillero_delete', ['id' => $astilleroCotizacion->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function calculaServicio($servicio, $cantidad, $precio, $iva, $divisa)
    {
        $subtotal = $cantidad * $precio;
        $ivatot = ($subtotal * $iva) / 100;
        $total = $subtotal + $ivatot;
        $servicio
            ->setCantidad($cantidad)
            ->setPrecio($precio)
            ->setSubtotal($subtotal)
            ->setIva($ivatot)
            ->setTotal($total)
            ->setDivisa($divisa);
        return $servicio;
    }


    private function llenarServicio($servicio,$datos){
        $servicio
            ->setServicio(null)
            ->setProducto(null)
            ->setOtroservicio(null)
            ->setAstilleroserviciobasico($datos->getAstilleroserviciobasico())
            ->setDivisa($datos->getDivisa());
        $servicio->setCantidad($datos->getCantidad());
        $servicio->setPrecio($datos->getPrecio());
        $servicio->setIva($datos->getIva());
        $servicio->setSubtotal($datos->getSubtotal());
        $servicio->setTotal($datos->getTotal());
        $servicio->setEstatus($datos->getEstatus());
        return $servicio;
    }

    /**
     * @param Correo\Notificacion[] $notificables
     * @param AstilleroCotizacion $cotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return void
     */
    private function enviaCorreoNotificacion($mailer, $notificables, $cotizacion)
    {
        if (!count($notificables)) {
            return;
        }

        $recipientes = [];
        foreach ($notificables as $key => $notificable) {
            $recipientes[$key] = $notificable->getCorreo();
        }

        $message = (new \Swift_Message('¡Cotizacion de servicios Astillero!'));
        $message->setFrom('noresponder@novonautica.com');
        $message->setTo($recipientes);

        $message->setBody(
            $this->renderView('mail/notificacion.html.twig', [
                'notificacion' => $notificables[0],
                'cotizacion' => $cotizacion
            ]),
            'text/html'
        );

        $mailer->send($message);
    }

    private function guardarServicioBasico($objeto, $servicio, $cantidad, $precio, $iva, $sumas, $dolar) {
        if($objeto->getDivisa()=='USD') {
            $subTotal = $cantidad * $precio * $dolar;
        } else {
            $subTotal = $cantidad * $precio;
        }

        /* Calcular los valores totales */
        $ivaTot = $subTotal * ($iva/100);
        $total = $subTotal + $ivaTot;

        $objeto
            ->setAstilleroserviciobasico($servicio)
            ->setServicio(null)
            ->setProducto(null)
            ->setOtroservicio(null)
            ->setPrecio($precio)
            ->setCantidad($cantidad)
            ->setIva($ivaTot)
            ->setSubtotal($subTotal)
            ->setTotal($total);
        if ($objeto->getEstatus()) {
            $sumas = ['granSubtotal'=>$sumas['granSubtotal']+=$subTotal,
                      'granIva'=>$sumas['granIva']+=$ivaTot,
                      'granTotal'=>$sumas['granTotal']+=$total];
        }
        return $sumas;
    }
    private function guardarServicioBasicoRecotizado($objeto,$cantidad,$precio,$iva,$sumas,$dolar){
        if($objeto->getDivisa()=='USD'){
            $subTotal = ($cantidad * $precio * $dolar)/100;
        }else{
            $subTotal = $cantidad * $precio;
        }
        $ivaTot = ($subTotal * $iva) / 100;
        $total = $subTotal + $ivaTot;
        $objeto
            ->setPrecio($precio)
            ->setCantidad($cantidad)
            ->setIva($ivaTot)
            ->setSubtotal($subTotal)
            ->setTotal($total);
        if ($objeto->getEstatus()) {
            $sumas = ['granSubtotal'=>$sumas['granSubtotal']+=$subTotal,
                      'granIva'=>$sumas['granIva']+=$ivaTot,
                      'granTotal'=>$sumas['granTotal']+=$total];
        }
        return $sumas;
    }
}
