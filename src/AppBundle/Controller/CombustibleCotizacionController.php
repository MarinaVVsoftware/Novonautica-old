<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 22/06/2018
 * Time: 04:03 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Correo;
use AppBundle\Entity\CotizacionNota;
use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\MonederoMovimiento;
use AppBundle\Entity\ValorSistema;
use AppBundle\Form\CotizacionNotaType;
use AppBundle\Form\MarinaHumedaCotizacionGasolinaType;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Gasolina cotizacion controller.
 *
 * @Route("/combustible")
 */
class CombustibleCotizacionController extends Controller
{
    /**
     * @Route("/", name="combustible_index")
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
                $results = $dataTables->handle($request, 'cotizacionCombustible');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }
        return $this->render('combustible/index.html.twig', ['title' => 'Cotizaciones de Combustible']);
    }

    /**
     * @Route("/nuevo", name="combustible_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        // Bloquear acceso si no puede crear cotizaciones
        $this->denyAccessUnlessGranted('COMBUSTIBLE_COTIZACION_CREATE', $marinaHumedaCotizacion);
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('AppBundle:ValorSistema')->findOneBy(['id' => 1]);
        $dolarBase = $qb->getDolar();
        $iva = $qb->getIva();
        $mensaje = $qb->getMensajeCorreoMarinaGasolina();
        $combustible = new MarinaHumedaCotizaServicios();
        $combustible
            ->setCantidad(0)
            ->setPrecio(0)
            ->setPrecioAux(0)
            ->setSubtotal(0)
            ->setIva(0)
            ->setTotal(0);
        $barcoid = $request->query->get('id');
        if ($barcoid !== null) {
            $solicitud = $em->getRepository('AppBundle:MarinaHumedaSolicitudGasolina')->find($barcoid);
            $cliente = $solicitud->getCliente();
            $barco = $solicitud->getIdbarco();
            $cantidadgasolina = $solicitud->getCantidadCombustible();
            $tipogasolina = $solicitud->getTipoCombustible();
            $combustible
                ->setTipo($tipogasolina)
                ->setCantidad($cantidadgasolina);
            $marinaHumedaCotizacion
                ->setBarco($barco)
                ->setCliente($cliente);
        }
        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($combustible)
            ->setDolar($dolarBase)
            ->setSubtotal(0)
            ->setIvatotal(0)
            ->setTotal(0)
            ->setMensaje($mensaje);
        $form = $this->createForm(MarinaHumedaCotizacionGasolinaType::class, $marinaHumedaCotizacion,[
            'attr' =>['class' => 'form-combustible']
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $foliobase = $qb->getFolioMarina();
            $folionuevo = $foliobase + 1;
            $combustible->setEstatus(1);
            $marinaHumedaCotizacion
                ->setIva($iva)
                ->setValidanovo(0)
                ->setValidacliente(0)
                ->setEstatus(1)
                ->setFecharegistro(new \DateTime())
                ->setFolio($folionuevo)
                ->setFoliorecotiza(0)
                ->setCreador($this->getUser());
            $this->getDoctrine()
                ->getRepository(ValorSistema::class)
                ->find(1)
                ->setFolioMarina($folionuevo);
            $em->persist($combustible);
            $em->persist($marinaHumedaCotizacion);
            $em->flush();

            //En caso de ser cotizacion creada a partir de solicitud de app cambiar estatus de usada
            if ($barcoid !== null) {
                $solicitud = $em->getRepository('AppBundle:MarinaHumedaSolicitudGasolina')->find($barcoid);
                $solicitud->setStatus(1);
            }
            // Buscar correos a notificar
            $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                'evento' => Correo\Notificacion::EVENTO_CREAR,
                'tipo' => Correo\Notificacion::TIPO_MARINA
            ]);
            $this->enviaCorreoNotificacion($mailer, $notificables, $marinaHumedaCotizacion);
            return $this->redirectToRoute('combustible_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }
        return $this->render('combustible/new.html.twig', [
            'title' => 'Nueva cotización Combustible',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'valdolar' => $dolarBase,
            'valiva' => $iva,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cliente.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getClientesAction(Request $request)
    {
        $clientes = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion')->getAllClientesCombustible();
        return new Response($this->serializeEntities($clientes, $request->getRequestFormat()));
    }

    /**
     * @Route("/barco.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getBarcosAction(Request $request)
    {
        $barcos = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion')->getAllBarcosCombustible();
        return new Response($this->serializeEntities($barcos, $request->getRequestFormat()));
    }

    /**
     * Muestra una cotizacion en base a su id
     *
     * @Route("/{id}", name="combustible_show")
     * @Method("GET")
     *
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     *
     * @return Response
     */
    public function showAction(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $folio = $marinaHumedaCotizacion->getFoliorecotiza()===0 ?
            $marinaHumedaCotizacion->getFolio() :
            $marinaHumedaCotizacion->getFolio().'-'.$marinaHumedaCotizacion->getFoliorecotiza();
        switch ($marinaHumedaCotizacion->getValidanovo()){
            case 0:
                $validacion = 'Pendiente validación de Novonautica';
                break;
            case 1:
                $validacion = 'Rechazado por '.$marinaHumedaCotizacion->getNombrevalidanovo();
                break;
            case 2:
                $validacion = 'Aprobado por '.$marinaHumedaCotizacion->getNombrevalidanovo();
                break;
            default: $validacion = '';
        }
        switch ($marinaHumedaCotizacion->getValidacliente()){
            case 0:
                $aceptacion = 'Pendiente aceptación del cliente';
                break;
            case 1:
                $aceptacion = 'Rechazado por el cliente';
                break;
            case 2:
                $aceptacion = 'Aprobado por el cliente';
                break;
            default: $aceptacion = '';
        }
        switch ($marinaHumedaCotizacion->getEstatuspago()){
            case 1:
                $pago = 'Con adeudo';
                break;
            case 2:
                $pago = 'Pagado';
                break;
            default: $pago = 'No pagado';
        }
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);
        return $this->render('combustible/show.html.twig', [
            'title' => 'Cotización Combustible',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'delete_form' => $deleteForm->createView(),
            'folio' => $folio,
            'validacion' => $validacion,
            'aceptacion' => $aceptacion,
            'pago' => $pago
        ]);
    }

    /**
     * @Route("/{id}/validar", name="combustible_validar")
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
        $this->denyAccessUnlessGranted('COMBUSTIBLE_COTIZACION_VALIDATE', $marinaHumedaCotizacion);
        if ($marinaHumedaCotizacion->getEstatus() == 0 ||
            $marinaHumedaCotizacion->getValidanovo() == 1 ||
            $marinaHumedaCotizacion->getValidacliente() == 1 ||
            $marinaHumedaCotizacion->getValidacliente() == 2
        ) {
            throw new NotFoundHttpException();
        }
        $folio = $marinaHumedaCotizacion->getFoliorecotiza()
            ? $marinaHumedaCotizacion->getFolio() . '-' . $marinaHumedaCotizacion->getFoliorecotiza()
            : $marinaHumedaCotizacion->getFolio();
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);
        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionType', $marinaHumedaCotizacion);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $marinaHumedaCotizacion->setNombrevalidanovo($this->getUser()->getNombre());
            if ($marinaHumedaCotizacion->getValidanovo() === 2) {
                // Activa un token para que valide el cliente
                $token = $marinaHumedaCotizacion->getFolio() . bin2hex(random_bytes(16));
                $marinaHumedaCotizacion->setToken($token);
                // Se envia un correo si se solicito notificar al cliente
                if($marinaHumedaCotizacion->isNotificarCliente()){
                    $this->enviaCorreoCotizacion($mailer,$marinaHumedaCotizacion);
                }
                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_VALIDAR,
                    'tipo' => Correo\Notificacion::TIPO_MARINA
                ]);
                $this->enviaCorreoNotificacion($mailer, $notificables, $marinaHumedaCotizacion);
            }
            if ($marinaHumedaCotizacion->getValidacliente() === 2) {
                // Guardar la fecha en la que se valido la cotizacion por el cliente
                $marinaHumedaCotizacion->setRegistroValidaCliente(new \DateTimeImmutable());
                // Quien valido por el cliente
                $marinaHumedaCotizacion->setQuienAcepto($this->getUser()->getNombre());
                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_ACEPTAR,
                    'tipo' => Correo\Notificacion::TIPO_MARINA
                ]);

                $this->enviaCorreoNotificacion($mailer, $notificables, $marinaHumedaCotizacion);
            }
            // Guardar la fecha en la que se valido la cotizacion por novonautica
            $marinaHumedaCotizacion->setRegistroValidaNovo(new \DateTimeImmutable());
            $em->persist($marinaHumedaCotizacion);
            $em->flush();

            return $this->redirectToRoute('combustible_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }
        return $this->render('combustible/validar.html.twig', [
            'title' => 'Cotización Combustible Validación',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'folio' => $folio
        ]);
    }

    /**
     * Genera el pdf de una cotizacion en base a su id
     *
     * @Route("/{id}/pdf", name="combustible-pdf")
     * @Method("GET")
     *
     * @param MarinaHumedaCotizacion $mhc
     *
     * @return PdfResponse
     */
    public function displayPDFAction(MarinaHumedaCotizacion $mhc)
    {
        $em = $this->getDoctrine()->getManager();
        $valor = $em->getRepository('AppBundle:ValorSistema')->find(1);
        $html = $this->renderView('combustible/cotizacionpdf.html.twig', [
            'title' => 'Cotizacion-' . $mhc->getFolio() . '.pdf',
            'marinaHumedaCotizacion' => $mhc,
            'valor' => $valor
        ]);
        $header = $this->renderView('marinahumeda/cotizacion/pdf/pdfencabezado.twig', [
            'marinaHumedaCotizacion' => $mhc
        ]);
        $footer = $this->renderView('marinahumeda/cotizacion/pdf/pdfpie.twig', [
            'marinaHumedaCotizacion' => $mhc,
            'valor' => $valor
        ]);
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
     * @Route("/{id}/pago", name="combustible_pago_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function editPagoAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $this->denyAccessUnlessGranted('ROLE_COMBUSTIBLE_PAGO', $marinaHumedaCotizacion);
        $totPagado = 0;
        $totPagadoMonedero = 0;
        $listaPagos = new ArrayCollection();

        // Conversion de pagos de la DB (MXN) a la vista (USD)
        foreach ($marinaHumedaCotizacion->getPagos() as $pago) {
            if ($pago->getDivisa() == 'USD') {
                $pesos = ($pago->getCantidad() / $pago->getDolar())*100;
                $pago->setCantidad($pesos);
            }
            $listaPagos->add($pago);
        }

        $form = $this->createForm('AppBundle\Form\MarinaHumedaRegistraPagoType', $marinaHumedaCotizacion);
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
                    if($pago->getMetodopago() === 'Monedero'){
                        if($pago->getDivisa() === 'MXN'){
                            $pagoDevuelto = ($pago->getCantidad()/$pago->getDolar())*100;
                        }else{
                            $pagoDevuelto = $pago->getCantidad();
                        }
                        $monederoDevuelto +=  $pagoDevuelto;
                        $notaMonedero = 'Devolución de pago de cotización. Folio: '.$folioCotizacion;
                        $fechaHoraActual = new \DateTime('now');
                        $monederoMovimiento = new MonederoMovimiento();
                        $monederoMovimiento
                            ->setCliente($marinaHumedaCotizacion->getCliente())
                            ->setFecha($fechaHoraActual)
                            ->setMonto($pagoDevuelto)
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
            // Conversion de la vista (USD) a la DB (MXN)
            foreach ($marinaHumedaCotizacion->getPagos() as $pago) {
                if ($pago->getDivisa() === 'USD') {
                    $unpago = ($pago->getCantidad() * $pago->getDolar()) / 100;
                    $pago->setCantidad($unpago);
                } else {
                    $unpago = $pago->getCantidad();
                }
                $totPagado += $unpago;
                if ($pago->getMetodopago() == 'Monedero' && $pago->getId() == null) {
                    $totPagadoMonedero += ($unpago / $pago->getDolar())*100;
                    $monederotot = $monedero - $totPagadoMonedero;
                    $notaMonedero = 'Pago de servicio de combustible. Folio cotización: ' . $folioCotizacion;
                    $fechaHoraActual = new \DateTime('now');
                    $monederoMovimiento = new MonederoMovimiento();
                    $monederoMovimiento
                        ->setCliente($marinaHumedaCotizacion->getCliente())
                        ->setFecha($fechaHoraActual)
                        ->setMonto(($unpago / $pago->getDolar())*100)
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

                    return $this->redirectToRoute('combustible_show', ['id' => $marinaHumedaCotizacion->getId()]);
                }
            }
        }
        return $this->render('combustible/pago.html.twig', [
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/nota", name="combustible_nota")
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
        $folio = $marinaHumedaCotizacion->getFoliorecotiza()===0 ? $marinaHumedaCotizacion->getFolio() : $marinaHumedaCotizacion->getFolio().'-'.$marinaHumedaCotizacion->getFoliorecotiza();
        $cotizacionnota = new CotizacionNota();
        $marinaHumedaCotizacion->addCotizacionnota($cotizacionnota);
        $form = $this->createForm(CotizacionNotaType::class, $cotizacionnota);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fechaHoraActual = new \DateTimeImmutable();
            $cotizacionnota->setFechahoraregistro($fechaHoraActual);
            $em->persist($marinaHumedaCotizacion);
            $em->flush();
            return $this->redirectToRoute('combustible_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }
        return $this->render('combustible/nota.html.twig', [
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
            'folio' => $folio
        ]);
    }

    /**
     * Muestra una cotizacion para recotizar
     *
     * @Route("/{id}/recotizar", name="combustible_recotizar")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior
     *
     * @return RedirectResponse|Response
     */
    public function recotizaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior)
    {
        $this->denyAccessUnlessGranted('COMBUSTIBLE_COTIZACION_REQUOTE', $marinaHumedaCotizacionAnterior);
        if ($marinaHumedaCotizacionAnterior->getEstatus() == 0 ||
            $marinaHumedaCotizacionAnterior->getValidacliente() == 2 ||
            $marinaHumedaCotizacionAnterior->getValidanovo() == 0 ||
            ($marinaHumedaCotizacionAnterior->getValidanovo() == 2 && $marinaHumedaCotizacionAnterior->getValidacliente() == 0)
        ) {
            throw new NotFoundHttpException();
        }
        $folioAnt = $marinaHumedaCotizacionAnterior->getFoliorecotiza()===0 ?
            $marinaHumedaCotizacionAnterior->getFolio() :
            $marinaHumedaCotizacionAnterior->getFolio().'-'.$marinaHumedaCotizacionAnterior->getFoliorecotiza();
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $cliente = $marinaHumedaCotizacionAnterior->getCliente();
        $barco = $marinaHumedaCotizacionAnterior->getBarco();
        $dolar = $marinaHumedaCotizacionAnterior->getDolar();
        $iva = $marinaHumedaCotizacionAnterior->getIva();
        $marinaHumedaCotizacion
            ->setCliente($cliente)
            ->setBarco($barco)
            ->setDescuento($marinaHumedaCotizacionAnterior->getDescuento())
            ->setDolar($dolar)
            ->setIva($marinaHumedaCotizacionAnterior->getIva())
            ->setSubtotal($marinaHumedaCotizacionAnterior->getSubtotal())
            ->setIvatotal($marinaHumedaCotizacionAnterior->getIvatotal())
            ->setDescuentototal($marinaHumedaCotizacionAnterior->getDescuentototal())
            ->setTotal($marinaHumedaCotizacionAnterior->getTotal())
            ->setValidanovo(0)
            ->setValidacliente(0)
            ->setFolio($marinaHumedaCotizacionAnterior->getFolio())
            ->setMensaje($marinaHumedaCotizacionAnterior->getMensaje());
        $servicios = $marinaHumedaCotizacionAnterior->getMHCservicios();

        $precioNoIncluyeIvaMXN = $servicios[0]->getPrecio();
        $ivaDelPrecioGardardoMXN = ($precioNoIncluyeIvaMXN * $iva) / 100;
        $precioIncluyeIvaMXN = $precioNoIncluyeIvaMXN + $ivaDelPrecioGardardoMXN;
        $combustible = new MarinaHumedaCotizaServicios();
        $combustible
            ->setTipo($servicios[0]->getTipo())
            ->setCantidad($servicios[0]->getCantidad())
            ->setPrecio($servicios[0]->getPrecio())
            ->setPrecioAux($precioIncluyeIvaMXN)
            ->setSubtotal($servicios[0]->getSubtotal())
            ->setIva($servicios[0]->getIva())
            ->setDescuento($servicios[0]->getDescuento())
            ->setTotal($servicios[0]->getTotal())
            ->setEstatus($servicios[0]->getEstatus());
        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($combustible);
        $form = $this->createForm(MarinaHumedaCotizacionGasolinaType::class, $marinaHumedaCotizacion,[
            'attr' =>['class' => 'form-combustible']
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $foliorecotizado = $marinaHumedaCotizacionAnterior->getFoliorecotiza() + 1;
            $fechaHoraActual = new \DateTime('now');
            $marinaHumedaCotizacion
                ->setValidanovo(0)
                ->setValidacliente(0)
                ->setEstatus(1)
                ->setFecharegistro($fechaHoraActual)
                ->setFoliorecotiza($foliorecotizado)
                ->setCreador($this->getUser());
            $marinaHumedaCotizacionAnterior
                ->setEstatus(0);
            $em->persist($combustible);
            $em->persist($marinaHumedaCotizacion);
            $em->persist($marinaHumedaCotizacionAnterior);
            $em->flush();
            return $this->redirectToRoute('combustible_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }
        return $this->render('combustible/recotizar.html.twig', [
            'title' => 'Recotización',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
            'folioAnt' => $folioAnt
        ]);

    }

    /**
     * @Route("/{id}/reenviar", name="combustible_reenviar")
     * @Method({"GET", "POST"})
     *
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse
     */
    public function reenviaCorreoAction(MarinaHumedaCotizacion $marinaHumedaCotizacion, \Swift_Mailer $mailer)
    {
        $this->enviaCorreoCotizacion($mailer,$marinaHumedaCotizacion);
        return $this->redirectToRoute('combustible_show', ['id' => $marinaHumedaCotizacion->getId()]);
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
        if (!count($notificables)) { return; }
        $recipientes = [];
        foreach ($notificables as $key => $notificable) {
            $recipientes[$key] = $notificable->getCorreo();
        }
        $message = (new \Swift_Message('¡Cotizacion de combustible!'));
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

    /**
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return void
     */
    private function enviaCorreoCotizacion($mailer, $marinaHumedaCotizacion)
    {
        $em = $this->getDoctrine()->getManager();
        $folio = $marinaHumedaCotizacion->getFoliorecotiza()
            ? $marinaHumedaCotizacion->getFolio() . '-' . $marinaHumedaCotizacion->getFoliorecotiza()
            : $marinaHumedaCotizacion->getFolio();
        $attachment = new Swift_Attachment(
            $this->displayPDFAction($marinaHumedaCotizacion),
            'Cotizacion-' . $folio . '.pdf', 'application/pdf');
        // Enviar correo de confirmacion
        $message = (new \Swift_Message('¡Cotizacion de servicios!'))
            ->setFrom('noresponder@novonautica.com')
            ->setTo($marinaHumedaCotizacion->getCliente()->getCorreo())
            ->setBcc('admin@novonautica.com')
            ->setBody(
                $this->renderView(':mail:cotizacion.html.twig', ['cotizacion' => $marinaHumedaCotizacion]),
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
        $tipoCorreo = $marinaHumedaCotizacion->getFoliorecotiza() === 0 ? 'Cotización servicios marinos' : 'Recotización servicios marinos';
        // Guardar correo en el log de correos
        $historialCorreo = new Correo();
        $historialCorreo
            ->setFecha(new \DateTime())
            ->setTipo($tipoCorreo)
            ->setDescripcion('Envio de cotización servicios marinos con folio: ' . $folio)
            ->setFolioCotizacion($folio)
            ->setMhcotizacion($marinaHumedaCotizacion);
        $em->persist($historialCorreo);
    }

    /**
     * @Route("/{id}", name="combustible_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $this->denyAccessUnlessGranted('COMBUSTIBLE_COTIZACION_DELETE', $marinaHumedaCotizacion);
        $form = $this->createDeleteForm($marinaHumedaCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($marinaHumedaCotizacion->getValidanovo() == 0) {
                $folioRecotiza = $marinaHumedaCotizacion->getFoliorecotiza();
                if($folioRecotiza > 0){
                    $folioRecotizaPrincipal = $folioRecotiza-1;
                    $this->getDoctrine()
                        ->getRepository(MarinaHumedaCotizacion::class)
                        ->findOneBy(['folio' => $marinaHumedaCotizacion->getFolio(),'foliorecotiza' => $folioRecotizaPrincipal])
                        ->setEstatus(true);
                }
                $em = $this->getDoctrine()->getManager();
                $em->remove($marinaHumedaCotizacion);
                $em->flush();
            }
        }
        return $this->redirectToRoute('combustible_index');
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
            ->setAction($this->generateUrl('combustible_delete', ['id' => $marinaHumedaCotizacion->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}