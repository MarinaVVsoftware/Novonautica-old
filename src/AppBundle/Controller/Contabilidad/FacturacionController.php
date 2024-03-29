<?php

namespace AppBundle\Controller\Contabilidad;

use AppBundle\Entity\Astillero;
use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\AstilleroCotizaServicio;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\Combustible;
use AppBundle\Entity\Contabilidad\Catalogo\Servicio;
use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\Tienda\Venta;
use AppBundle\Extra\FacturacionHelper;
use AppBundle\Extra\NumberToLetter;
use AppBundle\Form\Contabilidad\FacturacionType;
use AppBundle\Form\Contabilidad\PreviewType;
use DataTables\DataTablesInterface;
use Hyperion\MultifacturasBundle\src\Multifacturas;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GroupSequence;

/**
 * Facturacion controller.
 *
 * @Route("contabilidad/facturacion")
 */
class FacturacionController extends Controller
{
    /**
     * @var Pdf
     */
    private $pdf;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Multifacturas
     */
    private $multifacturas;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(
        Pdf $pdf,
        \Swift_Mailer $mailer,
        Multifacturas $multifacturas,
        KernelInterface $kernel
    ) {
        $this->pdf = $pdf;
        $this->mailer = $mailer;
        $this->multifacturas = $multifacturas;
        $this->kernel = $kernel;
    }

