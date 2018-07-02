<?php
/**
 * User: inrumi
 * Date: 6/27/18
 * Time: 16:16
 */

namespace AppBundle\Controller\Tienda;


use AppBundle\Entity\Tienda\Producto;
use AppBundle\Entity\Tienda\Venta;
use AppBundle\Form\Tienda\VentaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VentaController
 * @package AppBundle\Controller\Tienda
 * @Route("/tienda/venta")
 */
class VentaController extends AbstractController
{
    /**
     * @Route("/", name="tienda_venta")
     */
    public function indexAction(Request $request)
    {
        $venta = new Venta();

        $form = $this->createForm(VentaType::class, $venta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($registro);
//            $em->flush();

            dump($venta);
        }

        return $this->render(
            'tienda/venta/index.html.twig',
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
}
