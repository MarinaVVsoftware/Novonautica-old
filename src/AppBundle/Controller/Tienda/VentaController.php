<?php
/**
 * User: inrumi
 * Date: 6/27/18
 * Time: 16:16
 */

namespace AppBundle\Controller\Tienda;


use AppBundle\Entity\Cliente;
use AppBundle\Entity\Tienda\Producto;
use AppBundle\Entity\Tienda\Venta;
use AppBundle\Form\Tienda\VentaType;
use DataTables\DataTablesInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VentaController
 * @package AppBundle\Controller\Tienda
 * @Route("/tienda/venta")
 */
class VentaController extends AbstractController
{
    /**
     * @Route("/", name="tienda_venta_index")
     */
    public function indexAction()
    {
        return $this->render(
            'tienda/venta/index.html.twig',
            [
                'title' => 'Listado de ventas',
            ]
        );
    }

    /**
     * @Route("/ventas.json")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function getIndexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'venta');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/new", name="tienda_venta_new")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cliente = $em->getRepository(Cliente::class)->find(413);

        $venta = new Venta();
        $venta->setCliente($cliente);

        $form = $this->createForm(VentaType::class, $venta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($venta);
            $em->flush();

            return $this->redirectToRoute('tienda_venta_new');
        }

        return $this->render(
            'tienda/venta/new.html.twig',
            [
                'title' => 'Punto de venta',
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/productos")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getProductosAction(Request $request)
    {
        $productoRepository = $this->getDoctrine()->getRepository(Producto::class);
        $q = $request->query->get('q');
        $productos = $productoRepository->findProductosLike($q);

        return $this->json(
            ['results' => $productos],
            JsonResponse::HTTP_OK
        )
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/clientes")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getClientesAction(Request $request)
    {
        $clienteRepository = $this->getDoctrine()->getRepository(Cliente::class);
        $q = $request->query->get('q');

        return $this->json(
            [
                'results' => $clienteRepository->getAllWhereNombreLike($q)
            ]
        );
    }

    /**
     * @Route("/producto/{codigoBarras}")
     */
    public function getProductoAction(Request $request, $codigoBarras)
    {
        $producto = $this->getDoctrine()
            ->getRepository(Producto::class)
            ->getProductoByBarcode($codigoBarras);

        if (null === $producto) {
            return $this->json('Not found', JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json(
            $producto,
            JsonResponse::HTTP_OK
        );

    }

    /**
     * @Route("/{id}", name="tienda_venta_show")
     * @param Request $request
     * @param Venta $venta
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Venta $venta)
    {
        return $this->render(
            'tienda/venta/show.html.twig',
            [
                'title' => 'Detalle de venta',
                'venta' => $venta,
            ]
        );
    }

    /**
     * @Route("/{id}/pdf", name="tienda_venta_ticker-pdf")
     * @param Venta $venta
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ticketPDFAction(Venta $venta, Pdf $pdf)
    {
        $conceptos = $this->getDoctrine()
            ->getRepository(Venta\Concepto::class)
            ->getAllByVenta($venta->getId());

        /*
        return $this->render(
            'tienda/venta/ticket-pdf.html.twig',
            [
                'venta' => $venta,
                'conceptos' => $conceptos,
            ]
        );
        */

        $ventaHTML = $this->renderView(
            'tienda/venta/ticket-pdf.html.twig',
            [
                'venta' => $venta,
                'conceptos' => $conceptos,
            ]
        );

        return new PdfResponse(
            $pdf->getOutputFromHtml($ventaHTML),
            'nombre-de-archivo.pdf',
            'application/pdf',
            'inline'
        );
    }
}
