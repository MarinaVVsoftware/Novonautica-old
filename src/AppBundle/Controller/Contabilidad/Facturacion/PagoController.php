<?php
/**
 * User: inrumi
 * Date: 9/6/18
 * Time: 12:16
 */

namespace AppBundle\Controller\Contabilidad\Facturacion;


use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Entity\Contabilidad\Facturacion\Pago;
use AppBundle\Entity\ValorSistema;
use AppBundle\Extra\NumberToLetter;
use AppBundle\Form\Contabilidad\Facturacion\PagoType;
use DataTables\DataTablesInterface;
use Hyperion\MultifacturasBundle\src\Multifacturas;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                'title' => 'Listado de facturas',
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
//        $this->denyAccessUnlessGranted('CONTABILIDAD_CREATE_PAGO', $pago); TODO agregar permisos

        $em = $this->getDoctrine()->getManager();
        $valoresSistema = $em->getRepository(ValorSistema::class)->find(1); // TODO: diferenciar folios en base a la empresa y permisos de la cuenta

        $pago->setFolio($valoresSistema->getFolioFacturaAstillero());

        $form = $this->createForm(PagoType::class, $pago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sello = $this->multifacturas->procesaPago($pago);

            if (key_exists('codigo_mf_numero', $sello)) {
                $this->addFlash(
                    'danger',
                    'No se pudo sellar la factura, razón: '.$sello['codigo_mf_texto']
                );

                return $this->render('contabilidad/facturacion/new.html.twig', [
                    'facturacion' => $factura,
                    'form' => $form->createView(),
                ]);
            }

            $valoresSistema->setFolioFacturaAstillero($pago->getFolio() + 1);
            $em->persist($pago);
            $em->flush();

            return $this->redirectToRoute('contabilidad_facturacion_index');
        }

        return $this->render(
            'contabilidad/facturacion/pago/new.html.twig',
            [
                'form' => $form->createView(),
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
}
