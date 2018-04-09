<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\AstilleroCotizaServicio;
use AppBundle\Entity\AstilleroServicioBasico;
use AppBundle\Entity\Correo;
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
        return $this->render('astillero/cotizacion/index.html.twig', ['title' => 'Cotizaciones']);
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

        $this->denyAccessUnlessGranted('ASTILLERO_COTIZACION_CREATE', $astilleroCotizacion);

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $queryBasico = $qb->select('sb')->from(astilleroServicioBasico::class, 'sb')->getQuery();
        $preciosBasicos = $queryBasico->getArrayResult();

        $sistema = $em->getRepository('AppBundle:ValorSistema')->find(1);
        $dolar = $sistema->getDolar();
        $iva = $sistema->getIva();

        $astilleroGrua = new AstilleroCotizaServicio();
        $astilleroEstadia = new AstilleroCotizaServicio();
        $astilleroRampa = new AstilleroCotizaServicio();
        $astilleroKarcher = new AstilleroCotizaServicio();
        $astilleroExplanada = new AstilleroCotizaServicio();
        $astilleroElectricidad = new AstilleroCotizaServicio();
        $astilleroLimpieza = new AstilleroCotizaServicio();
        $astilleroInspeccionar = new AstilleroCotizaServicio();

        $astilleroGrua->setPrecio($preciosBasicos[0]['precio']);
        $astilleroEstadia->setPrecio($preciosBasicos[1]['precio']);
        $cantidad = 1;
        $precio = $preciosBasicos[2]['precio'];
        $astilleroRampa = $this->calculaServicio($astilleroRampa, $cantidad, $precio, $iva);
        $cantidad = 1;
        $precio = $preciosBasicos[3]['precio'];
        $astilleroKarcher = $this->calculaServicio($astilleroKarcher, $cantidad, $precio, $iva);
        $cantidad = 1;
        $precio = $preciosBasicos[4]['precio'];
        $astilleroExplanada = $this->calculaServicio($astilleroExplanada, $cantidad, $precio, $iva);
        $astilleroElectricidad->setPrecio($preciosBasicos[5]['precio']);
        $cantidad = 1;
        $precio = $preciosBasicos[6]['precio'];
        $astilleroLimpieza = $this->calculaServicio($astilleroLimpieza, $cantidad, $precio, $iva);
        $cantidad = 1;
        $precio = $preciosBasicos[7]['precio'];
        $astilleroInspeccionar = $this->calculaServicio($astilleroInspeccionar, $cantidad, $precio, $iva);

        $astilleroCotizacion
            ->addAcservicio($astilleroGrua)
            ->addAcservicio($astilleroEstadia)
            ->addAcservicio($astilleroRampa)
            ->addAcservicio($astilleroKarcher)
            ->addAcservicio($astilleroExplanada)
            ->addAcservicio($astilleroElectricidad)
            ->addAcservicio($astilleroLimpieza)
            ->addAcservicio($astilleroInspeccionar);

        $mensaje = $sistema->getMensajeCorreoAstillero();
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
            $cantidadDias = $astilleroCotizacion->getDiasEstadia();

            // Uso de grua
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(1);
            $cantidad = $eslora;
            $precio = ($astilleroGrua->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroGrua
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setIva($ivaTot)
                ->setSubtotal($subTotal)
                ->setTotal($total);
            if ($astilleroGrua->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            // Estadía
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(2);
            $cantidad = $cantidadDias * $eslora;
            $precio = $astilleroEstadia->getPrecio(); //ya esta en dolares
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
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroEstadia->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            // Uso de rampa
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(3);
            $cantidad = $astilleroRampa->getCantidad();
            $precio = ($astilleroRampa->getPrecio() / $valordolar) * 100;
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
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroRampa->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            // Uso de karcher
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(4);
            $cantidad = $astilleroKarcher->getCantidad();
            $precio = ($astilleroKarcher->getPrecio() / $valordolar) * 100;
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
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroKarcher->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            //uso de explanada
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(5);
            $cantidad = $astilleroExplanada->getCantidad();
            $precio = ($astilleroExplanada->getPrecio() / $valordolar) * 100;
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
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroExplanada->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            //Conexión a electricidad
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(6);
            $cantidad = $cantidadDias;
            $precio = ($astilleroElectricidad->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroElectricidad
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroElectricidad->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            //Limpieza de locación
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(7);
            $cantidad = $astilleroLimpieza->getCantidad();
            $precio = ($astilleroLimpieza->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroLimpieza
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroLimpieza->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            //Sacar para inspeccionar
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(8);
            $cantidad = $astilleroInspeccionar->getCantidad();
            $precio = ($astilleroInspeccionar->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroInspeccionar
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroInspeccionar->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            foreach ($astilleroCotizacion->getAcservicios() as $servAst) {
                if ($servAst->getAstilleroserviciobasico() == null) {
                    $cantidad = $servAst->getCantidad();
                    if ($servAst->getOtroservicio() != null) {
                        $precio = $servAst->getPrecio();
                        $precio = ($precio / $valordolar) * 100;
                    } elseif ($servAst->getProducto() != null) {
                        $precio = $servAst->getProducto()->getPrecio();
                    } elseif ($servAst->getServicio()->getPrecio() != null) {
                        $precio = $servAst->getServicio()->getPrecio();
                    } else {
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
            $foliobase = $sistema->getFolioMarina();
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

            // Asignarle a la cotizacion, quien la creo (El usuario actualmente logueado)
            $astilleroCotizacion->setCreador($this->getUser());

            $em->persist($astilleroCotizacion);
            $em->flush();

            // Buscar correos a notificar
            $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                'evento' => Correo\Notificacion::EVENTO_CREAR,
                'tipo' => Correo\Notificacion::TIPO_ASTILLERO
            ]);

            $this->enviaCorreoNotificacion($mailer, $notificables, $astilleroCotizacion);

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
     * @Route("/cliente.json")
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
    public function displayMarinaPDF(AstilleroCotizacion $ac, $tipo)
    {
        if ($tipo == 1) { //dolares
            $html = $this->renderView('astillero/cotizacion/pdf/cotizacionpdf.html.twig', [
                'astilleroCotizacion' => $ac
            ]);
        } else { //pesos
            $html = $this->renderView('astillero/cotizacion/pdf/cotizacion-pesospdf.html.twig', [
                'astilleroCotizacion' => $ac
            ]);
        }

        $header = $this->renderView('astillero/cotizacion/pdf/pdfencabezado.twig', [
            'astilleroCotizacion' => $ac
        ]);

        $hojapdf = $this->get('knp_snappy.pdf');

        $options = [
            'margin-top' => 19,
            'margin-right' => 0,
            'margin-left' => 0,
            'header-html' => utf8_decode($header),
        ];

        return new PdfResponse(
            $hojapdf->getOutputFromHtml($html, $options),
            'Cotizacion-' . $ac->getFolio() . '-' . $ac->getFoliorecotiza() . '.pdf',
            'application/pdf',
            'inline'
        );
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
            $astilleroCotizacion->getValidacliente() == 2
        ) {
            throw new NotFoundHttpException();
        }

        $valorSistema = new ValorSistema();
        $editForm = $this->createForm('AppBundle\Form\AstilleroCotizacionValidarType', $astilleroCotizacion);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (is_null($astilleroCotizacion->getTokenacepta())) {
                if ($astilleroCotizacion->getValidanovo() == 2) {
                    // Si no existe token pero ya ha sido validada por novonautica
                    $tokenAcepta = $valorSistema->generaToken(100);
                    $tokenRechaza = $valorSistema->generaToken(100);
                    $astilleroCotizacion->setTokenacepta($tokenAcepta);
                    $astilleroCotizacion->setTokenrechaza($tokenRechaza);
                    $astilleroCotizacion->setNombrevalidanovo($this->getUser()->getNombre());

                    // Generacion de PDF
                    // Se envia un correo si se solicito notificar al cliente
                    if ($astilleroCotizacion->isNotificarCliente()) {
                        $html = $this->renderView('astillero/cotizacion/pdf/cotizacionpdf.html.twig', [
                            'title' => 'Cotizacion-' . $astilleroCotizacion->getFolio() . '.pdf',
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
                            'Cotizacion-' . $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf', 'inline'
                        );
                        $pdfEnviarMXN = new PdfResponse(
                            $hojapdf->getOutputFromHtml($htmlMXN, $options),
                            'CotizacionMXN-' . $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf', 'inline'
                        );
                        $attachment = new Swift_Attachment($pdfEnviar, 'CotizacionUSD-' . $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf');
                        $attachmentMXN = new Swift_Attachment($pdfEnviarMXN, 'CotizacionMXN-' . $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf');

                        // Enviar correo de confirmacion
                        $message = (new \Swift_Message('¡Cotizacion de servicios Astillero!'))
                            ->setFrom('noresponder@novonautica.com')
                            ->setTo($astilleroCotizacion->getBarco()->getCliente()->getCorreo())
                            ->setBcc('admin@novonautica.com')
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
                        if ($astilleroCotizacion->getBarco()->getCorreoCapitan()) {
                            $message->addCc($astilleroCotizacion->getBarco()->getCorreoCapitan());
                        }
                        if ($astilleroCotizacion->getBarco()->getCorreoResponsable()) {
                            $message->addCc($astilleroCotizacion->getBarco()->getCorreoResponsable());
                        }
                        $mailer->send($message);

                        if ($astilleroCotizacion->getFoliorecotiza() == 0) {
                            $folio = $astilleroCotizacion->getFolio();
                            $tipoCorreo = 'Cotización servicio Astillero';
                        } else {
                            $folio = $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza();
                            $tipoCorreo = 'Recotización Servicio Astillero';
                        }

                        // Guardar correo en el log de correos
                        $historialCorreo = new Correo();
                        $historialCorreo
                            ->setFecha(new \DateTime('now'))
                            ->setTipo($tipoCorreo)
                            ->setDescripcion('Envio de cotización de Astillero con folio: ' . $folio)
                            ->setFolioCotizacion($folio)
                            ->setAcotizacion($astilleroCotizacion);

                        $em->persist($historialCorreo);
                    }

                    // Buscar correos a notificar
                    $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                        'evento' => Correo\Notificacion::EVENTO_VALIDAR,
                        'tipo' => Correo\Notificacion::TIPO_ASTILLERO
                    ]);

                    $this->enviaCorreoNotificacion($mailer, $notificables, $astilleroCotizacion);

                } else {
                    if ($astilleroCotizacion->getValidanovo() == 1) {
                        $astilleroCotizacion->setNombrevalidanovo($this->getUser()->getNombre());
                    }
                }
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

            // Guardar la fecha en la que se valido la cotizacion por novonautica
            $astilleroCotizacion->setRegistroValidaNovo(new \DateTimeImmutable());

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
            ->setSubtotal($astilleroCotizacionAnterior->getSubtotal())
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
        $astilleroGrua = $this->llenarServicio($astilleroGrua, $servicios[0], $dolar);
        $astilleroRampa = new AstilleroCotizaServicio();
        $astilleroRampa = $this->llenarServicio($astilleroRampa, $servicios[2], $dolar);
        $astilleroKarcher = new AstilleroCotizaServicio();
        $astilleroKarcher = $this->llenarServicio($astilleroKarcher, $servicios[3], $dolar);
        $astilleroExplanada = new AstilleroCotizaServicio();
        $astilleroExplanada = $this->llenarServicio($astilleroExplanada, $servicios[4], $dolar);
        $astilleroLimpieza = new AstilleroCotizaServicio();
        $astilleroLimpieza = $this->llenarServicio($astilleroLimpieza, $servicios[6], $dolar);
        $astilleroInspeccionar = new AstilleroCotizaServicio();
        $astilleroInspeccionar = $this->llenarServicio($astilleroInspeccionar, $servicios[7], $dolar);
        $astilleroElectricidad = new AstilleroCotizaServicio();
        $astilleroElectricidad = $this->llenarServicio($astilleroElectricidad, $servicios[5], $dolar);
        $astilleroEstadia = new AstilleroCotizaServicio();
        $astilleroEstadia
            ->setServicio(null)
            ->setProducto(null)
            ->setOtroservicio(null)
            ->setAstilleroserviciobasico($servicios[1]->getAstilleroserviciobasico())
            ->setCantidad($servicios[1]->getCantidad())
            ->setPrecio(($servicios[1]->getPrecio()))
            ->setIva(($servicios[1]->getIva()))
            ->setSubtotal(($servicios[1]->getSubtotal()))
            ->setTotal(($servicios[1]->getTotal()))
            ->setEstatus($servicios[1]->getEstatus());

        $astilleroCotizacion
            ->addAcservicio($astilleroGrua)
            ->addAcservicio($astilleroEstadia)
            ->addAcservicio($astilleroRampa)
            ->addAcservicio($astilleroKarcher)
            ->addAcservicio($astilleroExplanada)
            ->addAcservicio($astilleroElectricidad)
            ->addAcservicio($astilleroLimpieza)
            ->addAcservicio($astilleroInspeccionar);
        foreach ($servicios as $servAst) {
            if ($servAst->getAstilleroserviciobasico() == null) {
                $copiaServicio = new AstilleroCotizaServicio();
                $copiaServicio
                    ->setOtroservicio($servAst->getOtroservicio())
                    ->setAstilleroserviciobasico($servAst->getAstilleroserviciobasico())
                    ->setProducto($servAst->getProducto())
                    ->setServicio($servAst->getServicio())
                    ->setPrecio(($servAst->getPrecio() * $dolar) / 100);
                $copiaServicio->setCantidad($servAst->getCantidad());
                $copiaServicio->setSubtotal(($servAst->getSubtotal() * $dolar) / 100);
                $copiaServicio->setIva(($servAst->getIva() * $dolar) / 100);
                $copiaServicio->setTotal(($servAst->getTotal() * $dolar) / 100);
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
            $cantidadDias = $astilleroCotizacion->getDiasEstadia();

            // Uso de grua
            $cantidad = $eslora;
            $precio = ($astilleroGrua->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroGrua
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setIva($ivaTot)
                ->setSubtotal($subTotal)
                ->setTotal($total);
            if ($astilleroGrua->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            // Estadía
            $cantidad = $cantidadDias * $eslora;
            $precio = ($astilleroEstadia->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroEstadia
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroEstadia->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            // Uso de rampa
            $cantidad = $astilleroRampa->getCantidad();
            $precio = ($astilleroRampa->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroRampa
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroRampa->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            // Uso de karcher
            $cantidad = $astilleroKarcher->getCantidad();
            $precio = ($astilleroKarcher->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroKarcher
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroKarcher->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            //uso de explanada
            $cantidad = $astilleroExplanada->getCantidad();
            $precio = ($astilleroExplanada->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroExplanada
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroExplanada->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            //Conexión a electricidad
            $cantidad = $cantidadDias;
            $precio = ($astilleroElectricidad->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroElectricidad
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroElectricidad->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            //Limpieza de locación
            $cantidad = $astilleroLimpieza->getCantidad();
            $precio = ($astilleroLimpieza->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroLimpieza
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroLimpieza->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            //Sacar para inspeccionar
            $cantidad = $astilleroInspeccionar->getCantidad();
            $precio = ($astilleroInspeccionar->getPrecio() / $valordolar) * 100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroInspeccionar
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroInspeccionar->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }

            foreach ($astilleroCotizacion->getAcservicios() as $servAst) {
                if ($servAst->getAstilleroserviciobasico() == null) {
                    $cantidad = $servAst->getCantidad();
                    if ($servAst->getOtroservicio() != null) {
                        $precio = $servAst->getPrecio();
                        $precio = ($precio / $valordolar) * 100;
                    } elseif ($servAst->getProducto() != null) {
                        $precio = $servAst->getProducto()->getPrecio();
                    } elseif ($servAst->getServicio()->getPrecio() != null) {
                        $precio = $servAst->getServicio()->getPrecio();
                    } else {
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
            $foliorecotizado = $astilleroCotizacionAnterior->getFoliorecotiza() + 1;

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
        $listaPagos = new ArrayCollection();
        foreach ($astilleroCotizacion->getPagos() as $pago) {
            if ($pago->getDivisa() == 'MXN') {
                $pesos = ($pago->getCantidad() * $pago->getDolar()) / 100;
                $pago->setCantidad($pesos);
            }
            $listaPagos->add($pago);
        }
        $form = $this->createForm('AppBundle\Form\AstilleroRegistraPagoType', $astilleroCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $total = $astilleroCotizacion->getTotal();
            //$pagado = $astilleroCotizacion->getPagado();

            $em = $this->getDoctrine()->getManager();

            foreach ($listaPagos as $pago) {
                if (false === $astilleroCotizacion->getPagos()->contains($pago)) {
                    $pago->getAcotizacion()->removePago($pago);
                    $em->persist($pago);
                    $em->remove($pago);
                }
            }
            foreach ($astilleroCotizacion->getPagos() as $pago) {
                if ($pago->getDivisa() == 'MXN') {
                    $unpago = ($pago->getCantidad() / $pago->getDolar()) * 100;
                    $pago->setCantidad($unpago);
                } else {
                    $unpago = $pago->getCantidad();
                }
                $totPagado += $unpago;
            }
            if ($total < $totPagado) {
                $this->addFlash(
                    'notice',
                    'Error! Se ha intentado pagar más del total'
                );
            } else {
                $faltante = $total - $totPagado;
                if($faltante < 1 && $faltante > -1){
                    $astilleroCotizacion->setEstatuspago(2);
                } else {
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
     *
     * @param AstilleroCotizacion $astilleroCotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse
     */
    public function reenviaCoreoAction(AstilleroCotizacion $astilleroCotizacion, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $tokenAcepta = $astilleroCotizacion->getTokenacepta();
        $tokenRechaza = $astilleroCotizacion->getTokenrechaza();

        $html = $this->renderView('astillero/cotizacion/pdf/cotizacionpdf.html.twig', [
            'title' => 'Cotizacion-' . $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza() . '.pdf',
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
            'Cotizacion-' . $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf', 'inline'
        );
        $pdfEnviarMXN = new PdfResponse(
            $hojapdf->getOutputFromHtml($htmlMXN, $options),
            'CotizacionMXN-' . $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf', 'inline'
        );
        $attachment = new Swift_Attachment($pdfEnviar, 'CotizacionUSD-' . $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf');
        $attachmentMXN = new Swift_Attachment($pdfEnviarMXN, 'CotizacionMXN-' . $astilleroCotizacion->getFolio() . '-' . $astilleroCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf');

        // Enviar correo de confirmacion
        $message = (new \Swift_Message('¡Cotizacion de servicios Astillero!'))
            ->setFrom('noresponder@novonautica.com')
            ->setTo($astilleroCotizacion->getBarco()->getCliente()->getCorreo())
            ->setBcc('admin@novonautica.com')
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

        if($astilleroCotizacionAnterior->isEstatus() == 0){
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

        $astilleroGrua->setPrecio($preciosBasicos[0]['precio']);
        $cantidad = 1;
        $precio = $preciosBasicos[2]['precio'];
        $astilleroRampa = $this->calculaServicio($astilleroRampa,$cantidad,$precio,$iva);
        $cantidad = 1;
        $precio = $preciosBasicos[3]['precio'];
        $astilleroKarcher = $this->calculaServicio($astilleroKarcher,$cantidad,$precio,$iva);
        $cantidad = 1;
        $precio = $preciosBasicos[4]['precio'];
        $astilleroExplanada = $this->calculaServicio($astilleroExplanada,$cantidad,$precio,$iva);
        $astilleroElectricidad->setPrecio($preciosBasicos[5]['precio']);
        $cantidad = 1;
        $precio = $preciosBasicos[6]['precio'];
        $astilleroLimpieza = $this->calculaServicio($astilleroLimpieza,$cantidad,$precio,$iva);
        $cantidad = 1;
        $precio = $preciosBasicos[7]['precio'];
        $astilleroInspeccionar = $this->calculaServicio($astilleroInspeccionar,$cantidad,$precio,$iva);
        $astilleroDiasAdicionales->setPrecio($preciosBasicos[8]['precio'])
        ;

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
            ->addAcservicio($astilleroDiasAdicionales)
        ;
        $form = $this->createForm('AppBundle\Form\AstilleroCotizacionType', $astilleroCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $valordolar = $astilleroCotizacion->getDolar();
            $granSubtotal = 0;
            $granIva = 0;
            $granTotal = 0;
            $eslora = $astilleroCotizacion->getBarco()->getEslora();
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
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setIva($ivaTot)
                ->setSubtotal($subTotal)
                ->setTotal($total);
            if ($astilleroGrua->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            // Estadía
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(2);
            $astilleroEstadia
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio(0)
                ->setCantidad(0)
                ->setSubtotal(0)
                ->setIva(0)
                ->setTotal(0);
            // Uso de rampa
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(3);
            $cantidad = $astilleroRampa->getCantidad();
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
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroRampa->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            // Uso de karcher
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(4);
            $cantidad = $astilleroKarcher->getCantidad();
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
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroKarcher->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            //uso de explanada
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(5);
            $cantidad = $astilleroExplanada->getCantidad();
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
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroExplanada->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            //Conexión a electricidad
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(6);
            $cantidad = $astilleroDiasAdicionales->getCantidad();
            $precio = ($astilleroElectricidad->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroElectricidad
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroElectricidad->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            //Limpieza de locación
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(7);
            $cantidad = $astilleroLimpieza->getCantidad();
            $precio = ($astilleroLimpieza->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroLimpieza
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroLimpieza->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            //Sacar para inspeccionar
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(8);
            $cantidad = $astilleroInspeccionar->getCantidad();
            $precio = ($astilleroInspeccionar->getPrecio()/$valordolar)*100;
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroInspeccionar
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroInspeccionar->getEstatus()) {
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granTotal += $total;
            }
            //dias adicionales
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(9);

            $cantidad = $astilleroDiasAdicionales->getCantidad() * $eslora;
            $precio = ($astilleroDiasAdicionales->getPrecio());
            if ($precio == null) {
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal + $ivaTot;
            $astilleroDiasAdicionales
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setPrecio($precio)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total);
            if ($astilleroDiasAdicionales->getEstatus()) {
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
            $foliobase = $sistema->getFolioMarina();
            $folionuevo = $foliobase + 1;

            $astilleroCotizacion
                ->setDolar($valordolar)
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
            $this->getDoctrine()
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
        return $this->render('astillero/cotizacion/adicionales.html.twig', [
            'title' => 'Astillero Adicionales',
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
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(AstilleroCotizacion $astilleroCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('astillero_delete', ['id' => $astilleroCotizacion->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function calculaServicio($servicio, $cantidad, $precio, $iva)
    {
        $subtotal = $cantidad * $precio;
        $ivatot = ($subtotal * $iva) / 100;
        $total = $subtotal + $ivatot;
        $servicio
            ->setCantidad($cantidad)
            ->setPrecio($precio)
            ->setSubtotal($subtotal)
            ->setIva($ivatot)
            ->setTotal($total);
        return $servicio;
    }


    private function llenarServicio($servicio, $datos, $dolar)
    {
        $servicio
            ->setServicio(null)
            ->setProducto(null)
            ->setOtroservicio(null)
            ->setAstilleroserviciobasico($datos->getAstilleroserviciobasico());
        $servicio->setCantidad($datos->getCantidad());
        $servicio->setPrecio(($datos->getPrecio() * $dolar) / 100);
        $servicio->setIva(($datos->getIva() * $dolar) / 100);
        $servicio->setSubtotal(($datos->getSubtotal() * $dolar) / 100);
        $servicio->setTotal(($datos->getTotal() * $dolar) / 100);
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
}
