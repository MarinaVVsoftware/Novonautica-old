<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 22/06/2018
 * Time: 04:03 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Correo;
use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\ValorSistema;
use AppBundle\Form\MarinaHumedaCotizacionGasolinaType;
use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     * Enlista todas las cotizaciones combustible
     *
     * @Route("/", name="combustible_index")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function indexCombustibleAction(Request $request, DataTablesInterface $dataTables)
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
    public function newCombustibleAction(Request $request, \Swift_Mailer $mailer)
    {
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();

        // Bloquear acceso si no puede crear cotizaciones
        $this->denyAccessUnlessGranted('MARINA_COTIZACION_CREATE', $marinaHumedaCotizacion);
        $combustible = new MarinaHumedaCotizaServicios();
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('AppBundle:ValorSistema')->findOneBy(['id' => 1]);
        $combustible->setCantidad(0);
        $dolarBase = $qb->getDolar();
        $iva = $qb->getIva();
        $mensaje = $qb->getMensajeCorreoMarinaGasolina();
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
            ->setMensaje($mensaje);
        $form = $this->createForm(MarinaHumedaCotizacionGasolinaType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //$dolar = $marinaHumedaCotizacion->getDolar();
            $cantidad = $combustible->getCantidad();
            $precioMXN = (round(($combustible->getPrecio() / ($iva + 100)), 2)) * 100;
            $subtotalMXN = ($cantidad * $precioMXN);
            $ivaMXN = ($subtotalMXN * ($iva / 100));
            $totalMXN = ($subtotalMXN + $ivaMXN);

            $foliobase = $qb->getFolioMarina();
            $folionuevo = $foliobase + 1;

            if ($barcoid !== null) {
                $solicitud = $em->getRepository('AppBundle:MarinaHumedaSolicitudGasolina')->find($barcoid);
                $solicitud->setStatus(1);
            }
            $combustible
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setPrecio($precioMXN)// Precio sin iva
                ->setSubtotal($subtotalMXN)// Total sin iva
                ->setIva($ivaMXN)// El iva del total
                ->setTotal($totalMXN); // Total con iva
            ;
            $marinaHumedaCotizacion
                ->setIva($iva)
                ->setSubtotal($subtotalMXN)
                ->setIvatotal($ivaMXN)
                ->setTotal($totalMXN)
                ->setValidanovo(0)
                ->setValidacliente(0)
                ->setEstatus(1)
                ->setFecharegistro(new \DateTime())
                ->setFolio($folionuevo)
                ->setFoliorecotiza(0);

            $this->getDoctrine()
                ->getRepository(ValorSistema::class)
                ->find(1)
                ->setFolioMarina($folionuevo);

            $marinaHumedaCotizacion->setCreador($this->getUser());

            $em->persist($combustible);
            $em->persist($marinaHumedaCotizacion);
            $em->flush();

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
        if($marinaHumedaCotizacion->getFoliorecotiza() === 0){
            $folio = $marinaHumedaCotizacion->getFolio();
        }else{
            $folio = $marinaHumedaCotizacion->getFolio().'-'.$marinaHumedaCotizacion->getFoliorecotiza();
        }
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
     * Crea un formulario para eliminar una cotizacion
     *
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion The marinaHumedaCotizacion entity
     *
     * @return Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda_delete', ['id' => $marinaHumedaCotizacion->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}