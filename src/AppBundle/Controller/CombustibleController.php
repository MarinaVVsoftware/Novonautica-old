<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Correo;
use AppBundle\Entity\Combustible;
use AppBundle\Entity\CotizacionNota;
use AppBundle\Form\CombustibleType;
use AppBundle\Form\CotizacionNotaType;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
class CombustibleController extends Controller
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
                $results = $dataTables->handle($request, 'combustible');
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
     * @throws \Exception
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $combustible = new Combustible();

        $this->denyAccessUnlessGranted('COMBUSTIBLE_COTIZACION_CREATE', $combustible);

        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('AppBundle:ValorSistema')->findOneBy(['id' => 1]);
        $dolarBase = $qb->getDolar();
        $iva = $qb->getIva();

        $mensaje = $qb->getMensajeCorreoMarinaGasolina();
        $combustible
            ->setIva($iva)
            ->setDolar($dolarBase)
            ->setMensaje($mensaje);

        $barcoid = $request->query->get('id');

        if ($barcoid !== null) {
            $solicitud = $em->getRepository('AppBundle:MarinaHumedaSolicitudGasolina')->find($barcoid);
            $cliente = $solicitud->getCliente();
            $barco = $solicitud->getIdbarco();
            $cantidadgasolina = $solicitud->getCantidadCombustible();
            $tipogasolina = $em->getRepository('AppBundle:Combustible\Catalogo')->find($solicitud->getTipoCombustible());
            $combustible
                ->setTipo($tipogasolina)
                ->setCantidad($cantidadgasolina)
                ->setBarco($barco)
                ->setCliente($cliente);
        }

        $form = $this->createForm(CombustibleType::class, $combustible,[
            'attr' =>['class' => 'form-combustible']
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $foliobase = $qb->getFolioCombustible();
            $folionuevo = $foliobase + 1;
            $combustible
                ->setCliente($combustible->getBarco()->getCliente())
                ->setFecha(new \DateTime())
                ->setFolio($folionuevo)
                ->setFoliorecotiza(0)
                ->setCreador($this->getUser());
            $this->getDoctrine()
                ->getRepository('AppBundle:ValorSistema')
                ->find(1)
                ->setFolioCombustible($folionuevo);
            $em->persist($combustible);

            //En caso de ser cotizacion creada a partir de solicitud de app cambiar estatus de usada
            if ($barcoid !== null) {
                $solicitud = $em->getRepository('AppBundle:MarinaHumedaSolicitudGasolina')->find($barcoid);
                $solicitud->setStatus(true);
                $em->persist($solicitud);
            }
            $em->flush();
            // Buscar correos a notificar
            $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                'evento' => Correo\Notificacion::EVENTO_CREAR,
                'tipo' => Correo\Notificacion::TIPO_COMBUSTIBLE
            ]);
            $this->enviaCorreoNotificacion($mailer, $notificables, $combustible);
            return $this->redirectToRoute('combustible_show', ['id' => $combustible->getId()]);
        }

        return $this->render('combustible/new.html.twig', [
            'title' => 'Nueva cotización Combustible',
            'valdolar' => $dolarBase,
            'valiva' => $iva,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="combustible_show")
     * @Method("GET")
     *
     * @param Combustible $combustible
     *
     * @return Response
     */
    public function showAction(Combustible $combustible)
    {
        switch ($combustible->getValidanovo()){
            case 0:
                $validacion = 'Pendiente validación de Novonautica';
                break;
            case 1:
                $validacion = 'Rechazado por '.$combustible->getNombrevalidanovo();
                break;
            case 2:
                $validacion = 'Aprobado por '.$combustible->getNombrevalidanovo();
                break;
            default: $validacion = '';
        }
        switch ($combustible->getValidacliente()){
            case 0:
                $aceptacion = 'Pendiente aceptación del cliente';
                break;
            case 1:
                $aceptacion = 'Rechazado por '.$combustible->getQuienAcepto();
                break;
            case 2:
                $aceptacion = 'Aprobado por '.$combustible->getQuienAcepto();
                break;
            default: $aceptacion = '';
        }
        switch ($combustible->getEstatuspago()){
            case 1:
                $pago = 'Con adeudo';
                break;
            case 2:
                $pago = 'Pagado';
                break;
            default: $pago = 'No pagado';
        }
        return $this->render('combustible/show.html.twig',[
            'title' => 'Cotización Combustible',
            'combustible' => $combustible,
            'folio' => $combustible->getFolioCompleto(),
            'validacion' => $validacion,
            'aceptacion' => $aceptacion,
            'pago' => $pago
        ]);
    }

    /**
     * @Route("/{id}/tipo-combustible.json", name="json_tipo_combustible")
     * @Method("GET")
     *
     * @param int $tipo
     *
     * @return Response
     */
    public function getTipoCombustibleAction($id)
    {
        $productoRepository = $this->getDoctrine()->getRepository(Combustible\Catalogo::class);

        return $this->json(
            $productoRepository->getProducto($id)
        );
    }

    /**
     * @Route("/{id}/nota", name="combustible_nota")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Combustible $combustible
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function agregaNotaAction(Request $request, Combustible $combustible)
    {
        $em = $this->getDoctrine()->getManager();

        $cotizacionnota = new CotizacionNota();
        $combustible->addCotizacionnota($cotizacionnota);
        $form = $this->createForm(CotizacionNotaType::class, $cotizacionnota);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fechaHoraActual = new \DateTimeImmutable();
            $cotizacionnota->setFechahoraregistro($fechaHoraActual);
            $em->persist($combustible);
            $em->flush();
            return $this->redirectToRoute('combustible_show', ['id' => $combustible->getId()]);
        }
        return $this->render('combustible/nota.html.twig', [
            'combustible' => $combustible,
            'form' => $form->createView(),
            'folio' => $combustible->getFolioCompleto()
        ]);
    }

    /**
     * Genera el pdf de una cotizacion en base a su id
     *
     * @Route("/{id}/pdf", name="combustible_pdf")
     * @Method("GET")
     *
     * @param Combustible $combustible
     *
     * @return PdfResponse
     */
    public function displayPDFAction(Combustible $combustible)
    {
        $em = $this->getDoctrine()->getManager();
        $valor = $em->getRepository('AppBundle:ValorSistema')->find(1);
        $html = $this->renderView('combustible/pdf/cotizacionpdf.html.twig', [
            'title' => 'Cotizacion-' . $combustible->getFolioCompleto() . '.pdf',
            'combustible' => $combustible,
            'valor' => $valor
        ]);
        $header = $this->renderView('combustible/pdf/encabezado.html.twig', [
            'combustible' => $combustible
        ]);
        $footer = $this->renderView('marinahumeda/cotizacion/pdf/pdfpie.twig', [
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
            'Cotizacion-' . $combustible->getFolioCompleto() . '.pdf', 'application/pdf', 'inline'
        );
    }

    /**
     * @Route("/{id}/validar", name="combustible_validar")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Combustible $combustible
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function validaAction(Request $request, Combustible $combustible, \Swift_Mailer $mailer)
    {
        $this->denyAccessUnlessGranted('COMBUSTIBLE_COTIZACION_VALIDATE', $combustible);
        if ($combustible->getEstatus() == 0 ||
            $combustible->getValidanovo() == 1 ||
            $combustible->getValidacliente() == 1 ||
            $combustible->getValidacliente() == 2
        ) {
            throw new NotFoundHttpException();
        }
        $folio = $combustible->getFolioCompleto();
        $editForm = $this->createForm('AppBundle\Form\CombustibleValidarType', $combustible);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $combustible->setNombrevalidanovo($this->getUser()->getNombre());
            if ($combustible->getValidanovo() === 2) {
                // Activa un token para que valide el cliente
                $token = $combustible->getFolio() . bin2hex(random_bytes(16));
                $combustible->setToken($token);
                // Se envia un correo si se solicito notificar al cliente
                if($combustible->isNotificarCliente()){
                    $this->enviaCorreoCotizacion($mailer,$combustible);
                }
                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_VALIDAR,
                    'tipo' => Correo\Notificacion::TIPO_COMBUSTIBLE
                ]);
                $this->enviaCorreoNotificacion($mailer, $notificables, $combustible);

                // Guardar la fecha en la que se valido la cotizacion novonautica y agrega fecha límite para
                // aceptación por el cliente
                $sistema = $em->getRepository('AppBundle:ValorSistema')->find(1);
                $diasCombustible = $sistema->getDiasHabilesCombustible();
                $combustible
                    ->setRegistroValidaNovo(new \DateTimeImmutable())
                    ->setLimiteValidaCliente((new \DateTime('now'))->modify('+ '.$diasCombustible.' day'));
            }
            if ($combustible->getValidacliente() === 2) {
                // Guardar la fecha en la que se valido la cotizacion por el cliente
                $combustible->setRegistroValidaCliente(new \DateTimeImmutable());
                // Quien valido por el cliente
                $combustible->setQuienAcepto($this->getUser()->getNombre());
                // Buscar correos a notificar
                $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_ACEPTAR,
                    'tipo' => Correo\Notificacion::TIPO_COMBUSTIBLE
                ]);

                $this->enviaCorreoNotificacion($mailer, $notificables, $combustible);
            }
            $combustible->setRegistroValidaNovo(new \DateTimeImmutable());
            $em->persist($combustible);
            $em->flush();
            return $this->redirectToRoute('combustible_show', ['id' => $combustible->getId()]);
        }
        return $this->render('combustible/validar.html.twig', [
            'title' => 'Cotización Combustible Validación',
            'combustible' => $combustible,
            'edit_form' => $editForm->createView(),
            'folio' => $folio
        ]);
    }

    /**
     * @Route("/{id}/reenviar", name="combustible_reenviar")
     * @Method({"GET", "POST"})
     *
     * @param Combustible $combustible
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse
     */
    public function reenviaCorreoAction(Combustible $combustible, \Swift_Mailer $mailer)
    {
        $this->enviaCorreoCotizacion($mailer,$combustible);
        return $this->redirectToRoute('combustible_show', ['id' => $combustible->getId()]);
    }

    /**
     * @Route("/{id}/pago", name="combustible_pago_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Combustible $combustible
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function editPagoAction(Request $request, Combustible $combustible)
    {
        $this->denyAccessUnlessGranted('ROLE_COMBUSTIBLE_PAGO', $combustible);
        $totPagado = 0;
        $totPagadoMonedero = 0;
        $listaPagos = new ArrayCollection();
        // Conversion de pagos de la DB (MXN) a la vista (USD)
        foreach ($combustible->getPagos() as $pago) {
            if ($pago->getDivisa() == 'USD') {
                $pesos = ($pago->getCantidad() / $pago->getDolar())*100;
                $pago->setCantidad($pesos);
            }
            $listaPagos->add($pago);
        }
        $form = $this->createForm('AppBundle\Form\CombustiblePagoType', $combustible);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $total = $combustible->getTotal();
            foreach ($listaPagos as $pago) {
                if (false === $combustible->getPagos()->contains($pago)) {
                    $pago->getCombustible()->removePago($pago);
                    $em->persist($pago);
                    $em->remove($pago);
                }
            }
            // Conversion de la vista (USD) a la DB (MXN)
            foreach ($combustible->getPagos() as $pago) {
                if ($pago->getDivisa() === 'USD') {
                    $unpago = ($pago->getCantidad() * $pago->getDolar()) / 100;
                    $pago->setCantidad($unpago);
                } else {
                    $unpago = $pago->getCantidad();
                }
                $totPagado += $unpago;
            }
            if (($total + 1) < $totPagado) {
                $this->addFlash('notice', 'Error! Se ha intentado pagar más del total');
            } else {
                $faltante = $total - $totPagado;
                if ($faltante <= 0.5) {
                    $combustible->setRegistroPagoCompletado(new \DateTimeImmutable());
                    $combustible->setEstatuspago(2);
                } else {
                    $combustible->setEstatuspago(1);
                }
                $combustible->setPagado($totPagado);
                $em->persist($combustible);
                $em->flush();
                return $this->redirectToRoute('combustible_show', ['id' => $combustible->getId()]);
            }
        }
        return $this->render('combustible/pago.html.twig', [
            'combustible' => $combustible,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/recotizar", name="combustible_recotizar")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Combustible $combustibleAnt
     *
     * @return RedirectResponse|Response
     */
    public function recotizaAction(Request $request, Combustible $combustibleAnt)
    {
        $this->denyAccessUnlessGranted('COMBUSTIBLE_COTIZACION_REQUOTE', $combustibleAnt);
        if ($combustibleAnt->getEstatus() == 0 || $combustibleAnt->getValidacliente() == 2 || $combustibleAnt->getValidanovo() == 0 ||
            ($combustibleAnt->getValidanovo() == 2 && $combustibleAnt->getValidacliente() == 0)
        ) {
            throw new NotFoundHttpException();
        }
        $combustible = clone $combustibleAnt;
        $form = $this->createForm(CombustibleType::class, $combustible,[
            'attr' =>['class' => 'form-combustible']
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $foliorecotizado = $combustibleAnt->getFoliorecotiza() + 1;
            $combustible
                ->setEstatus(true)
                ->setFecha(new \DateTime('now'))
                ->setFoliorecotiza($foliorecotizado)
                ->setCreador($this->getUser());
            $combustibleAnt->setEstatus(0);
            $em->persist($combustible);
            $em->persist($combustibleAnt);
            $em->flush();
            return $this->redirectToRoute('combustible_show',['id' => $combustible->getId()]);
        }

        return $this->render('combustible/recotizar.html.twig', [
            'title' => 'Recotización Combustible',
            'idanterior' => $combustibleAnt->getId(),
            'combustible' => $combustible,
            'form' => $form->createView(),
            'folioAnt' => $combustibleAnt->getFolioCompleto()
        ]);
    }

    /**
     * @param Correo\Notificacion[] $notificables
     * @param Combustible $cotizacion
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
     * @param Combustible $combustible
     * @param \Swift_Mailer $mailer
     *
     * @return void
     */
    private function enviaCorreoCotizacion($mailer, $combustible)
    {
        $em = $this->getDoctrine()->getManager();
        $folio = $combustible->getFolioCompleto();
        $attachment = new Swift_Attachment(
            $this->displayPDFAction($combustible),
            'Cotizacion-' . $folio . '.pdf', 'application/pdf');
        // Enviar correo de confirmacion
        $message = (new \Swift_Message('¡Cotizacion de servicios marinos!'))
            ->setFrom('noresponder@novonautica.com')
            ->setTo($combustible->getCliente()->getCorreo())
            ->setBcc('admin@novonautica.com')
            ->setBody(
                $this->renderView(':mail:cotizacion.html.twig', ['cotizacion' => $combustible]),
                'text/html'
            )
            ->attach($attachment);
        if ($combustible->getBarco()->getCorreoCapitan()) {
            $message->addCc($combustible->getBarco()->getCorreoCapitan());
        }
        if ($combustible->getBarco()->getCorreoResponsable()) {
            $message->addCc($combustible->getBarco()->getCorreoResponsable());
        }
        $mailer->send($message);
        $tipoCorreo = $combustible->getFoliorecotiza() === 0 ? 'Cotización servicios marinos' : 'Recotización servicios marinos';
        // Guardar correo en el log de correos
        $historialCorreo = new Correo();
        $historialCorreo
            ->setFecha(new \DateTime())
            ->setTipo($tipoCorreo)
            ->setDescripcion('Envio de cotización servicios marinos con folio: ' . $folio)
            ->setFolioCotizacion($folio)
            ->setCombustible($combustible);
        $em->persist($historialCorreo);
    }

    /**
     * @Route("/{id}", name="combustible_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Combustible $combustible
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Combustible $combustible)
    {
        $this->denyAccessUnlessGranted('COMBUSTIBLE_COTIZACION_DELETE', $combustible);
        $form = $this->createDeleteForm($combustible);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($combustible->getValidanovo() == 0) {
                $folioRecotiza = $combustible->getFoliorecotiza();
                if($folioRecotiza > 0){
                    $folioRecotizaPrincipal = $folioRecotiza-1;
                    $this->getDoctrine()
                        ->getRepository(Combustible::class)
                        ->findOneBy(['folio' => $combustible->getFolio(),'foliorecotiza' => $folioRecotizaPrincipal])
                        ->setEstatus(true);
                }
                $em = $this->getDoctrine()->getManager();
                $em->remove($combustible);
                $em->flush();
            }
        }
        return $this->redirectToRoute('combustible_index');
    }

    /**
     * Crea un formulario para eliminar una cotizacion
     *
     * @param Combustible $combustible The combustible entity
     *
     * @return Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Combustible $combustible)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('combustible_delete', ['id' => $combustible->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
