<?php
/**
 * User: inrumi
 * Date: 9/6/18
 * Time: 12:16
 */

namespace AppBundle\Controller\Contabilidad\Facturacion;


use AppBundle\Entity\Cliente\CuentaBancaria;
use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Entity\Contabilidad\Facturacion\Pago;
use AppBundle\Extra\NumberToLetter;
use AppBundle\Form\Contabilidad\Facturacion\PagoType;
use DataTables\DataTablesInterface;
use Hyperion\MultifacturasBundle\src\Multifacturas;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Pago
 * @package AppBundle\Controller\Contabilidad\Facturacion
 *
 * @Route("contabilidad/facturacion/pagos")
 */
class PagoController extends AbstractController
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
     * @Route("/pagos-dt")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function dataTablesAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'complemento_pago');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Lists all facturacion entities.
     *
     * @Route("/{id}", name="contabilidad_factura_pago_index", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function indexAction(Facturacion $factura)
    {
        return $this->render(
            'contabilidad/facturacion/pago/index.html.twig',
            [
                'title' => 'Listado de complementos de pago',
                'factura' => $factura,
            ]
        );
    }

    /**
     * Las facturas con forma de pago PPD necesitan una relacion con pagos, esta entidad es la de sus pagos
     *
     * @Route("/{id}/new", name="contabilidad_factura_pago_new_from_factura", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Facturacion $factura
     *
     * @return RedirectResponse|Response
     */
    public function newFromFacturaAction(Request $request, Facturacion $factura)
    {
        $pago = new Facturacion\Pago($factura);

        $em = $this->getDoctrine()->getManager();
        $pagoRepository = $em->getRepository(Pago::class);
        $totalPagado = $pagoRepository->getTotalPagadoEnFactura($factura->getId());

        if ($totalPagado['parcialidad'] > 0) {
            $pago->setNumeroParcialidad($totalPagado['parcialidad']);
            $pago->setImporteSaldoAnterior($factura->getTotal() - $totalPagado['pagado']);
        } else {
            $pago->setImporteSaldoAnterior($factura->getTotal());
        }

        $form = $this->createForm(PagoType::class, $pago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $facturacionRepository = $em->getRepository(Facturacion::class);
            $receptor = $factura->getReceptor();

            $factura->setIsPagada(2);

            if ($pago->getImporteSaldoInsoluto() <= 0.0) {
                $factura->setIsPagada(1);
            }

            // Aqui existe un problema de race condition, donde pueden existir mas de dos usuarios creando una
            // cotizacion, lo que ocasionara que se dupliquen los folios, para prevenir esto
            // se vuelve a leer el valor y se escribe aun cuando el folio se muestra antes de generar el formulario
            $pago->setFolio($facturacionRepository->getFolioByEmpresa($factura->getEmisor()->getId()));

            $sello = $this->multifacturas->procesaPago($pago);

            if (key_exists('codigo_mf_numero', $sello)) {
                $this->addFlash(
                    'danger',
                    'No se pudo sellar la factura, razón: '.$sello['codigo_mf_texto']
                );

                return $this->render('contabilidad/facturacion/pago/new.html.twig', [
                    'form' => $form->createView(),
                    'facturacion' => $factura,
                ]);
            }

            $em->persist($factura);
            $em->persist($pago);
            $em->flush();

            if (is_string($receptor->getCorreos()) && strlen($receptor->getCorreos()) > 0) {
                $arrayCorreos = explode(',', $receptor->getCorreos());

                $this->enviarPago($factura, $pago, $arrayCorreos, $this->getUser()->getCorreo());
            }

            return $this->redirectToRoute('contabilidad_factura_pago_index', ['id' => $factura->getId()]);
        }

        return $this->render(
            'contabilidad/facturacion/pago/new.html.twig',
            [
                'form' => $form->createView(),
                'factura' => $factura,
            ]
        );
    }

    /**
     * @Route("/{id}/cuentas-bancarias")
     */
    public function getCuentasBancariasAction(Request $request, Facturacion $factura)
    {
        $cliente = $factura->getCliente()->getId();
        $query = $request->query->get('q');
        $cuentas = $this->getDoctrine()
            ->getRepository(CuentaBancaria::class)
            ->getCuentasBancariasLikeSelect2($cliente, $query);

        return new JsonResponse(
            [
                'results' => $cuentas,
            ]
        );
    }

    /**
     * @Route("/{id}/preview", name="contabilidad_factura_pago_preview")
     */
    public function previewAction(Request $request, Facturacion $factura)
    {
        $pago = new Pago($factura);
        $form = $this->createForm(PagoType::class, $pago);
        $form->submit($request->query->all()['appbundle_contabilidad_facturacion_pago']);

        $preview = $this->renderView(
            'contabilidad/facturacion/pago/pdf/preview.html.twig',
            [
                'factura' => $factura,
                'pago' => $pago,
                'num_letras' => (new NumberToLetter())->toWord(($pago->getMontoPagos() / 100), $pago->getMonedaPagos()),
            ]
        );

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($preview),
            'pago-preview.pdf',
            'application/pdf',
            'inline'
        );
    }

    /**
     * @Route("/{id}/pdf/{pago}", name="contabilidad_factura_pago_pdf")
     */
    public function pdfAction(Facturacion $factura, Pago $pago)
    {
        $html = $this->renderView(
            'contabilidad/facturacion/pago/pdf/pago.html.twig',
            [
                'title' => 'Comprobante de pago',
                'factura' => $factura,
                'pago' => $pago,
                'num_letras' => (new NumberToLetter())->toWord(($factura->getTotal() / 100), $factura->getMoneda()),
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
     * @Route("/{id}/reenviar/{pago}", name="contabilidad_factura_pago_reenvio")
     */
    public function reenviarAction(Facturacion $factura, Pago $pago)
    {
        $receptor = $factura->getReceptor();

        if (is_string($receptor->getCorreos()) && strlen($receptor->getCorreos()) > 0) {
            $arrayCorreos = explode(',', $receptor->getCorreos());
            $this->enviarPago($factura, $pago, $arrayCorreos, $this->getUser()->getCorreo());
        }

        return $this->redirectToRoute('contabilidad_factura_pago_index', ['id' => $factura->getId()]);
    }

    /**
     * @Route("/{id}/cancelar/{pago}")
     *
     * @param $id
     * @param Pago $pago
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelAction($id, Pago $pago)
    {
        $timbrado = $this->multifacturas->cancelaPago($pago);

        if ($timbrado['codigo_mf_numero']) {
            $this->addFlash('danger', $timbrado['codigo_mf_texto']);
        } else {
            $this->addFlash('danger', $timbrado['codigo_mf_texto']);

            $pago->setIsCancelado(true);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('contabilidad_factura_pago_index', ['id' => $id]);
    }

    private function enviarPago(Facturacion $factura, Pago $pago, array $emails, $bbc = null)
    {
        $attachmentPDF = new Swift_Attachment(
            $this->pdfAction($factura, $pago),
            'factura_'.$pago->getFolio().'.pdf',
            'application/pdf'
        );

        $attachmentXML = new Swift_Attachment(
            $pago->getXml(),
            'factura_'.$pago->getFolio().'.xml',
            'application/pdf'
        );

        $message = (new \Swift_Message('Factura de su pago realizado en '.$pago->getFecha()->format('d/m/Y')))
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
