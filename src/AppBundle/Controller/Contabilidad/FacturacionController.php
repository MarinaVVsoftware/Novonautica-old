<?php

namespace AppBundle\Controller\Contabilidad;

use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\AstilleroCotizaServicio;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\Combustible;
use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\MarinaHumedaCotizacionAdicional;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\MarinaHumedaServicio;
use AppBundle\Entity\Tienda\Venta;
use AppBundle\Entity\ValorSistema;
use AppBundle\Extra\NumberToLetter;
use AppBundle\Form\Contabilidad\FacturacionType;
use AppBundle\Form\Contabilidad\PreviewType;
use DataTables\DataTablesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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

    public function __construct(
        Pdf $pdf,
        \Swift_Mailer $mailer,
        Multifacturas $multifacturas
    ) {
        $this->pdf = $pdf;
        $this->mailer = $mailer;
        $this->multifacturas = $multifacturas;
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
     * @param Request $request
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
            $factura->setFolio($facturacionRepository->getFolioByEmpresa($factura->getEmisor()->getId()));

            $sello = $this->multifacturas->procesa($factura);

            if (key_exists('codigo_mf_numero', $sello)) {
                $this->addFlash(
                    'danger',
                    'No se pudo sellar la factura, razón: '.$sello['codigo_mf_texto']
                );

                return $this->render('contabilidad/facturacion/new.html.twig', [
                    'factura' => $factura,
                    'form' => $form->createView(),
                ]);
            }

            $em->persist($factura);
            $em->flush();

            if (is_string($receptor->getCorreos()) && strlen($receptor->getCorreos()) > 0) {
                $arrayCorreos = explode(',', $receptor->getCorreos());

                $this->enviarFactura($factura, $arrayCorreos, $this->getUser()->getCorreo());
            }

            return $this->redirectToRoute('contabilidad_facturacion_index');
        }

        return $this->render('contabilidad/facturacion/new.html.twig', [
            'form' => $form->createView(),
            'factura' => $factura,
        ]);
    }

    /**
     * @Route("/preview", name="contabilidad_facturacion_preview")
     * @Method("GET")
     */
    public function previewAction(Request $request)
    {
        $factura = new Facturacion();
        $form = $this->createForm(PreviewType::class, $factura);
        $form->submit($request->query->all()['appbundle_contabilidad_facturacion']);

        $preview = $this->renderView(
            'contabilidad/facturacion/pdf/preview.html.twig',
            [
                'factura' => $factura,
                'num_letras' => (new NumberToLetter())->toWord(($factura->getTotal() / 100), $factura->getMoneda()),
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

        return new JsonResponse([
            'results' => [
                'folio' => $folio,
            ],
        ]);
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

        return new JsonResponse(
            [
                'results' => $clientes,
            ],
            JsonResponse::HTTP_OK);
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

        return new JsonResponse(
            [
                'results' => $rfcs,
            ],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/cotizaciones")
     */
    public function getAllCotizacionesFromClientAction(Request $request)
    {
        $emisor = $request->query->get('e');
        $cliente = $request->query->get('c');
        $manager = $this->getDoctrine()->getManager();

        return new JsonResponse(
            [
                'results' => self::getCotizaciones($manager, $emisor, $cliente),
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

        switch ($emisor) {
            case 3:
                $marinaRepository = $manager->getRepository(MarinaHumedaCotizaServicios::class);
                $conceptos = array_map(function ($concepto) {
                    $concepto['conceptoImporte'] = (int) (($concepto['conceptoImporte'] / 100) * ($concepto['conceptoDolar'] / 100) * 100);
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
                $conceptos = $tiendaRepository->getOneWithCatalogo($cotizacion);
                break;
            default:
                $conceptos = [];
        }

        return (new JsonResponse(
            [
                'results' => $conceptos,
            ]
        ))->setEncodingOptions(JSON_NUMERIC_CHECK);
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
        $html = $this->renderView(
            'contabilidad/facturacion/pdf/factura.html.twig',
            [
                'factura' => $factura,
                'numLetras' => (new NumberToLetter())->toWord(($factura->getTotal() / 100), $factura->getMoneda()),
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

        $timbrado = $this->multifacturas->cancela($factura);

        if ($timbrado['codigo_mf_numero']) {
            $this->addFlash('danger', $timbrado['codigo_mf_texto']);
        } else {
            $this->addFlash('danger', $timbrado['codigo_mf_texto']);

            $factura->setIsCancelada(true);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('contabilidad_facturacion_index');
    }

    /**
     * @Route("/{id}")
     * @param int $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $facturacionRepository = $this->getDoctrine()->getRepository(Facturacion::class);

        try {
            $factura = $facturacionRepository->getFactura($id);
        } catch (NonUniqueResultException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->render(
            'contabilidad/facturacion/show.html.twig',
            [
                'factura' => $factura,
            ]
        );
    }

    public static function getCotizaciones($manager, $emisor, $cliente)
    {
        switch ($emisor) {
            case 3:
                $marinaRepository = $manager->getRepository(MarinaHumedaCotizacion::class);
                $cotizaciones = $marinaRepository->getCotizacionesFromCliente($cliente);
                break;
            case 4:
                $combustibleRepository = $manager->getRepository(Combustible::class);
                $cotizaciones = $combustibleRepository->getCotizacionesFromCliente($cliente);
                break;
            case 5:
                $astilleroRepository = $manager->getRepository(AstilleroCotizacion::class);
                $cotizaciones = $astilleroRepository->getCotizacionesFromCliente($cliente);
                break;
            case 7:
                $tiendaRepository = $manager->getRepository(Venta::class);
                $cotizaciones = $tiendaRepository->getCotizacionesFromCliente($cliente);
                break;
            default:
                $cotizaciones = [];
        }

        return $cotizaciones;
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
