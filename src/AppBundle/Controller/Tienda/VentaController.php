<?php
/**
 * User: inrumi
 * Date: 6/27/18
 * Time: 16:16
 */

namespace AppBundle\Controller\Tienda;


use AppBundle\Entity\Tienda\Inventario\Registro;
use AppBundle\Entity\Tienda\Producto;
use AppBundle\Entity\Tienda\Venta;
use AppBundle\Form\Tienda\VentaType;
use DataTables\DataTablesInterface;
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
        $venta = new Venta();

        $form = $this->createForm(VentaType::class, $venta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*$em = $this->getDoctrine()->getManager();
            $em->persist($venta);
            $em->flush();*/

//            return $this->redirectToRoute('tienda_venta_new');
            dump($venta);
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
     * @Route("/producto/{codigoBarras}")
     */
    public function getProductoAction(Request $request, Producto $producto)
    {
        return $this->json(
            $producto,
            JsonResponse::HTTP_OK
        )
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/{id}", name="tienda_venta_show")
     * @param Request $request
     * @param Venta $venta
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showIndex(Request $request, Venta $venta)
    {
        return $this->render(
            'tienda/venta/show.html.twig',
            [
                'title' => 'Detalle de venta',
                'venta' => $venta
            ]
        );
    }
}
