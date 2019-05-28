<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Correo;
use AppBundle\Entity\CotizacionNota;
use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\MonederoMovimiento;
use AppBundle\Entity\ValorSistema;
use AppBundle\Form\CotizacionNotaType;
use AppBundle\Form\Marina\CotizacionMoratoriaType;
use AppBundle\Form\MarinaHumedaCotizacionType;
use AppBundle\Form\MarinaHumedaRegistraPagoType;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * Marinahumedacotizacion controller.
 *
 * @Route("/marina/cotizacion")
 */
class MarinaHumedaCotizacionController extends Controller
{
    /**
     * Enlista todas las cotizaciones estadias
     *
     * @Route("/estadia/", name="marina-humeda_estadia_index")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function indexEstadiaAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'cotizacionEstadia');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }
        return $this->render('marinahumeda/cotizacion/estadia/index.html.twig', [
            'title' => 'Cotizaciones de Estadias',
            'papelera' => false
        ]);
    }

    /**
     * Enlista todas las cotizaciones estadias
     *
     * @Route("/estadia/datatables-marina")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function indexEstadiaDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'cotizacionEstadia');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/estadia/papelera/", name="marina-humeda_estadia_papelera")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function papeleraEstadiaAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'cotizacionEstadiaPapelera');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getCode());
            }
        }
        return $this->render('marinahumeda/cotizacion/estadia/index.html.twig', [
            'title' => 'Cotizaciones Papelera de reciclaje',
            'papelera' => true
        ]);
    }

    /**
     * Crea una nueva cotizacion
     *
     * @Route("/estadia/nuevo", name="marina-humeda_estadia_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();

        // Bloquear acceso si no puede crear cotizaciones
        $this->denyAccessUnlessGranted('MARINA_COTIZACION_CREATE', $marinaHumedaCotizacion);

        $marinaDiasEstadia = new MarinaHumedaCotizaServicios();
        $marinaElectricidad = new MarinaHumedaCotizaServicios();

        $em = $this->getDoctrine()->getManager();

        $sistema = $em->getRepository('AppBundle:ValorSistema')->find(1);
        $dolarBase = $sistema->getDolar();
        $iva = $sistema->getIva();
        $mensaje = $sistema->getMensajeCorreoMarina();

        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($marinaDiasEstadia)
            ->addMarinaHumedaCotizaServicios($marinaElectricidad)
            ->setMensaje($mensaje);

        $form = $this->createForm(MarinaHumedaCotizacionType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($marinaDiasEstadia->getPrecio()) {
                $precioEstadia = $marinaDiasEstadia->getPrecio()->getCosto();
                $marinaDiasEstadia->setIsPrecioOtro(false);
            } else {
                $precioEstadia = $marinaDiasEstadia->getPrecioOtro();
                $marinaDiasEstadia->setIsPrecioOtro(true);
            }

            if ($marinaElectricidad->getPrecioAux()) {
                $precioElectricidad = $marinaElectricidad->getPrecioAux()->getCosto();
                $marinaElectricidad->setIsPrecioOtro(false);
            } else {
                $precioElectricidad = $marinaElectricidad->getPrecioOtro();
                $marinaElectricidad->setIsPrecioOtro(true);
            }

            /**
             * Fix: Lets the $precioElectricidad be zero.
             * Date: May 22th 2019.
             * By: Manuel Gutiérrez.
             */
            if (!$precioEstadia) {
                $this->addFlash('danger', 'Precio no seleccionado para días estadia');
            } elseif ($precioElectricidad === NULL || !is_numeric($precioElectricidad)) {
                $this->addFlash('danger', 'Precio no seleccionado para electricidad');
            } else {
                $granSubtotal = 0;
                $granIva = 0;
                $granDescuento = 0;
                $granTotal = 0;
                $descuentoEstadia = $marinaHumedaCotizacion->getDescuentoEstadia();
                $descuentoElectricidad = $marinaHumedaCotizacion->getDescuentoElectricidad();
                $eslora = $marinaHumedaCotizacion->getBarco()->getEslora();
                $cantidadDias = $marinaHumedaCotizacion->getDiasEstadia();

                // Added
                $cantidadDiasElectricidad = $marinaHumedaCotizacion->getDiasElectricidad();
                // Días Estadía
                $subTotal = $cantidadDias * $precioEstadia * $eslora;
                $descuentoTot = ($subTotal * $descuentoEstadia) / 100;
                $subTotal_descuento = $subTotal - $descuentoTot;
                $ivaTot = ($subTotal_descuento * $iva) / 100;
                $total = $subTotal_descuento + $ivaTot;

                $marinaDiasEstadia->setTipo(1);
                $marinaDiasEstadia->setEstatus(1);
                $marinaDiasEstadia->setCantidad($cantidadDias);
                $marinaDiasEstadia->setPrecio($precioEstadia);
                $marinaDiasEstadia->setSubtotal($subTotal);
                $marinaDiasEstadia->setDescuento($descuentoTot);
                $marinaDiasEstadia->setIva($ivaTot);
                $marinaDiasEstadia->setTotal($total);
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granDescuento += $descuentoTot;
                $granTotal += $total;

                // Conexión a electricidad
                // Add
                $subTotal = $cantidadDiasElectricidad * $precioElectricidad * $eslora;
                //$subTotal = $cantidadDias * $precioElectricidad * $eslora;
                $descuentoTot = ($subTotal * $descuentoElectricidad) / 100;
                $subTotal_descuento = $subTotal - $descuentoTot;
                $ivaTot = ($subTotal_descuento * $iva) / 100;
                $total = $subTotal_descuento + $ivaTot;

                $marinaElectricidad->setTipo(2);
                $marinaElectricidad->setEstatus(1);
                $marinaElectricidad->setCantidad($cantidadDiasElectricidad);
                // $marinaElectricidad->setCantidad($cantidadDias);
                $marinaElectricidad->setPrecio($precioElectricidad);
                $marinaElectricidad->setSubtotal($subTotal);
                $marinaElectricidad->setDescuento($descuentoTot);
                $marinaElectricidad->setIva($ivaTot);
                $marinaElectricidad->setTotal($total);

                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granDescuento += $descuentoTot;
                $granTotal += $total;

                //-------------------------------------------------

                $marinaHumedaCotizacion->setIva($iva);
                $marinaHumedaCotizacion->setSubtotal($granSubtotal);
                $marinaHumedaCotizacion->setIvatotal($granIva);
                $marinaHumedaCotizacion->setDescuentototal($granDescuento);
                $marinaHumedaCotizacion->setTotal($granTotal);
                $marinaHumedaCotizacion->setValidanovo(0);
                $marinaHumedaCotizacion->setValidacliente(0);
                $marinaHumedaCotizacion->setEstatus(1);
                $marinaHumedaCotizacion->setFecharegistro(new \DateTime());
                $marinaHumedaCotizacion->setFolio($sistema->getFolioMarina() + 1);
                $marinaHumedaCotizacion->setFoliorecotiza(0);
                $marinaHumedaCotizacion->setCreador($this->getUser());

                $this->getDoctrine()->getRepository(ValorSistema::class)->find(1)
                    ->setFolioMarina($sistema->getFolioMarina() + 1);

                $em->persist($marinaHumedaCotizacion);
                $em->flush();

                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_CREAR,
                    'tipo' => Correo\Notificacion::TIPO_MARINA
                ]);
                $this->enviaCorreoNotificacion($mailer, $notificables, $marinaHumedaCotizacion);

                return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
            }
        }

        return $this->render('marinahumeda/cotizacion/estadia/new.html.twig', [
            'title' => 'Nueva cotización',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'valdolar' => $dolarBase,
            'valiva' => $iva,
            'iduser' => $this->getUser()->getId(),
            'form' => $form->createView()
        ]);
    }

    /**
     * Muestra una cotizacion en base a su id
     *
     * @Route("/{id}", name="marina-humeda_show")
     * @Method("GET")
     *
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     *
     * @return Response
     */
    public function showAction(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $reciclarForm = $this->createReciclarForm($marinaHumedaCotizacion, $marinaHumedaCotizacion->isDeleted() ? 0 : 1);
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);
        return $this->render('marinahumeda/cotizacion/show.html.twig', [
            'title' => 'Cotización',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'reciclar_form' => $reciclarForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/validar", name="marina-humeda_validar")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function validaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion, \Swift_Mailer $mailer)
    {
        // Bloquear acceso si no pueden validar
        $this->denyAccessUnlessGranted('MARINA_COTIZACION_VALIDATE', $marinaHumedaCotizacion);

        if ($marinaHumedaCotizacion->getEstatus() == 0 ||
            $marinaHumedaCotizacion->getValidanovo() == 1 ||
            $marinaHumedaCotizacion->getValidacliente() == 1 ||
            $marinaHumedaCotizacion->getValidacliente() == 2 ||
            $marinaHumedaCotizacion->isDeleted()
        ) {
            throw new NotFoundHttpException();
        }

        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);

        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionType', $marinaHumedaCotizacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $marinaHumedaCotizacion->setNombrevalidanovo($this->getUser()->getNombre());
            $folio = $marinaHumedaCotizacion->getFoliorecotiza()
                ? $marinaHumedaCotizacion->getFolio() . '-' . $marinaHumedaCotizacion->getFoliorecotiza()
                : $marinaHumedaCotizacion->getFolio();

            // Se envia un correo si se solicito notificar al cliente
            if (
                $marinaHumedaCotizacion->getValidanovo() === 2 &&
                $marinaHumedaCotizacion->getValidacliente() !== 2
            ) {
                // Activa un token para que valide el cliente
                $token = $marinaHumedaCotizacion->getFolio() . bin2hex(random_bytes(16));
                $marinaHumedaCotizacion->setToken($token);

                if ($marinaHumedaCotizacion->isNotificarCliente()) {
                    $attachment = new Swift_Attachment(
                        $this->displayMarinaPDFAction($marinaHumedaCotizacion),
                        'Cotizacion-' . $folio . '.pdf',
                        'application/pdf'
                    );

                    // Enviar correo de confirmacion
                    $message = (new \Swift_Message('¡Cotizacion de servicios!'))
                        ->setFrom('noresponder@novonautica.com')
                        ->setTo($marinaHumedaCotizacion->getCliente()->getCorreo())
                        ->setBcc('admin@novonautica.com')
                        ->setBody(
                            $this->renderView(
                                ':mail:cotizacion.html.twig',
                                [
                                    'cotizacion' => $marinaHumedaCotizacion
                                ]
                            ),
                            'text/html'
                        )
                        ->attach($attachment);

                    if ($marinaHumedaCotizacion->getBarco()->getCorreoCapitan()) {
                        $message->addCc($marinaHumedaCotizacion->getBarco()->getCorreoCapitan());
                    }

                    if ($marinaHumedaCotizacion->getBarco()->getCorreoResponsable()) {
                        $message->addCc($marinaHumedaCotizacion->getBarco()->getCorreoResponsable());
                    }

                    $mailer->send($message);

                    $tipoCorreo = $marinaHumedaCotizacion->getFoliorecotiza() === 0 ? 'Cotización servicio Marina Humeda' : 'Recotización servicio Marina Humeda';

                    // Guardar correo en el log de correos
                    $historialCorreo = new Correo();

                    $historialCorreo
                        ->setFecha(new \DateTime())
                        ->setTipo($tipoCorreo)
                        ->setDescripcion('Envio de cotización con folio: ' . $folio)
                        ->setFolioCotizacion($folio)
                        ->setMhcotizacion($marinaHumedaCotizacion);

                    $em->persist($historialCorreo);
                }

                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_VALIDAR,
                    'tipo' => Correo\Notificacion::TIPO_MARINA
                ]);

                $this->enviaCorreoNotificacion($mailer, $notificables, $marinaHumedaCotizacion);

                // Guardar la fecha en la que se valido la cotización por novonautica y agrega fecha límite para
                // aceptación por el cliente
                $sistema = $em->getRepository('AppBundle:ValorSistema')->find(1);
                $diasMarina = $sistema->getDiasHabilesMarinaCotizacion();

                $marinaHumedaCotizacion
                    ->setRegistroValidaNovo(new \DateTimeImmutable())
                    ->setLimiteValidaCliente((new \DateTime('now'))->modify('+ ' . $diasMarina . ' day'));
            }

            if ($marinaHumedaCotizacion->getValidacliente() === 2) {
                // Guardar la fecha en la que se valido la cotizacion por el cliente
                $marinaHumedaCotizacion
                    ->setRegistroValidaCliente(new \DateTimeImmutable());

                // Quien valido por el cliente
                $marinaHumedaCotizacion->setQuienAcepto($this->getUser()->getNombre());

                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_ACEPTAR,
                    'tipo' => Correo\Notificacion::TIPO_MARINA
                ]);

                $this->enviaCorreoNotificacion($mailer, $notificables, $marinaHumedaCotizacion);
            }

            $em->persist($marinaHumedaCotizacion);
            $em->flush();

            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }

        return $this->render('marinahumeda/cotizacion/validar.html.twig', [
            'title' => 'Validación',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Genera el pdf de una cotizacion en base a su id
     *
     * @Route("/{id}/pdf", name="marina-pdf")
     * @Method("GET")
     *
     * @param MarinaHumedaCotizacion $mhc
     *
     * @return PdfResponse
     */
    public function displayMarinaPDFAction(MarinaHumedaCotizacion $mhc)
    {
        $em = $this->getDoctrine()->getManager();

        $valor = $em->getRepository('AppBundle:ValorSistema')->find(1);

        $html = $this->renderView(
            'marinahumeda/cotizacion/pdf/cotizacionpdf.html.twig',
            [
                'title' => 'Cotizacion-'.$mhc->getFolio().'.pdf',
                'marinaHumedaCotizacion' => $mhc,
                'valor' => $valor,
            ]
        );

        $header = $this->renderView(
            'marinahumeda/cotizacion/pdf/pdfencabezado.twig',
            [
                'marinaHumedaCotizacion' => $mhc,
            ]
        );

        $footer = $this->renderView(
            'marinahumeda/cotizacion/pdf/pdfpie.twig',
            [
                'marinaHumedaCotizacion' => $mhc,
                'valor' => $valor,
            ]
        );

        $hojapdf = $this->get('knp_snappy.pdf');

        $options = [
            'margin-top' => 23,
            'margin-right' => 0,
            'margin-bottom' => 33,
            'margin-left' => 0,
            'header-html' => utf8_decode($header),
            'footer-html' => utf8_decode($footer)
        ];

        return new PdfResponse(
            $hojapdf->getOutputFromHtml($html, $options),
            'Cotizacion-' . $mhc
                ->getFolio() . '-' . $mhc
                ->getFoliorecotiza() . '.pdf', 'application/pdf', 'inline'
        );
    }

    /**
     * @Route("/{id}/pago", name="marina_cotizacion_pago_edit")
     * @Method({"GET", "POST"})
     *
     * @Security("has_role('ROLE_MARINA_PAGO')")
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function editPagoAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        if ($marinaHumedaCotizacion->isDeleted()) {
            throw new NotFoundHttpException();
        }

        $totPagado = 0;
        $totPagadoMonedero = 0;
        $listaPagos = new ArrayCollection();

        // Conversion de pagos de la DB (USD) a la vista (MXN)
        foreach ($marinaHumedaCotizacion->getPagos() as $pago) {
            if ($pago->getDivisa() == 'MXN') {
                $pesos = ($pago->getCantidad() * $pago->getDolar()) / 100;
                $pago->setCantidad($pesos);
            }

            $listaPagos->add($pago);
        }

        $form = $this->createForm(MarinaHumedaRegistraPagoType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($marinaHumedaCotizacion->getFoliorecotiza()) {
                $folioCotizacion = $marinaHumedaCotizacion->getFolio() . '-' . $marinaHumedaCotizacion->getFoliorecotiza();
            } else {
                $folioCotizacion = $marinaHumedaCotizacion->getFolio();
            }

            $total = $marinaHumedaCotizacion->getTotal();
            $monedero = $marinaHumedaCotizacion->getCliente()->getMonederomarinahumeda();
            $em = $this->getDoctrine()->getManager();
            $monederoDevuelto = 0;

            foreach ($listaPagos as $pago) {

                if (false === $marinaHumedaCotizacion->getPagos()->contains($pago)) {

                    if ($pago->getMetodopago() === 'Monedero') {

                        $monederoDevuelto +=  $pago->getCantidad();

                        $notaMonedero = 'Devolución de pago de cotización. Folio: '.$folioCotizacion;
                        $fechaHoraActual = new \DateTime();
                        $monederoMovimiento = new MonederoMovimiento();

                        $monederoMovimiento
                            ->setCliente($marinaHumedaCotizacion->getCliente())
                            ->setFecha($fechaHoraActual)
                            ->setMonto($pago->getCantidad())
                            ->setOperacion(1)
                            ->setResultante($marinaHumedaCotizacion->getCliente()->getMonederomarinahumeda() + $monederoDevuelto)
                            ->setTipo(1)
                            ->setDescripcion($notaMonedero);

                        $em->persist($monederoMovimiento);
                    }

                    $pago->getMhcotizacion()->removePago($pago);
                    $em->persist($pago);
                    $em->remove($pago);
                }
            }

            // Conversion de la vista (MXN) a la DB (USD)
            foreach ($marinaHumedaCotizacion->getPagos() as $pago) {

                if ($pago->getDivisa() == 'MXN') {
                    $unpago = ($pago->getCantidad() / $pago->getDolar()) * 100;
                    $pago->setCantidad($unpago);
                } else {
                    $unpago = $pago->getCantidad();
                }

                $totPagado += $unpago;

                if ($pago->getMetodopago() == 'Monedero' && $pago->getId() == null) {
                    $totPagadoMonedero += $unpago;
                    $monederotot = $monedero - $totPagadoMonedero;

                    if (
                        $marinaHumedaCotizacion->getMHCservicios()->first()->getTipo() == 1 ||
                        $marinaHumedaCotizacion->getMHCservicios()->first()->getTipo() == 2
                    ) {
                        $notaMonedero = 'Pago de servicio de estadía y electricidad. Folio cotización: ' . $folioCotizacion;
                    } else {
                        $notaMonedero = 'Pago de servicio de gasolina. Folio cotización: ' . $folioCotizacion;
                    }

                    $fechaHoraActual = new \DateTime();

                    $monederoMovimiento = new MonederoMovimiento();
                    $monederoMovimiento
                        ->setCliente($marinaHumedaCotizacion->getCliente())
                        ->setFecha($fechaHoraActual)
                        ->setMonto($unpago)
                        ->setOperacion(2)
                        ->setResultante($monederotot)
                        ->setTipo(1)
                        ->setDescripcion($notaMonedero);

                    $em->persist($monederoMovimiento);
                }
            }

            if (($total + 1) < $totPagado) {
                $this->addFlash('notice', 'Error! Se ha intentado pagar más del total');
            } else {
                if ($monedero < $totPagadoMonedero) {
                    $this->addFlash('notice', 'Error! Fondos insuficientes en el monedero');
                } else {
                    $faltante = $total - $totPagado;

                    if ($faltante <= 0.5) {
                        $marinaHumedaCotizacion->setRegistroPagoCompletado(new \DateTimeImmutable());
                        $marinaHumedaCotizacion->setEstatuspago(2);
                    } else {
                        $marinaHumedaCotizacion->setEstatuspago(1);
                    }

                    $monederoRestante = $monedero - $totPagadoMonedero;
                    $marinaHumedaCotizacion->setPagado($totPagado);
                    $marinaHumedaCotizacion->getCliente()->setMonederomarinahumeda($monederoRestante + $monederoDevuelto);

                    $em->persist($marinaHumedaCotizacion);
                    $em->flush();

                    return $this->redirectToRoute(
                        'marina-humeda_show',
                        ['id' => $marinaHumedaCotizacion->getId()]
                    );
                }
            }
        }

        return $this->render(
            'marinahumeda/cotizacion/pago/edit.html.twig',
            [
                'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     *
     * @Route("/{id}/nota", name="marina-humeda_nota")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function agregaNotaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $em = $this->getDoctrine()->getManager();
        $cotizacionnota = new CotizacionNota();
        $marinaHumedaCotizacion->addCotizacionnota($cotizacionnota);
        $form = $this->createForm(CotizacionNotaType::class, $cotizacionnota);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fechaHoraActual = new \DateTimeImmutable();
            $cotizacionnota->setFechahoraregistro($fechaHoraActual);
            $em->persist($marinaHumedaCotizacion);
            $em->flush();
            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }
        return $this->render('marinahumeda/cotizacion/nota/new.html.twig', [
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @Route("/{id}/moratoria", name="marina-humeda_moratoria")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function agregaMoratoriaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $this->denyAccessUnlessGranted('MARINA_COTIZACION_MORATORIA', $marinaHumedaCotizacion);
        if ($marinaHumedaCotizacion->getValidacliente() !== 2 || $marinaHumedaCotizacion->isDeleted()) {
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();

        $totalAnterior = $marinaHumedaCotizacion->getSubtotal() + $marinaHumedaCotizacion->getIvatotal() - $marinaHumedaCotizacion->getDescuentototal();
        if ($marinaHumedaCotizacion->getPorcentajeMoratorio()) {
            $porcentajeMoratorio = $marinaHumedaCotizacion->getPorcentajeMoratorio();
            $totalMoratorio = $marinaHumedaCotizacion->getMoratoriaTotal();
        } else {
            $qb = $em->getRepository('AppBundle:ValorSistema')->findOneBy(['id' => 1]);
            $porcentajeMoratorio = $qb->getPorcentajeMoratorio();
            $totalMoratorio = ($porcentajeMoratorio * $totalAnterior) / 100;
        }
        $totalNuevo = $totalMoratorio + $totalAnterior;
        $marinaHumedaCotizacion->setPorcentajeMoratorio($porcentajeMoratorio);
        $form = $this->createForm(CotizacionMoratoriaType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalMoratorio = ($marinaHumedaCotizacion->getPorcentajeMoratorio() * $totalAnterior) / 100;
            $totalNuevo = $totalMoratorio + $totalAnterior;
            $marinaHumedaCotizacion
                ->setMoratoriaTotal($totalMoratorio)
                ->setTotal($totalNuevo);
            $em->persist($marinaHumedaCotizacion);
            $em->flush();
            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }
        return $this->render('marinahumeda/cotizacion/moratoria.html.twig', [
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
            'totalMoratorio' => $totalMoratorio,
            'totalAnterior' => $totalAnterior,
            'totalNuevo' => $totalNuevo
        ]);

    }

    /**
     *
     * @Route("/estadia/{id}/renovar", name="marina-humeda_estadia_renovar")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function renuevaEstadiaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior)
    {
        $this->denyAccessUnlessGranted('MARINA_COTIZACION_RENEW', $marinaHumedaCotizacionAnterior);

        if ($marinaHumedaCotizacionAnterior->getValidacliente() != 2 || $marinaHumedaCotizacionAnterior->isDeleted()) {
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('AppBundle:ValorSistema')->findOneBy(['id' => 1]);
        $dolar = $qb->getDolar();
        $iva = $qb->getIva();
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $cliente = $marinaHumedaCotizacionAnterior->getCliente();
        $barco = $marinaHumedaCotizacionAnterior->getBarco();

        $marinaHumedaCotizacion->setCliente($cliente);
        $marinaHumedaCotizacion->setBarco($barco);
        $marinaHumedaCotizacion->setFechaLlegada($marinaHumedaCotizacionAnterior->getFechaLlegada());
        $marinaHumedaCotizacion->setFechaSalida($marinaHumedaCotizacionAnterior->getFechaSalida());
        $marinaHumedaCotizacion->setSlip(null);
        $marinaHumedaCotizacion->setDescuentoEstadia($marinaHumedaCotizacionAnterior->getDescuentoEstadia());
        $marinaHumedaCotizacion->setDescuentoElectricidad($marinaHumedaCotizacionAnterior->getDescuentoElectricidad());
        $marinaHumedaCotizacion->setDolar($dolar);
        $marinaHumedaCotizacion->setIva($iva);
        $marinaHumedaCotizacion->setSubtotal($marinaHumedaCotizacionAnterior->getSubtotal());
        $marinaHumedaCotizacion->setIvatotal($marinaHumedaCotizacionAnterior->getIvatotal());
        $marinaHumedaCotizacion->setDescuentototal($marinaHumedaCotizacionAnterior->getDescuentototal());
        $marinaHumedaCotizacion->setTotal($marinaHumedaCotizacionAnterior->getTotal());
        $marinaHumedaCotizacion->setValidanovo(0);
        $marinaHumedaCotizacion->setValidacliente(0);
        $marinaHumedaCotizacion->setMensaje($marinaHumedaCotizacionAnterior->getMensaje());
        $marinaHumedaCotizacion->setDiasEstadia($marinaHumedaCotizacionAnterior->getDiasEstadia());
        $marinaHumedaCotizacion->setDiasElectricidad($marinaHumedaCotizacionAnterior->getDiasElectricidad());

        $servicios = $marinaHumedaCotizacionAnterior->getMHCservicios();
        $marinaDiasEstadia = new MarinaHumedaCotizaServicios();
        $marinaDiasEstadia->setTipo($servicios[0]->getTipo());
        $marinaDiasEstadia->setCantidad($servicios[0]->getCantidad());
        $marinaDiasEstadia->setSubtotal($servicios[0]->getSubtotal());
        $marinaDiasEstadia->setIva($servicios[0]->getIva());
        $marinaDiasEstadia->setDescuento($servicios[0]->getDescuento());
        $marinaDiasEstadia->setTotal($servicios[0]->getTotal());
        $marinaDiasEstadia->setEstatus($servicios[0]->getEstatus());
        $servicios[0]->getIsPrecioOtro() ?
            $marinaDiasEstadia->setPrecioOtro($servicios[0]->getPrecio()) :
            $marinaDiasEstadia->setPrecio($servicios[0]->getPrecio());

        $marinaElectricidad = new MarinaHumedaCotizaServicios();
        $marinaElectricidad->setTipo($servicios[1]->getTipo());
        $marinaElectricidad->setCantidad($servicios[1]->getCantidad());
        $marinaElectricidad->setSubtotal($servicios[1]->getSubtotal());
        $marinaElectricidad->setIva($servicios[1]->getIva());
        $marinaElectricidad->setDescuento($servicios[1]->getDescuento());
        $marinaElectricidad->setTotal($servicios[1]->getTotal());
        $marinaElectricidad->setEstatus($servicios[1]->getEstatus());
        $servicios[1]->getIsPrecioOtro() ?
            $marinaElectricidad->setPrecioOtro($servicios[1]->getPrecio()) :
            $marinaElectricidad->setPrecio($servicios[1]->getPrecio());

        $marinaHumedaCotizacion->addMarinaHumedaCotizaServicios($marinaDiasEstadia);
        $marinaHumedaCotizacion->addMarinaHumedaCotizaServicios($marinaElectricidad);

        $form = $this->createForm(MarinaHumedaCotizacionType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($marinaDiasEstadia->getPrecio()) {
                $precioEstadia = $marinaDiasEstadia->getPrecio()->getCosto();
                $marinaDiasEstadia->setIsPrecioOtro(false);
            } else {
                $precioEstadia = $marinaDiasEstadia->getPrecioOtro();
                $marinaDiasEstadia->setIsPrecioOtro(true);
            }
            if ($marinaElectricidad->getPrecioAux()) {
                $precioElectricidad = $marinaElectricidad->getPrecioAux()->getCosto();
                $marinaElectricidad->setIsPrecioOtro(false);
            } else {
                $precioElectricidad = $marinaElectricidad->getPrecioOtro();
                $marinaElectricidad->setIsPrecioOtro(true);
            }
            if (!$precioEstadia) {
                $this->addFlash('danger', 'Precio no seleccionado para días estadia');
            } elseif ($precioElectricidad === NULL || !is_numeric($precioElectricidad)) {
                $this->addFlash('danger', 'Precio no seleccionado para electricidad');
            } else {
                $granSubtotal = 0;
                $granIva = 0;
                $granDescuento = 0;
                $granTotal = 0;
                $descuentoEstadia = $marinaHumedaCotizacion->getDescuentoEstadia();
                $descuentoElectricidad = $marinaHumedaCotizacion->getDescuentoElectricidad();
                $eslora = $marinaHumedaCotizacion->getBarco()->getEslora();
                $cantidadDias = $marinaHumedaCotizacion->getDiasEstadia();
                // Added
                $cantidadDiasElectricidad = $marinaHumedaCotizacion->getDiasElectricidad();

                // Días Estadía
                $subTotal = $cantidadDias * $precioEstadia * $eslora;
                $descuentoTot = ($subTotal * $descuentoEstadia) / 100;
                $subTotal_descuento = $subTotal - $descuentoTot;
                $ivaTot = ($subTotal_descuento * $iva) / 100;
                $total = $subTotal_descuento + $ivaTot;

                $marinaDiasEstadia->setEstatus(1);
                $marinaDiasEstadia->setCantidad($cantidadDias);
                $marinaDiasEstadia->setPrecio($precioEstadia);
                $marinaDiasEstadia->setSubtotal($subTotal);
                $marinaDiasEstadia->setDescuento($descuentoTot);
                $marinaDiasEstadia->setIva($ivaTot);
                $marinaDiasEstadia->setTotal($total);
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granDescuento += $descuentoTot;
                $granTotal += $total;

                // Conexión a electricidad
                // Added
                $subTotal = $cantidadDiasElectricidad * $precioElectricidad * $eslora;
                //$subTotal = $cantidadDias * $precioElectricidad * $eslora;
                $descuentoTot = ($subTotal * $descuentoElectricidad) / 100;
                $subTotal_descuento = $subTotal - $descuentoTot;
                $ivaTot = ($subTotal_descuento * $iva) / 100;
                $total = $subTotal_descuento + $ivaTot;

                $marinaElectricidad->setEstatus(1);
                $marinaElectricidad->setCantidad($cantidadDiasElectricidad);
                // $marinaElectricidad->setCantidad($cantidadDias);
                $marinaElectricidad->setPrecio($precioElectricidad);
                $marinaElectricidad->setSubtotal($subTotal);
                $marinaElectricidad->setDescuento($descuentoTot);
                $marinaElectricidad->setIva($ivaTot);
                $marinaElectricidad->setTotal($total);
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granDescuento += $descuentoTot;
                $granTotal += $total;

                //-------------------------------------------------

                $marinaHumedaCotizacion->setCliente($cliente);
                $marinaHumedaCotizacion->setBarco($barco);
                $marinaHumedaCotizacion->setIva($iva);
                $marinaHumedaCotizacion->setSubtotal($granSubtotal);
                $marinaHumedaCotizacion->setIvatotal($granIva);
                $marinaHumedaCotizacion->setDescuentototal($granDescuento);
                $marinaHumedaCotizacion->setTotal($granTotal);
                $marinaHumedaCotizacion->setValidanovo(0);
                $marinaHumedaCotizacion->setValidacliente(0);
                $marinaHumedaCotizacion->setEstatus(1);
                $marinaHumedaCotizacion->setFecharegistro(new \DateTime());
                $marinaHumedaCotizacion->setFolio($qb->getFolioMarina() + 1);
                $marinaHumedaCotizacion->setFoliorecotiza(0);

                $this->getDoctrine()->getRepository(ValorSistema::class)->find(1)
                    ->setFolioMarina($qb->getFolioMarina() + 1);

                $em->persist($marinaHumedaCotizacion);
                $em->flush();

                return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
            }
        }
        return $this->render('marinahumeda/cotizacion/estadia/recotizar.html.twig', [
            'title' => 'Renovación',
            'idanterior' => $marinaHumedaCotizacionAnterior->getId(),
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'iduser' => $this->getUser()->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Muestra una cotizacion para recotizar
     *
     * @Route("/estadia/{id}/recotizar", name="marina-humeda_estadia_recotizar")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function recotizaEstadiaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior)
    {
        $this->denyAccessUnlessGranted('MARINA_COTIZACION_REQUOTE', $marinaHumedaCotizacionAnterior);

        if ($marinaHumedaCotizacionAnterior->getEstatus() == 0 ||
            $marinaHumedaCotizacionAnterior->getValidacliente() == 2 ||
            $marinaHumedaCotizacionAnterior->getValidanovo() == 0 ||
            ($marinaHumedaCotizacionAnterior->getValidanovo() == 2 && $marinaHumedaCotizacionAnterior->getValidacliente() == 0) ||
            $marinaHumedaCotizacionAnterior->isDeleted()
        ) {
            throw new NotFoundHttpException();
        }

        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $foliorecotizado = $marinaHumedaCotizacionAnterior->getFoliorecotiza() + 1;
        $cliente = $marinaHumedaCotizacionAnterior->getCliente();
        $barco = $marinaHumedaCotizacionAnterior->getBarco();

        $marinaHumedaCotizacion->setCreador($this->getUser());
        $marinaHumedaCotizacion->setCliente($cliente);
        $marinaHumedaCotizacion->setBarco($barco);
        $marinaHumedaCotizacion->setFechaLlegada($marinaHumedaCotizacionAnterior->getFechaLlegada());
        $marinaHumedaCotizacion->setFechaSalida($marinaHumedaCotizacionAnterior->getFechaSalida());
        $marinaHumedaCotizacion->setSlip(null);
        $marinaHumedaCotizacion->setDescuentoEstadia($marinaHumedaCotizacionAnterior->getDescuentoEstadia());
        $marinaHumedaCotizacion->setDescuentoElectricidad($marinaHumedaCotizacionAnterior->getDescuentoElectricidad());
        $marinaHumedaCotizacion->setDolar($marinaHumedaCotizacionAnterior->getDolar());
        $marinaHumedaCotizacion->setIva($marinaHumedaCotizacionAnterior->getIva());
        $marinaHumedaCotizacion->setSubtotal($marinaHumedaCotizacionAnterior->getSubtotal());
        $marinaHumedaCotizacion->setIvatotal($marinaHumedaCotizacionAnterior->getIvatotal());
        $marinaHumedaCotizacion->setDescuentototal($marinaHumedaCotizacionAnterior->getDescuentototal());
        $marinaHumedaCotizacion->setTotal($marinaHumedaCotizacionAnterior->getTotal());
        $marinaHumedaCotizacion->setValidanovo(0);
        $marinaHumedaCotizacion->setValidacliente(0);
        $marinaHumedaCotizacion->setFolio($marinaHumedaCotizacionAnterior->getFolio());
        $marinaHumedaCotizacion->setFoliorecotiza($foliorecotizado);
        $marinaHumedaCotizacion->setMensaje($marinaHumedaCotizacionAnterior->getMensaje());
        $marinaHumedaCotizacion->setDiasEstadia($marinaHumedaCotizacionAnterior->getDiasEstadia());
        $marinaHumedaCotizacion->setDiasElectricidad($marinaHumedaCotizacionAnterior->getDiasElectricidad());

        $servicios = $marinaHumedaCotizacionAnterior->getMHCservicios();

        $marinaDiasEstadia = new MarinaHumedaCotizaServicios();
        $marinaDiasEstadia->setTipo($servicios[0]->getTipo());
        $marinaDiasEstadia->setCantidad($servicios[0]->getCantidad());
        $marinaDiasEstadia->setSubtotal($servicios[0]->getSubtotal());
        $marinaDiasEstadia->setIva($servicios[0]->getIva());
        $marinaDiasEstadia->setDescuento($servicios[0]->getDescuento());
        $marinaDiasEstadia->setTotal($servicios[0]->getTotal());
        $marinaDiasEstadia->setEstatus($servicios[0]->getEstatus());
        $servicios[0]->getIsPrecioOtro() ?
            $marinaDiasEstadia->setPrecioOtro($servicios[0]->getPrecio()) :
            $marinaDiasEstadia->setPrecio($servicios[0]->getPrecio());


        $marinaElectricidad = new MarinaHumedaCotizaServicios();
        $marinaElectricidad->setTipo($servicios[1]->getTipo());
        $marinaElectricidad->setCantidad($servicios[1]->getCantidad());
        $marinaElectricidad->setPrecio($servicios[1]->getPrecio());
        $marinaElectricidad->setSubtotal($servicios[1]->getSubtotal());
        $marinaElectricidad->setIva($servicios[1]->getIva());
        $marinaElectricidad->setDescuento($servicios[1]->getDescuento());
        $marinaElectricidad->setTotal($servicios[1]->getTotal());
        $marinaElectricidad->setEstatus($servicios[1]->getEstatus());
        $servicios[1]->getIsPrecioOtro() ?
            $marinaElectricidad->setPrecioOtro($servicios[1]->getPrecio()) :
            $marinaElectricidad->setPrecio($servicios[1]->getPrecio());

        $marinaHumedaCotizacion->addMarinaHumedaCotizaServicios($marinaDiasEstadia);
        $marinaHumedaCotizacion->addMarinaHumedaCotizaServicios($marinaElectricidad);
        $dolar = $marinaHumedaCotizacionAnterior->getDolar();
        $iva = $marinaHumedaCotizacionAnterior->getIva();

        $form = $this->createForm(MarinaHumedaCotizacionType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($marinaDiasEstadia->getPrecio()) {
                $precioEstadia = $marinaDiasEstadia->getPrecio()->getCosto();
                $marinaDiasEstadia->setIsPrecioOtro(false);
            } else {
                $precioEstadia = $marinaDiasEstadia->getPrecioOtro();
                $marinaDiasEstadia->setIsPrecioOtro(true);
            }
            if ($marinaElectricidad->getPrecioAux()) {
                $precioElectricidad = $marinaElectricidad->getPrecioAux()->getCosto();
                $marinaElectricidad->setIsPrecioOtro(false);
            } else {
                $precioElectricidad = $marinaElectricidad->getPrecioOtro();
                $marinaElectricidad->setIsPrecioOtro(true);
            }

            if (!$precioEstadia) {
                $this->addFlash('danger', 'Precio no seleccionado para días estadia');
            } elseif ($precioElectricidad === NULL || !is_numeric($precioElectricidad)) {
                $this->addFlash('danger', 'Precio no seleccionado para electricidad');
            } else {
                $granSubtotal = 0;
                $granIva = 0;
                $granDescuento = 0;
                $granTotal = 0;
                $descuentoEstadia = $marinaHumedaCotizacion->getDescuentoEstadia();
                $descuentoElectricidad = $marinaHumedaCotizacion->getDescuentoElectricidad();
                $eslora = $marinaHumedaCotizacion->getBarco()->getEslora();
                $cantidadDias = $marinaHumedaCotizacion->getDiasEstadia();
                // Added
                $cantidadDiasElectricidad = $marinaHumedaCotizacion->getDiasElectricidad();

                // Días Estadía
                $subTotal = $cantidadDias * $precioEstadia * $eslora;
                $descuentoTot = ($subTotal * $descuentoEstadia) / 100;
                $subTotal_descuento = $subTotal - $descuentoTot;
                $ivaTot = ($subTotal_descuento * $iva) / 100;
                $total = $subTotal_descuento + $ivaTot;

                $marinaDiasEstadia->setEstatus(1);
                $marinaDiasEstadia->setCantidad($cantidadDias);
                $marinaDiasEstadia->setPrecio($precioEstadia);
                $marinaDiasEstadia->setSubtotal($subTotal);
                $marinaDiasEstadia->setDescuento($descuentoTot);
                $marinaDiasEstadia->setIva($ivaTot);
                $marinaDiasEstadia->setTotal($total);
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granDescuento += $descuentoTot;
                $granTotal += $total;

                // Conexión a electricidad
                // Add
                $subTotal = $cantidadDiasElectricidad * $precioElectricidad * $eslora;
                //$subTotal = $cantidadDias * $precioElectricidad * $eslora;
                $descuentoTot = ($subTotal * $descuentoElectricidad) / 100;
                $subTotal_descuento = $subTotal - $descuentoTot;
                $ivaTot = ($subTotal_descuento * $iva) / 100;
                $total = $subTotal_descuento + $ivaTot;

                $marinaElectricidad->setEstatus(1);
                $marinaElectricidad->setCantidad($cantidadDiasElectricidad);
                // $marinaElectricidad->setCantidad($cantidadDias);
                $marinaElectricidad->setPrecio($precioElectricidad);
                $marinaElectricidad->setSubtotal($subTotal);
                $marinaElectricidad->setDescuento($descuentoTot);
                $marinaElectricidad->setIva($ivaTot);
                $marinaElectricidad->setTotal($total);
                $granSubtotal += $subTotal;
                $granIva += $ivaTot;
                $granDescuento += $descuentoTot;
                $granTotal += $total;

                //-------------------------------------------------

                $marinaHumedaCotizacion->setCliente($cliente);
                $marinaHumedaCotizacion->setBarco($barco);
                $marinaHumedaCotizacion->setIva($iva);
                $marinaHumedaCotizacion->setSubtotal($granSubtotal);
                $marinaHumedaCotizacion->setIvatotal($granIva);
                $marinaHumedaCotizacion->setDescuentototal($granDescuento);
                $marinaHumedaCotizacion->setTotal($granTotal);
                $marinaHumedaCotizacion->setValidanovo(0);
                $marinaHumedaCotizacion->setValidacliente(0);
                $marinaHumedaCotizacion->setEstatus(1);
                $marinaHumedaCotizacion->setFecharegistro(new \DateTime());
                $marinaHumedaCotizacionAnterior->setEstatus(0);

                $em->persist($marinaHumedaCotizacion);
                $em->persist($marinaHumedaCotizacionAnterior);
                $em->flush();

                return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
            }
        }
        return $this->render('marinahumeda/cotizacion/estadia/recotizar.html.twig', [
            'title' => 'Recotización',
            'idanterior' => $marinaHumedaCotizacionAnterior->getId(),
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'iduser' => $this->getUser()->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/estadia/{id}/{borrar}", name="marina-humeda_estadia_reciclar")
     * @Method({"POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $mhc
     * @param $borrar
     *
     * @return RedirectResponse|Response
     */
    public function reciclarAction(Request $request, MarinaHumedaCotizacion $mhc, $borrar)
    {
        $form = $this->createReciclarForm($mhc, $borrar);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $mhc->setIsDeleted($borrar);
            $em->persist($mhc);
            $em->flush();
        }
        return $this->redirectToRoute($mhc->isDeleted() ? 'marina-humeda_estadia_papelera' : 'marina-humeda_estadia_index');
    }

    /**
     * @param MarinaHumedaCotizacion $mhc The marinaHumedaCotizacion entity
     * @param $borrar
     * @return Form|\Symfony\Component\Form\FormInterface
     */
    public function createReciclarForm(MarinaHumedaCotizacion $mhc, $borrar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda_estadia_reciclar', [
                'id' => $mhc->getId(),
                'borrar' => $borrar
            ]))
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * @Route("/{id}/reenviar", name="marina-humeda_reenviar")
     * @Method({"GET", "POST"})
     *
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    public function reenviaCoreoAction(MarinaHumedaCotizacion $marinaHumedaCotizacion, \Swift_Mailer $mailer)
    {
        if ($marinaHumedaCotizacion->isDeleted()) {
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();

        $folio = $marinaHumedaCotizacion->getFoliorecotiza()
            ? $marinaHumedaCotizacion->getFolio() . '-' . $marinaHumedaCotizacion->getFoliorecotiza()
            : $marinaHumedaCotizacion->getFolio();

        $attachment = new Swift_Attachment(
            $this->displayMarinaPDFAction($marinaHumedaCotizacion),
            'Cotizacion-' . $folio . '.pdf', 'application/pdf');

        // Enviar correo de confirmacion
        $message = (new \Swift_Message('¡Cotizacion de servicios!'))
            ->setFrom('noresponder@novonautica.com')
            ->setTo($marinaHumedaCotizacion->getCliente()->getCorreo())
            ->setBcc('admin@novonautica.com')
            ->setBody(
                $this->renderView('mail/cotizacion.html.twig', [
                    'cotizacion' => $marinaHumedaCotizacion,
                ]),
                'text/html'
            )
            ->attach($attachment);

        if ($marinaHumedaCotizacion->getBarco()->getCorreoCapitan()) {
            $message->addCc($marinaHumedaCotizacion->getBarco()->getCorreoCapitan());
        }
        if ($marinaHumedaCotizacion->getBarco()->getCorreoResponsable()) {
            $message->addCc($marinaHumedaCotizacion->getBarco()->getCorreoResponsable());
        }

        $mailer->send($message);

        $tipoCorreo = $marinaHumedaCotizacion->getFoliorecotiza() === 0
            ? $tipoCorreo = 'Cotización servicio Marina Humeda'
            : $tipoCorreo = 'Recotización servicio Marina Humeda';

        $historialCorreo = new Correo();
        $historialCorreo
            ->setFecha(new \DateTime())
            ->setTipo($tipoCorreo)
            ->setDescripcion('Reenvio de cotización con Folio: ' . $folio)
            ->setFolioCotizacion($folio)
            ->setMhcotizacion($marinaHumedaCotizacion);

        $em->persist($historialCorreo);
        $em->persist($marinaHumedaCotizacion);
        $em->flush();

        return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
    }

    /**
     * @Route("estadiacliente.json")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getClientesAction(Request $request)
    {
        $clientes = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion')->getAllClientes();
        return new Response($this->serializeEntities($clientes, $request->getRequestFormat()));
    }

    /**
     * @Route("estadiabarco.json")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getBarcosAction(Request $request)
    {
        $barcos = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion')->getAllBarcos();
        return new Response($this->serializeEntities($barcos, $request->getRequestFormat()));
    }

    /**
     * @Route("/comprueba-pincode.json", name="marina-humeda_ajax-pincode")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function compruebaPincodeAction(Request $request)
    {
        $pincode = $this->getDoctrine()->getRepository('AppBundle:Pincode')
            ->findOneBy(['pin' => $request->get('pincode')]);

        if(!$pincode){
            return $this->json('notfound');
        }

        if (//si es 1 entonces sigue vigente
            ($pincode->getExpiration()->diff(new \DateTime()))->invert
            &&
            $pincode->getStatus()
        ){
            // Actualizar pincode
            $em = $this->getDoctrine()->getManager();
            $pincode->setUsedAt(new \DateTime());
            $pincode->setUsedBy(
                $this->getDoctrine()
                    ->getRepository('AppBundle:Usuario')
                    ->find($request->get('iduser'))
            );
            $pincode->setStatus(false);
            $em->persist($pincode);
            $em->flush();

            return $this->json(true);
        }

        return $this->json(false);
    }

    /**
     * Deletes a marinaHumedaCotizacion entity.
     *
     * @Route("/{id}", name="marina-humeda_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $this->denyAccessUnlessGranted('MARINA_COTIZACION_DELETE', $marinaHumedaCotizacion);

        $form = $this->createDeleteForm($marinaHumedaCotizacion);
        $form->handleRequest($request);
        $tipo = $marinaHumedaCotizacion->getMHCservicios()->first()->getTipo();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($marinaHumedaCotizacion->getValidanovo() == 0) {
                $folioRecotiza = $marinaHumedaCotizacion->getFoliorecotiza();
                if ($folioRecotiza > 0) {
                    $folioRecotizaPrincipal = $folioRecotiza - 1;
                    $this->getDoctrine()
                        ->getRepository(MarinaHumedaCotizacion::class)
                        ->findOneBy(['folio' => $marinaHumedaCotizacion->getFolio(), 'foliorecotiza' => $folioRecotizaPrincipal])
                        ->setEstatus(true);
                }
                $em = $this->getDoctrine()->getManager();
                $em->remove($marinaHumedaCotizacion);
                $em->flush();
            }
        }

        if ($tipo == 1 || $tipo == 2) {
            return $this->redirectToRoute('marina-humeda_estadia_index');
        } else {
            if ($tipo == 3 || $tipo == 4 || $tipo == 5) {
                return $this->redirectToRoute('combustible_index');
            } else {
                return $this->redirectToRoute('inicio');
            }
        }
    }

    /**
     * Crea un formulario para eliminar una cotizacion
     *
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion The marinaHumedaCotizacion entity
     *
     * @return Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda_delete',
                ['id' => $marinaHumedaCotizacion->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function serializeEntities($entity, $format, $ignoredAttributes = [])
    {
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $normalizer->setIgnoredAttributes($ignoredAttributes);

        return $serializer->serialize($entity, $format);
    }

    /**
     * @param Correo\Notificacion[] $notificables
     * @param MarinaHumedaCotizacion $cotizacion
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

        $message = (new \Swift_Message('¡Cotizacion de Marina Humeda!'));
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


    private function compruebaUsoPincode($mhc, $estadiaOtroPrecio, $electricidadOtroPrecio, $pincode)
    {
        //si es 1 entonces sigue vigente
        $pincodeVigencia = $pincode ?
            (
                $pincode->getExpiration()->diff(new \DateTime())
            )
                ->invert
            &&
            $pincode->getStatus() :
            0;
        if (
            (
                (
                    $estadiaOtroPrecio ||
                    $electricidadOtroPrecio ||
                    $mhc->getDescuentoEstadia() ||
                    $mhc->getDescuentoElectricidad()
                )
                &&
                (
                    $pincode &&
                    $pincode->getStatus() &&
                    $pincodeVigencia
                )
            )
            ||
            (
                !$estadiaOtroPrecio &&
                !$electricidadOtroPrecio &&
                !$mhc->getDescuentoEstadia() &&
                !$mhc->getDescuentoElectricidad()
            )
        ) {
            return true;
        } else {
            return false;
        }

    }
}