    /**
     * Lists all facturacion entities.
     *
     * @Route("/", name="contabilidad_facturacion_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function indexAction()
    {
        return $this->render(
            'contabilidad/facturacion/index.html.twig',
            [
                'title' => 'Listado de facturas',
            ]
        );
    }

    /**
     * @Route("/facturas-dt")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function dataTablesAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'facturas');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Al crearse una factura es necesario que se timbre
     * el timbrado se hace actualmente a traves de la clase Multifacturas en el metodo "procesa"
     * este metodo se llama desde FacturaPuedeTimbrarValidator ya que desde ahi se valida si la factura pudo ser
     * timbrada por el procesador de multifacturas
     *
     * @Route("/new", name="contabilidad_facturacion_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $factura = new Facturacion();
        $this->denyAccessUnlessGranted('CONTABILIDAD_CREATE', $factura);

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(FacturacionType::class, $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facturacionRepository = $em->getRepository(Facturacion::class);
            $receptor = $factura->getReceptor();

            if ($factura->getMetodoPago() === 'PUE') {
                $factura->setIsPagada(1);
            }

            // Aqui existe un problema de race condition, donde pueden existir mas de dos usuarios creando una
            // cotizacion, lo que ocasionara que se dupliquen los folios, para prevenir esto
            // se vuelve a leer el valor y se escribe aun cuando el folio se muestra antes de generar el formulario
            $factura->setFolio(
                $facturacionRepository->getFolioByEmpresa($factura->getEmisor()->getId())
            );

            // Buscar las cotizaciones a las que se les asignara una factura
            // Puede ser una o muchas dependiendo si el emisor es V&V Store y este quiere facturar todas las ventas
            $cotizacionRepository = FacturacionHelper::getCotizacionRepository($em, $factura->getEmisor()->getId());
            $cotizacionFecha = $form->get('fechaFiltro')->getData();
            $cotizacionId = $form->get('cotizaciones')->getData();

            $cotizaciones = $cotizacionRepository->getFullCotizacionesFromCliente(
                $factura->getCliente()->getId(),
                $cotizacionFecha->modify('first day of this month'),
                (clone $cotizacionFecha)->modify('last day of this month'),
                $cotizacionId === 'ALL' ? null : $cotizacionId
            );

            // Ciclar por cada cotizacion y asignarle la factura
            foreach ($cotizaciones as $cotizacion) {
                $cotizacion->setFactura($factura);
                $em->persist($cotizacion);
            }

            // Esto solo debe pasar en astillero por el momento
            // checar si la factura pertenece a astillero y verificar si hay que restar o devolver productos de su inventario
            if ($cotizacionRepository->getClassName() === AstilleroCotizacion::class) {
                $astilleroProductoRepository = $em->getRepository(Astillero\Producto::class);
                $conceptos = $form->get('conceptos')->all();

                foreach ($conceptos as $concepto) {
                    // Se asigna la variable $productoId y verificamos que exista
                    if ($productoId = $concepto->get('producto')->getData()) {
                        $producto = $astilleroProductoRepository->find($productoId);

                        $cantidadInicial = $producto->getExistencia() ?? 0;
                        $cantidadDevolver = $concepto->getData()->getCantidad();
                        $cantidadRemover = $concepto->get('productoRemover')->getData();
                        $cantidadFinal = ($cantidadInicial - ($cantidadDevolver - $cantidadRemover));

                        $producto->setExistencia($cantidadFinal);
                        $em->persist($producto);
                    }
                }
            }

            $em->persist($factura);
            $em->flush();

            if (is_string($receptor->getCorreos()) && strlen($receptor->getCorreos()) > 0) {
                $arrayCorreos = explode(',', $receptor->getCorreos());

                $this->enviarFactura($factura, $arrayCorreos, $this->getUser()->getCorreo());
            }

            return $this->redirectToRoute('contabilidad_facturacion_index');
        }

        return $this->render(
            'contabilidad/facturacion/new.html.twig',
            [
                'form' => $form->createView(),
                'factura' => $factura,
            ]
        );
    }

    /**
     * @Route("/preview", name="contabilidad_facturacion_preview")
     */
    public function previewAction(Request $request)
    {
        $factura = new Facturacion();
        $factura->isPreview = true;

        $form = $this->createForm(PreviewType::class, $factura);
        $form->handleRequest($request);

        // Checar si existen las imagenes para reenderizar
        $webDirectory = $this->kernel->getRootDir().'/../web/uploads/facturacion/emisor/logos/';
        $logoExists = file_exists($webDirectory.$factura->getEmisor()->getLogo());

        $preview = $this->renderView(
            'contabilidad/facturacion/pdf/preview.html.twig',
            [
                'factura' => $factura,
                'num_letras' => (new NumberToLetter())->toWord(($factura->getTotal() / 100), $factura->getMoneda()),
                'logoExists' => $logoExists,
            ]
        );

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($preview),
            'factura-preview.pdf',
            'application/pdf',
            'inline'
        );
    }

    /**
     * @Route("/get-folio")
     */
    public function getFolioAction(Request $request)
    {
        $e = $request->query->get('e');

        $folio = $this->getDoctrine()
            ->getRepository(Facturacion::class)
            ->getFolioByEmpresa($e);

        return $this->json(
            [
                'results' => [
                    'folio' => $folio,
                ],
            ]
        );
    }

    /**
     * @Route("/clientes")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getClientesLikeAction(Request $request)
    {
        $q = $request->query->get('q');
        $clientes = $this->getDoctrine()
            ->getRepository(Cliente::class)
            ->getAllWhereNombreLike($q);

        return $this->json(
            [
                'results' => $clientes,
            ]
        );
    }

    /**
     * @Route("/clientes/rfc")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getClienteRFCsAction(Request $request)
    {
        $q = $request->query->get('q');
        $rfcs = $this->getDoctrine()
            ->getRepository(Cliente\RazonSocial::class)
            ->getRFCsFromClient($q);

        return $this->json(
            [
                'results' => $rfcs,
            ]
        );
    }

    /**
     * @Route("/cotizaciones")
     */
    public function getAllCotizacionesFromClientAction(Request $request)
    {
        $emisor = $request->query->get('e');
        $cliente = $request->query->get('c');
        $fecha = $request->query->get('m');

        $manager = $this->getDoctrine()->getManager();

        return $this->json(
            [
                'results' => FacturacionHelper::getCotizaciones($manager, $emisor, $cliente, $fecha),
            ]
        );
    }

    /**
     * @Route("/conceptos")
     */
    public function getConceptosAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $emisor = $request->query->get('e');
        $cotizacion = $request->query->get('c');
        $fecha = $request->query->get('m');

        $inicio = \DateTime::createFromFormat('Y-m-d', $fecha)->modify('first day of this month');
        $fin = (clone $inicio)->modify('last day of this month');

        switch ($emisor) {
            case 3:
                $marinaRepository = $manager->getRepository(MarinaHumedaCotizaServicios::class);
                $conceptos = array_map(function ($concepto) {
                    $concepto['conceptoImporte'] = (int)(($concepto['conceptoImporte']) * ($concepto['conceptoDolar']) / 100);

                    return $concepto;
                }, $marinaRepository->getOneWithCatalogo($cotizacion));
                break;
            case 4:
                $combustibleRepository = $manager->getRepository(Combustible::class);
                $conceptos = $combustibleRepository->getOneWithCatalogo($cotizacion);
                break;
            case 5:
                $astilleroRepository = $manager->getRepository(AstilleroCotizaServicio::class);
                $conceptos = $astilleroRepository->getOneWithCatalogo($cotizacion);
                break;
            case 7:
                $tiendaRepository = $manager->getRepository(Venta\Concepto::class);
                $conceptos = $fecha
                    ? $tiendaRepository->getOneWithCatalogo($cotizacion, $inicio, $fin)
                    : $tiendaRepository->getOneWithCatalogo($cotizacion);
                break;
            default:
                $conceptos = [];
        }

        return $this->json(
            [
                'results' => $conceptos,
            ]
        )->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/sugerencias")
     */
    public function autocompleteAction(Request $request)
    {
        $query = $request->query->get('q');

        if (!$query) {
            return $this->json(['results' => []]);
        }

        $servicioRepository = $this->getDoctrine()->getRepository(Servicio::class);
        $suggestions = $servicioRepository->getSuggestions($query);

        return $this->json(
            [
                'results' => $suggestions,
            ]
        );
    }

    /**
     * @Route("/{id}/pdf", name="contabilidad_factura_pdf")
     * @Method("GET")
     *
     * @param Facturacion $factura
     *
     * @return PdfResponse
     */
    public function getFacturaPDFAction(Facturacion $factura)
    {
        // Checar si existen las imagenes para reenderizar
        $webDirectory = $this->kernel->getRootDir().'/../web/uploads/facturacion/emisor/logos/';
        $logoExists = file_exists($webDirectory.$factura->getEmisor()->getLogo());

        $html = $this->renderView(
            'contabilidad/facturacion/pdf/factura.html.twig',
            [
                'factura' => $factura,
                'numLetras' => (new NumberToLetter())->toWord(($factura->getTotal() / 100), $factura->getMoneda()),
                'logoExists' => $logoExists,
            ]
        );

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            "factura-{$factura->getFolio()}.pdf",
            'application/pdf',
            'inline'
        );
    }

    /**
     * @Route("/{id}/reenviar", name="contabilidad_facturacion_reenvio")
     * @Method("GET")
     *
     * @param Facturacion $factura
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reenviarAction(Facturacion $factura)
    {
        $receptor = $factura->getReceptor();

        if (is_string($receptor->getCorreos()) && strlen($receptor->getCorreos()) > 0) {
            $arrayCorreos = explode(',', $receptor->getCorreos());
            $this->enviarFactura($factura, $arrayCorreos, $this->getUser()->getCorreo());
        }

        return $this->redirectToRoute('contabilidad_facturacion_index');
    }

    /**
     * @Route("/{id}/cancelar", name="contabilidad_facturacion_cancel")
     * @Method({"GET"})
     *
     * @param Facturacion $factura
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelAction(Facturacion $factura)
    {
        $this->denyAccessUnlessGranted('CONTABILIDAD_CANCEL', $factura);

        $em = $this->getDoctrine()->getManager();
        $cotizacionRepository = FacturacionHelper::getCotizacionRepository($em, $factura->getEmisor()->getId());
        $cotizacion = $cotizacionRepository->findOneBy(['factura' => $factura->getId()]);

        if ($this->kernel->getEnvironment() === 'dev') {
            $factura->setIsCancelada(true);
            $cotizacion->setFactura(null);

            $em->flush();

            return $this->redirectToRoute('contabilidad_facturacion_index');
        }

        $timbrado = $this->multifacturas->cancela($factura);

        if ($timbrado['codigo_mf_numero']) {
            $this->addFlash('danger', $timbrado['codigo_mf_texto']);

            return $this->redirectToRoute('contabilidad_facturacion_index');
        }

        $this->addFlash('danger', $timbrado['codigo_mf_texto']);

        $factura->setIsCancelada(true);
        $cotizacion->setFactura(null);

        $em->flush();

        return $this->redirectToRoute('contabilidad_facturacion_index');
    }

    /**
     * @Route("/{id}")
     * @param Facturacion $factura
     *
     * @return Response
     */
    public function showAction(Facturacion $factura)
    {
        return $this->render(
            'contabilidad/facturacion/show.html.twig',
            [
                'factura' => $factura,
            ]
        );
    }

    private function enviarFactura(Facturacion $factura, array $emails, $bbc = null)
    {
        $attachmentPDF = new Swift_Attachment(
            $this->getFacturaPDFAction($factura),
            'factura_'.$factura->getFolio().'.pdf',
            'application/pdf'
        );

        $attachmentXML = new Swift_Attachment(
            $factura->getXml(),
            'factura_'.$factura->getFolio().'.xml',
            'application/pdf'
        );

        $message = (new \Swift_Message('Factura de su pago realizado en '.$factura->getFecha()->format('d/m/Y')))
            ->setFrom('noresponder@novonautica.com')
            ->setTo($emails)
            ->setBody(
                $this->renderView('mail/envio-factura.html.twig', [
                    'factura' => $factura,
                ]),
                'text/html'
            )
            ->attach($attachmentPDF)
            ->attach($attachmentXML);

        if ($bbc) {
            $message->setBcc($bbc);
        }

        $this->mailer->send($message);
    }
}
