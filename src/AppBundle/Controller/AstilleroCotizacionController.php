<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\CuentaBancaria;
use AppBundle\Entity\AstilleroCotizaServicio;
use AppBundle\Entity\AstilleroServicioBasico;
use AppBundle\Entity\Correo;
use AppBundle\Form\AstilleroCotizacionAceptadaType;
use AppBundle\Form\AstilleroCotizacionRechazadaType;
use AppBundle\Form\AstilleroCotizacionType;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use SensioLabs\Security\Exception\HttpException;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
//        if($request->isXmlHttpRequest()){
//            try{
//                $results = $dataTables->handle($request,'astillerocotizacion');
//                return $this->json($results);
//            } catch (HttpException $e){
//                return $this->json($e->getMessage(), $e->getCode());
//            }
//        }
        $em = $this->getDoctrine()->getManager();
        $astilleroCotizacion = $em->getRepository('AppBundle:AstilleroCotizacion')->findAll();
        return $this->render('astillero/cotizacion/index.html.twig', [
            'title' => 'Cotizaciones',
            'astilleroCotizacions' => $astilleroCotizacion
        ]);
    }
    /**
     * @Route("/gracias", name="astillero_gracias")
     * @Method("GET")
     */
    public function graciasAction()
    {
        return $this->render('marinahumeda/cotizacion/gracias.twig', [
        ]);
    }
    /**
     * @Route("/aceptaciones", name="astillero-aceptaciones")
     */
    public function displayAstilleroAceptaciones()
    {

        $em = $this->getDoctrine()->getManager();
        $miRepositorio = $em->getRepository('AppBundle:AstilleroCotizacion');
        $astilleroCotizacions = $miRepositorio->soloAceptados();

        return $this->render('astillero/cotizacion/index.html.twig', [
            'title' => 'Aceptaciones',
            'astilleroCotizacions' => $astilleroCotizacions,
        ]);
    }

    /**
     * @Route("/odt", name="astillero-odt")
     */
    public function displayAstilleroODT(Request $request)
    {
        return $this->render('astillero-odt.twig');
    }

    /**
     * Crea una nueva cotizacion de astillero
     *
     * @Route("/nueva", name="astillero_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request) //cantidades se guardan en pesos
    {
        $astilleroCotizacion = new AstilleroCotizacion();
        $astilleroGrua = new AstilleroCotizaServicio();
        $astilleroEstadia = new AstilleroCotizaServicio();
        $astilleroRampa = new AstilleroCotizaServicio();
        $astilleroKarcher = new AstilleroCotizaServicio();
        $astilleroExplanada = new AstilleroCotizaServicio();


        $astilleroCotizacion
            ->addAcservicio($astilleroGrua)
            ->addAcservicio($astilleroEstadia)
            ->addAcservicio($astilleroRampa)
            ->addAcservicio($astilleroKarcher)
            ->addAcservicio($astilleroExplanada)
          ;

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
        $sistema = $query->getArrayResult();
        $dolar = $sistema[0]['dolar'];
        $iva = $sistema[0]['iva'];
        $mensaje = $sistema[0]['mensajeCorreoAstillero'];
        $astilleroCotizacion->setDolar($dolar)->setMensaje($mensaje);
        $form = $this->createForm('AppBundle\Form\AstilleroCotizacionType', $astilleroCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $valordolar = $astilleroCotizacion->getDolar();
            $granSubtotal = 0;
            $granIva = 0;
            $granTotal = 0;
            $eslora = $astilleroCotizacion->getBarco()->getEslora();
            $llegada = $astilleroCotizacion->getFechaLlegada();
            $salida = $astilleroCotizacion->getFechaSalida();
            //$diferenciaDias = date_diff($llegada, $salida);
            //$cantidadDias = ($diferenciaDias->days);
            $cantidadDias = $astilleroCotizacion->getDiasEstadia();

            // Uso de grua
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(1);
            $cantidad = $eslora;
            $precio = ($astilleroGrua->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroGrua
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setAstilleroserviciobasico($servicio);
            $astilleroGrua->setPrecio($precio);
            $astilleroGrua->setCantidad($cantidad);
            $astilleroGrua->setIva($ivaTot);
            $astilleroGrua->setSubtotal($subTotal);
            $astilleroGrua->setTotal($total);
            $astilleroGrua->setEstatus(true);
            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granTotal += $total;

            // Estadía
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(2);
            $cantidad = $cantidadDias * $eslora;
            $precio = ($astilleroEstadia->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroEstadia
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null);
            $astilleroEstadia->setPrecio($precio);
            $astilleroEstadia->setCantidad($cantidad);
            $astilleroEstadia->setSubtotal($subTotal);
            $astilleroEstadia->setIva($ivaTot);
            $astilleroEstadia->setTotal($total);
            $astilleroEstadia->setEstatus(true);
            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granTotal += $total;

            // Uso de rampa
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(3);
            $cantidad = 1;
            $precio = ($astilleroRampa->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;

            $astilleroRampa
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null);
            $astilleroRampa->setPrecio($precio);
            $astilleroRampa->setCantidad($cantidad);
            $astilleroRampa->setSubtotal($subTotal);
            $astilleroRampa->setIva($ivaTot);
            $astilleroRampa->setTotal($total);
            if ($astilleroRampa->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            // Uso de karcher
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(4);
            $cantidad = 1;
            $precio = ($astilleroKarcher->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroKarcher
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null);
            $astilleroKarcher->setPrecio($precio);
            $astilleroKarcher->setCantidad($cantidad);
            $astilleroKarcher->setSubtotal($subTotal);
            $astilleroKarcher->setIva($ivaTot);
            $astilleroKarcher->setTotal($total);
            if ($astilleroKarcher->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            //uso de explanada
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(5);
            $cantidad = 1;
            $precio = ($astilleroExplanada->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroExplanada
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null);
            $astilleroExplanada->setPrecio($precio);
            $astilleroExplanada->setCantidad($cantidad);
            $astilleroExplanada->setSubtotal($subTotal);
            $astilleroExplanada->setIva($ivaTot);
            $astilleroExplanada->setTotal($total);
            if ($astilleroExplanada->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }


            foreach ($astilleroCotizacion->getAcservicios() as $servAst) {
                if ($servAst->getAstilleroserviciobasico() == null) {
                    $cantidad = $servAst->getCantidad();
                    if($servAst->getOtroservicio() != null){
                        $precio = $servAst->getPrecio();
                        $precio = ($precio/$valordolar)*100;
                    }elseif ($servAst->getProducto() != null){
                        $precio = $servAst->getProducto()->getPrecio();
                    }elseif ($servAst->getServicio()->getPrecio() != null){
                        $precio = $servAst->getServicio()->getPrecio();
                    }else{
                        $precio = 0;
                    }
                    $subTotal = $cantidad * $precio;
                    $ivaTot = ($subTotal * $iva) / 100;
                    $total = $subTotal + $ivaTot;
                    $servAst->setPrecio($precio);
                    $servAst->setSubtotal($subTotal);
                    $servAst->setIva($ivaTot);
                    $servAst->setTotal($total);
                    $servAst->setEstatus(true);

                    $granSubtotal += $subTotal;
                    $granIva += $ivaTot;
                    $granTotal += $total;
                }
            }

            //------------------------------------------------
            $fechaHoraActual = new \DateTime('now');
            $foliobase = $sistema[0]['folioMarina'];
            $folionuevo = $foliobase + 1;

            $astilleroCotizacion
                ->setDolar($astilleroCotizacion->getDolar())
                ->setIva($iva)
                ->setSubtotal($granSubtotal)
                ->setIvatotal($granIva)
                ->setTotal($granTotal)
                ->setFecharegistro($fechaHoraActual)
                ->setEstatus(true);
            $astilleroCotizacion->setValidanovo(0);
            $astilleroCotizacion->setValidacliente(0);
            $astilleroCotizacion->setFolio($folionuevo);
            $astilleroCotizacion->setFoliorecotiza(0);
            $folioactualiza = $this->getDoctrine()
                ->getRepository(ValorSistema::class)
                ->find(1)
                ->setFolioMarina($folionuevo);

            // Asignacion de cotizacion al cliente y viceversa
            $cliente = $astilleroCotizacion->getBarco()->getCliente();
            $cliente->addAstilleroCotizacione($astilleroCotizacion);
            $astilleroCotizacion->setCliente($cliente);

            $em->persist($astilleroCotizacion);
            $em->flush();

            return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
        }

        return $this->render('astillero/cotizacion/new.html.twig', [
            'title' => 'Nueva cotización',
            'astilleroCotizacion' => $astilleroCotizacion,
            'valdolar' => $dolar,
            'valiva' => $iva,
            'form' => $form->createView()
        ]);
    }

    /**
     * Muestra una cotizacion de astillero
     *
     * @Route("/{id}", name="astillero_show")
     * @Method("GET")
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
     *
     * @return PdfResponse
     */
    public function displayMarinaPDF(AstilleroCotizacion $ac,$tipo)
    {
        if($tipo == 1){ //dolares
            $html = $this->renderView('astillero/cotizacion/pdf/cotizacionpdf.html.twig', [
                'title' => 'Cotizacion-0.pdf',
                'astilleroCotizacion' => $ac
            ]);
        }else{ //pesos
            $html = $this->renderView('astillero/cotizacion/pdf/cotizacion-pesospdf.html.twig', [
                'title' => 'Cotizacion-0.pdf',
                'astilleroCotizacion' => $ac
            ]);
        }

        $header = $this->renderView('astillero/cotizacion/pdf/pdfencabezado.twig', [
            'astilleroCotizacion' => $ac
        ]);
        $footer = $this->renderView('astillero/cotizacion/pdf/pdfpie.twig', [
            'astilleroCotizacion' => $ac
        ]);
        $hojapdf = $this->get('knp_snappy.pdf');
        $options = [
            'margin-top' => 30,
            'margin-right' => 0,
            'margin-bottom' => 10,
            'margin-left' => 0,
            'header-html' => utf8_decode($header),
            'footer-html' => utf8_decode($footer)
        ];
        return new PdfResponse(
            $hojapdf->getOutputFromHtml($html, $options),
            'Cotizacion-'.$ac->getFolio().'-'.$ac->getFoliorecotiza().'.pdf', 'application/pdf', 'inline'
        );
    }
    /**
     * Confirma la respuesta de un cliente a una cotizacion
     *
     * @Route("/{token}/confirma", name="respuesta-cliente-astillero")
     * @Method({"GET", "POST"})
     *
     * @param $token
     *
     * @return Response
     */
    public function repuestaCliente(Request $request, $token)
    {

        $em = $this->getDoctrine()->getManager();
        $cotizacionAceptar = $em->getRepository(AstilleroCotizacion::class)
            ->findOneBy(['tokenacepta'=>$token]);

        if($cotizacionAceptar) {
            $cuentaBancaria = $em->getRepository(CuentaBancaria::class)
                ->findAll();
            $qb = $em->createQueryBuilder();
            $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
            $sistema =$query->getArrayResult();

            $diasHabiles = $sistema[0]['diasHabilesAstilleroCotizacion'];

            if($cotizacionAceptar->getFoliorecotiza()==0){
                $folio = $cotizacionAceptar->getFolio();
            }else{
                $folio = $cotizacionAceptar->getFolio().'-'.$cotizacionAceptar->getFoliorecotiza();
            }
            $valorSistema = new ValorSistema();
            $codigoSeguimiento = $folio.'-'.$valorSistema->generaToken(10);

            $cotizacionAceptar->setValidacliente(2);
            $cotizacionAceptar->setCodigoseguimiento($codigoSeguimiento);
            $em->persist($cotizacionAceptar);
            $em->flush();

            $mensaje1 = '¡Enhorabuena!';
            $mensaje2 = 'La cotización '.$folio.' ha sido aprobada.';
            $suformulario = 1;

            $editForm = $this->createForm(AstilleroCotizacionAceptadaType::class, $cotizacionAceptar);
            $editForm ->handleRequest($request);
            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $cotizacionAceptar->setFecharespuesta (new \DateTime('now'));
                $em->persist($cotizacionAceptar);
                $em->flush();
                return $this->redirectToRoute('astillero_gracias');
            }
            return $this->render('marinahumeda/cotizacion/respuesta-cliente.twig', [
                'mensaje1' => $mensaje1,
                'mensaje2' => $mensaje2,
                'suformulario' => $suformulario,
                'cuentaBancaria' => $cuentaBancaria,
                'diasHabiles' => $diasHabiles,
                'form' => $editForm->createView(),
                'marinaHumedaCotizacion' => $cotizacionAceptar
            ]);
        }
        else{
            $cotizacionRechazar = $em->getRepository(AstilleroCotizacion::class)
                ->findOneBy(['tokenrechaza'=>$token]);
            if($cotizacionRechazar){
                $cotizacionRechazar->setValidacliente(1);
                $em->persist($cotizacionRechazar);
                $em->flush();
                if($cotizacionRechazar->getFoliorecotiza()==0){
                    $folio = $cotizacionRechazar->getFolio();
                }else{
                    $folio = $cotizacionRechazar->getFolio().'-'.$cotizacionRechazar->getFoliorecotiza();
                }
                $mensaje1 = '¡Oh-oh!';
                $mensaje2 = 'La cotización '.$folio.' no ha sido aprobada.';
                $mensaje3 = 'Nos gustaría saber su opinión o comentarios del motivo de su rechazo.';
                $suformulario = 2;

                $editForm = $this->createForm(AstilleroCotizacionRechazadaType::class, $cotizacionRechazar);
                $editForm ->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $em->flush();
                    return $this->redirectToRoute('astillero_gracias');
                }

            }
            return $this->render('marinahumeda/cotizacion/respuesta-cliente.twig', [
                'mensaje1' => $mensaje1,
                'mensaje2' => $mensaje2,
                'mensaje3' => $mensaje3,
                'suformulario' => $suformulario,
                'form' => $editForm->createView()
            ]);
        }
    }

    /**
     * @Route("/{id}/pago", name="astillero_cotizacion_pago_edit")
     * @Method({"GET", "POST"})
     */
    public function editPagoAction(Request $request,AstilleroCotizacion $astilleroCotizacion)
    {
        $totPagado = 0;
        $listaPagos = new ArrayCollection();
        foreach ($astilleroCotizacion->getPagos() as $pago){
            $listaPagos->add($pago);
        }
        $form = $this->createForm('AppBundle\Form\AstilleroRegistraPagoType', $astilleroCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $total = $astilleroCotizacion->getTotal();
            $pagado = $astilleroCotizacion->getPagado();

            $em = $this->getDoctrine()->getManager();

            foreach ($listaPagos as $pago) {
                if (false === $astilleroCotizacion->getPagos()->contains($pago)) {
                    $pago->getAcotizacion()->removePago($pago);
                    $em->persist($pago);
                    $em->remove($pago);
                }
            }
            foreach ($astilleroCotizacion->getPagos() as $pago) {
                $totPagado+=$pago->getCantidad();
            }
            if($total < $totPagado) {
                $this->addFlash(
                    'notice',
                    'Error! Se ha intentado pagar más del total'
                );
            }else{
                $faltante = $total - $totPagado;
                if($faltante==0){
                    $astilleroCotizacion->setEstatuspago(2);
                }else{
                    $astilleroCotizacion->setEstatuspago(1);
                }
                $astilleroCotizacion
                    ->setPagado($totPagado);
                $em->persist($astilleroCotizacion);
                $em->flush();
                return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
            }

        }
        return $this->render('astillero/cotizacion/pago/edit.html.twig', array(
            'title' => 'Registrar pagos',
            'astilleroCotizacion' => $astilleroCotizacion,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/reenviar", name="astillero_reenviar")
     * @Method({"GET", "POST"})
     **/
    public function reenviaCoreoAction(Request $request, AstilleroCotizacion $astilleroCotizacion,\Swift_Mailer $mailer){
        $em = $this->getDoctrine()->getManager();
        $tokenAcepta = $astilleroCotizacion->getTokenacepta();
        $tokenRechaza = $astilleroCotizacion->getTokenrechaza();

        $html = $this->renderView('astillero/cotizacion/pdf/cotizacionpdf.html.twig', [
            'title' => 'Cotizacion-'.$astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza().'.pdf',
            'astilleroCotizacion' => $astilleroCotizacion
        ]);
        $htmlMXN = $this->renderView('astillero/cotizacion/pdf/cotizacion-pesospdf.html.twig', [
            'title' => 'Cotizacion-0.pdf',
            'astilleroCotizacion' => $astilleroCotizacion
        ]);
        $header = $this->renderView('astillero/cotizacion/pdf/pdfencabezado.twig', [
            'astilleroCotizacion' => $astilleroCotizacion
        ]);
        $footer = $this->renderView('astillero/cotizacion/pdf/pdfpie.twig', [
            'astilleroCotizacion' => $astilleroCotizacion
        ]);
        $hojapdf = $this->get('knp_snappy.pdf');
        $options = [
            'margin-top' => 30,
            'margin-right' => 0,
            'margin-bottom' => 33,
            'margin-left' => 0,
            'header-html' => utf8_decode($header),
            'footer-html' => utf8_decode($footer)
        ];
        $pdfEnviar = new PdfResponse(
            $hojapdf->getOutputFromHtml($html, $options),
            'Cotizacion-'.$astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza().'.pdf', 'application/pdf', 'inline'
        );
        $pdfEnviarMXN = new PdfResponse(
            $hojapdf->getOutputFromHtml($htmlMXN, $options),
            'CotizacionMXN-'.$astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza().'.pdf', 'application/pdf', 'inline'
        );
        $attachment = new Swift_Attachment($pdfEnviar, 'CotizacionUSD-'.$astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza().'.pdf', 'application/pdf');
        $attachmentMXN = new Swift_Attachment($pdfEnviarMXN, 'CotizacionMXN-'.$astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza().'.pdf', 'application/pdf');

        // Enviar correo de confirmacion
        $message = (new \Swift_Message('¡Cotizacion de servicios Astillero!'))
            ->setFrom('noresponder@novonautica.com')
            ->setTo($astilleroCotizacion->getBarco()->getCliente()->getCorreo())
            ->setBcc('admin@novonautica.com')
            ->setCc([$astilleroCotizacion->getBarco()->getCorreoCapitan(),$astilleroCotizacion->getBarco()->getCorreoResponsable()])
            ->setBody(
                $this->renderView('astillero/cotizacion/correo-clientevalida.twig', [
                        'astilleroCotizacion' => $astilleroCotizacion,
                        'tokenAcepta' => $tokenAcepta,
                        'tokenRechaza' => $tokenRechaza
                    ]
                ),
                'text/html'
            )
            ->attach($attachment)
            ->attach($attachmentMXN);

        $mailer->send($message);

        $historialCorreo = new Correo();
        $historialCorreo
            ->setFecha(new \DateTime('now'))
            ->setTipo('Cotización servicio Astillero')
            ->setDescripcion('Reenvio de cotización de Astillero')
            ->setFolioCotizacion($astilleroCotizacion->getFolio())
            ->setAcotizacion($astilleroCotizacion)
        ;

        $em->persist($historialCorreo);

        $em->flush();

        return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
    }

    /**
     * Editar una cotizacion
     *
     * @Route("/{id}/edit", name="astillero_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, AstilleroCotizacion $astilleroCotizacion)
    {
        $deleteForm = $this->createDeleteForm($astilleroCotizacion);
        $editForm = $this->createForm('AppBundle\Form\AstilleroCotizacionType', $astilleroCotizacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('astillero_edit', ['id' => $astilleroCotizacion->getId()]);
        }

        return $this->render('astillero/cotizacion/edit.html.twig', [
            'title' => 'Editar cotización',
            'astilleroCotizacion' => $astilleroCotizacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }
    /**
     * Muestra una cotizacion para recotizar
     *
     * @Route("/{id}/recotizar", name="astillero_recotizar")
     * @Method({"GET", "POST"})
     */
    public function recotizaAction(Request $request, AstilleroCotizacion $astilleroCotizacionAnterior)
    {
        if ($astilleroCotizacionAnterior->getEstatus() == 0 ||
            $astilleroCotizacionAnterior->getValidacliente() ==2 ||
            $astilleroCotizacionAnterior->getValidanovo() == 0 ||
            ($astilleroCotizacionAnterior->getValidanovo() == 2 && $astilleroCotizacionAnterior->getValidacliente() ==0)
        ) {
            throw new NotFoundHttpException();
        }
        $astilleroCotizacion = new AstilleroCotizacion();


        $barco = $astilleroCotizacionAnterior->getBarco();
        $iva = $astilleroCotizacionAnterior->getIva();
        $dolar = $astilleroCotizacionAnterior->getDolar();
        $astilleroCotizacion
            ->setFechaLlegada($astilleroCotizacionAnterior->getFechaLlegada())
            ->setFechaSalida($astilleroCotizacionAnterior->getFechaSalida())
            ->setDiasEstadia($astilleroCotizacionAnterior->getDiasEstadia())
            ->setBarco($barco)
            ->setTotal($astilleroCotizacionAnterior->getTotal())
            ->setIva($astilleroCotizacionAnterior->getIva())
            ->setSubtotal($astilleroCotizacionAnterior->getSubtotal())
            ->setIvatotal($astilleroCotizacionAnterior->getIvatotal())
            ->setFolio($astilleroCotizacionAnterior->getFolio())
           ;
        $astilleroCotizacion
            ->setDolar($dolar)
            ->setMensaje($astilleroCotizacionAnterior->getMensaje())
            ;
        $astilleroCotizacion
            ->setValidanovo(0);
        $astilleroCotizacion
            ->setValidacliente(0);

        $servicios = $astilleroCotizacionAnterior->getAcservicios();

        $astilleroGrua = new AstilleroCotizaServicio();
        $astilleroGrua
            ->setServicio(null)
            ->setProducto(null)
            ->setOtroservicio(null)
            ->setAstilleroserviciobasico($servicios[0]->getAstilleroserviciobasico());
        $astilleroGrua->setCantidad($servicios[0]->getCantidad());
        $astilleroGrua->setPrecio(($servicios[0]->getPrecio()*$dolar)/100);
        $astilleroGrua->setIva(($servicios[0]->getIva()*$dolar)/100);
        $astilleroGrua->setSubtotal(($servicios[0]->getSubtotal()*$dolar)/100);
        $astilleroGrua->setTotal(($servicios[0]->getTotal()*$dolar)/100);
        $astilleroGrua->setEstatus($servicios[0]->getEstatus());
        $astilleroEstadia = new AstilleroCotizaServicio();
        $astilleroEstadia
            ->setServicio(null)
            ->setProducto(null)
            ->setOtroservicio(null)
            ->setAstilleroserviciobasico($servicios[1]->getAstilleroserviciobasico());
        $astilleroEstadia->setCantidad($servicios[1]->getCantidad());
        $astilleroEstadia->setPrecio(($servicios[1]->getPrecio()*$dolar)/100);
        $astilleroEstadia->setIva(($servicios[1]->getIva()*$dolar)/100);
        $astilleroEstadia->setSubtotal(($servicios[1]->getSubtotal()*$dolar)/100);
        $astilleroEstadia->setTotal(($servicios[1]->getTotal()*$dolar)/100);
        $astilleroEstadia->setEstatus($servicios[1]->getEstatus());
        $astilleroRampa = new AstilleroCotizaServicio();
        $astilleroRampa
            ->setServicio(null)
            ->setProducto(null)
            ->setOtroservicio(null)
            ->setAstilleroserviciobasico($servicios[2]->getAstilleroserviciobasico());
        $astilleroRampa->setCantidad($servicios[2]->getCantidad());
        $astilleroRampa->setPrecio(($servicios[2]->getPrecio()*$dolar)/100);
        $astilleroRampa->setIva(($servicios[2]->getIva()*$dolar)/100);
        $astilleroRampa->setSubtotal(($servicios[2]->getSubtotal()*$dolar)/100);
        $astilleroRampa->setTotal(($servicios[2]->getTotal()*$dolar)/100);
        $astilleroRampa->setEstatus($servicios[2]->getEstatus());
        $astilleroKarcher = new AstilleroCotizaServicio();
        $astilleroKarcher
            ->setServicio(null)
            ->setProducto(null)
            ->setOtroservicio(null)
            ->setAstilleroserviciobasico($servicios[3]->getAstilleroserviciobasico());
        $astilleroKarcher->setCantidad($servicios[3]->getCantidad());
        $astilleroKarcher->setPrecio(($servicios[3]->getPrecio()*$dolar)/100);
        $astilleroKarcher->setIva(($servicios[3]->getIva()*$dolar)/100);
        $astilleroKarcher->setSubtotal(($servicios[3]->getSubtotal()*$dolar)/100);
        $astilleroKarcher->setTotal(($servicios[3]->getTotal()*$dolar)/100);
        $astilleroKarcher->setEstatus($servicios[3]->getEstatus());
        $astilleroExplanada = new AstilleroCotizaServicio();
        $astilleroExplanada
            ->setServicio(null)
            ->setProducto(null)
            ->setOtroservicio(null)
            ->setAstilleroserviciobasico($servicios[4]->getAstilleroserviciobasico());
        $astilleroExplanada->setCantidad($servicios[4]->getCantidad());
        $astilleroExplanada->setPrecio(($servicios[4]->getPrecio()*$dolar)/100);
        $astilleroExplanada->setIva(($servicios[4]->getIva()*$dolar)/100);
        $astilleroExplanada->setSubtotal(($servicios[4]->getSubtotal()*$dolar)/100);
        $astilleroExplanada->setTotal(($servicios[4]->getTotal()*$dolar)/100);
        $astilleroExplanada->setEstatus($servicios[4]->getEstatus());

        $astilleroCotizacion
            ->addAcservicio($astilleroGrua)
            ->addAcservicio($astilleroEstadia)
            ->addAcservicio($astilleroRampa)
            ->addAcservicio($astilleroKarcher)
            ->addAcservicio($astilleroExplanada)
           ;
        foreach ($servicios as $servAst) {
            if ($servAst->getAstilleroserviciobasico() == null) {
                $copiaServicio = new AstilleroCotizaServicio();
                $copiaServicio
                    ->setOtroservicio($servAst->getOtroservicio())
                    ->setAstilleroserviciobasico($servAst->getAstilleroserviciobasico())
                    ->setProducto($servAst->getProducto())
                    ->setServicio($servAst->getServicio())
                    ->setPrecio(($servAst->getPrecio()*$dolar)/100);
                $copiaServicio->setCantidad($servAst->getCantidad());
                $copiaServicio->setSubtotal(($servAst->getSubtotal()*$dolar)/100);
                $copiaServicio->setIva(($servAst->getIva()*$dolar)/100);
                $copiaServicio->setTotal(($servAst->getTotal()*$dolar)/100);
                $astilleroCotizacion->addAcservicio($copiaServicio);

            }
        }
        $form = $this->createForm(AstilleroCotizacionType::class, $astilleroCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $valordolar = $astilleroCotizacion->getDolar();

            $granSubtotal = 0;
            $granIva = 0;
            $granTotal = 0;
            $eslora = $astilleroCotizacion->getBarco()->getEslora();
            $llegada = $astilleroCotizacion->getFechaLlegada();
            $salida = $astilleroCotizacion->getFechaSalida();
//            $diferenciaDias = date_diff($llegada, $salida);
//            $cantidadDias = ($diferenciaDias->days);
            $cantidadDias = $astilleroCotizacion->getDiasEstadia();

            // Uso de grua
            $cantidad=$eslora;
            $precio = ($astilleroGrua->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroGrua->setPrecio($precio);
            $astilleroGrua->setCantidad($cantidad);
            $astilleroGrua->setIva($ivaTot);
            $astilleroGrua->setSubtotal($subTotal);
            $astilleroGrua->setTotal($total);
            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granTotal += $total;

            // Estadía
            $cantidad = $cantidadDias * $eslora;
            $precio = ($astilleroEstadia->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroEstadia->setPrecio($precio);
            $astilleroEstadia->setCantidad($cantidad);
            $astilleroEstadia->setSubtotal($subTotal);
            $astilleroEstadia->setIva($ivaTot);
            $astilleroEstadia->setTotal($total);
            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granTotal += $total;

            // Uso de rampa
            $cantidad = 1;
            $precio = ($astilleroRampa->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroRampa->setPrecio($precio);
            $astilleroRampa->setCantidad($cantidad);
            $astilleroRampa->setSubtotal($subTotal);
            $astilleroRampa->setIva($ivaTot);
            $astilleroRampa->setTotal($total);
            if ($astilleroRampa->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            // Uso de karcher
            $cantidad = 1;
            $precio = ($astilleroKarcher->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroKarcher->setPrecio($precio);
            $astilleroKarcher->setCantidad($cantidad);
            $astilleroKarcher->setSubtotal($subTotal);
            $astilleroKarcher->setIva($ivaTot);
            $astilleroKarcher->setTotal($total);
            if ($astilleroKarcher->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            //uso de explanada
            $cantidad = 1;
            $precio = ($astilleroExplanada->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroExplanada->setPrecio($precio);
            $astilleroExplanada->setCantidad($cantidad);
            $astilleroExplanada->setSubtotal($subTotal);
            $astilleroExplanada->setIva($ivaTot);
            $astilleroExplanada->setTotal($total);
            if ($astilleroExplanada->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            foreach ($astilleroCotizacion->getAcservicios() as $servAst) {
                if ($servAst->getAstilleroserviciobasico() == null) {
                    $cantidad = $servAst->getCantidad();
                    if($servAst->getOtroservicio() != null){
                        $precio = $servAst->getPrecio();
                        $precio = ($precio/$valordolar)*100;
                    }elseif ($servAst->getProducto() != null){
                        $precio = $servAst->getProducto()->getPrecio();
                    }elseif ($servAst->getServicio()->getPrecio() != null){
                        $precio = $servAst->getServicio()->getPrecio();
                    }else{
                        $precio = 0;
                    }
                    $subTotal = $cantidad * $precio;
                    $ivaTot = ($subTotal * $iva) / 100;
                    $total = $subTotal + $ivaTot;
                    $servAst->setPrecio($precio);
                    $servAst->setSubtotal($subTotal);
                    $servAst->setIva($ivaTot);
                    $servAst->setTotal($total);
                    $servAst->setEstatus(true);

                    $granSubtotal += $subTotal;
                    $granIva += $ivaTot;
                    $granTotal += $total;
                }
            }
            //------------------------------------------------
            $fechaHoraActual = new \DateTime('now');
            $foliorecotizado = $astilleroCotizacionAnterior->getFoliorecotiza()+1;

            $astilleroCotizacion
                ->setDolar($astilleroCotizacion->getDolar())
                ->setSubtotal($granSubtotal)
                ->setIvatotal($granIva)
                ->setTotal($granTotal)
                ->setFecharegistro($fechaHoraActual)
                ->setEstatus(true);
            $astilleroCotizacion->setValidanovo(0);
            $astilleroCotizacion->setValidacliente(0);
            $astilleroCotizacion->setFoliorecotiza($foliorecotizado);

            $astilleroCotizacionAnterior->setEstatus(false);
            $em->persist($astilleroCotizacion);
            $em->persist($astilleroCotizacionAnterior);
            $em->flush();
            return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
        }

        return $this->render('astillero/cotizacion/recotizar.html.twig', [
            'title' => 'Recotización',
            'astilleroCotizacion' => $astilleroCotizacion,
            'form' => $form->createView()
        ]);
    }
    /**
     *
     * @Route("/{id}/validar", name="astillero_validar")
     * @Method({"GET", "POST"})
     **/
    public function validaAction(Request $request, AstilleroCotizacion $astilleroCotizacion, \Swift_Mailer $mailer)
    {
        if ($astilleroCotizacion->isEstatus() == 0 ||
            $astilleroCotizacion->getValidanovo() == 1 ||
            $astilleroCotizacion->getValidanovo() == 2
            //    $marinaHumedaCotizacion->getValidacliente() ==1 ||
            //    $marinaHumedaCotizacion->getValidacliente() ==2
        ) {
            throw new NotFoundHttpException();
        }
        $valorSistema = new ValorSistema();
        //$servicios = $marinaHumedaCotizacion->getMHCservicios();
        $editForm = $this->createForm('AppBundle\Form\AstilleroCotizacionValidarType', $astilleroCotizacion);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($astilleroCotizacion->getValidanovo() == 2) {
                $tokenAcepta = $valorSistema->generaToken(100);
                $tokenRechaza = $valorSistema->generaToken(100);
                $astilleroCotizacion->setTokenacepta($tokenAcepta);
                $astilleroCotizacion->setTokenrechaza($tokenRechaza);
                $astilleroCotizacion->setNombrevalidanovo($this->getUser()->getNombre());

                // creando pdf
                $html = $this->renderView('astillero/cotizacion/pdf/cotizacionpdf.html.twig', [
                    'title' => 'Cotizacion-0.pdf',
                    'astilleroCotizacion' => $astilleroCotizacion
                ]);
                $htmlMXN = $this->renderView('astillero/cotizacion/pdf/cotizacion-pesospdf.html.twig', [
                    'title' => 'Cotizacion-0.pdf',
                    'astilleroCotizacion' => $astilleroCotizacion
                ]);
                $header = $this->renderView('astillero/cotizacion/pdf/pdfencabezado.twig', [
                    'astilleroCotizacion' => $astilleroCotizacion
                ]);
                $footer = $this->renderView('astillero/cotizacion/pdf/pdfpie.twig', [
                    'astilleroCotizacion' => $astilleroCotizacion
                ]);
                $hojapdf = $this->get('knp_snappy.pdf');
                $options = [
                    'margin-top' => 30,
                    'margin-right' => 0,
                    'margin-bottom' => 33,
                    'margin-left' => 0,
                    'header-html' => utf8_decode($header),
                    'footer-html' => utf8_decode($footer)
                ];
                $pdfEnviar = new PdfResponse(
                    $hojapdf->getOutputFromHtml($html, $options),
                    'Cotizacion-'.$astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza().'.pdf', 'application/pdf', 'inline'
                );
                $pdfEnviarMXN = new PdfResponse(
                    $hojapdf->getOutputFromHtml($htmlMXN, $options),
                    'CotizacionMXN-'.$astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza().'.pdf', 'application/pdf', 'inline'
                );
                $attachment = new Swift_Attachment($pdfEnviar, 'CotizacionUSD-'.$astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza().'.pdf', 'application/pdf');
                $attachmentMXN = new Swift_Attachment($pdfEnviarMXN, 'CotizacionMXN-'.$astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza().'.pdf', 'application/pdf');

                // Enviar correo de confirmacion
                $message = (new \Swift_Message('¡Cotizacion de servicios Astillero!'))
                    ->setFrom('noresponder@novonautica.com')
                    ->setTo($astilleroCotizacion->getBarco()->getCliente()->getCorreo())
                    ->setBcc('admin@novonautica.com')
                    ->setCc([$astilleroCotizacion->getBarco()->getCorreoCapitan(),$astilleroCotizacion->getBarco()->getCorreoResponsable()])
                    ->setBody(
                        $this->renderView('astillero/cotizacion/correo-clientevalida.twig', [
                                'astilleroCotizacion' => $astilleroCotizacion,
                                'tokenAcepta' => $tokenAcepta,
                                'tokenRechaza' => $tokenRechaza
                            ]
                        ),
                        'text/html'
                    )
                    ->attach($attachment)
                    ->attach($attachmentMXN);

                $mailer->send($message);

                if($astilleroCotizacion->getFoliorecotiza() == 0){
                    $folio = $astilleroCotizacion->getFolio();
                    $tipoCorreo = 'Cotización servicio Astillero';
                }else{
                    $folio = $astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza();
                    $tipoCorreo = 'Recotización Servicio Astillero';
                }
                $historialCorreo = new Correo();
                $historialCorreo
                    ->setFecha(new \DateTime('now'))
                    ->setTipo($tipoCorreo)
                    ->setDescripcion('Envio de cotización de Astillero con folio: ' . $folio)
                    ->setFolioCotizacion($folio)
                    ->setAcotizacion($astilleroCotizacion)
                ;

                $em->persist($historialCorreo);
            }
            else{
                if($astilleroCotizacion->getValidanovo()==1){
                    $astilleroCotizacion->setNombrevalidanovo($this->getUser()->getNombre());
                }
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
     * Elimina una cotizacion
     *
     * @Route("/{id}", name="astillero_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AstilleroCotizacion $astilleroCotizacion)
    {
        $form = $this->createDeleteForm($astilleroCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($astilleroCotizacion);
            $em->flush();
        }

        return $this->redirectToRoute('astillero_index');
    }

    /**
     * Creates a form to delete a astilleroCotizacion entity.
     *
     * @param AstilleroCotizacion $astilleroCotizacion The astilleroCotizacion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AstilleroCotizacion $astilleroCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('astillero_delete', array('id' => $astilleroCotizacion->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
