<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2018-12-08
 * Time: 23:23
 */

namespace AppBundle\Controller\JRFMarine;


use AppBundle\Entity\JRFMarine\Categoria\Subcategoria;
use AppBundle\Entity\JRFMarine\Producto;
use AppBundle\Form\JRFMarine\ProductoType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductoController
 * @package AppBundle\Controller\JRFMarine
 *
 * @Route("/jrfmarine/productos")
 */
class ProductoController extends AbstractController
{
    /**
     * @Route("/", name="jrfmarine_productos_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $productoRepository = $em->getRepository(Producto::class);

        $producto = $request->query->get('producto') ?: null;
        $producto = $producto ? $productoRepository->find($producto) : new Producto();

        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($producto);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(
            'jrfmarine/producto/index.html.twig',
            [
                'title' => 'Productos',
                'producto' => $producto,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'jrfmarine/productos');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/subcategorias/{categoria}")
     */
    public function subcategoriasAction($categoria)
    {
        $subcategoriaRepository = $this->getDoctrine()->getRepository(Subcategoria::class);
        $subcategorias = $subcategoriaRepository->findBy(['categoria' => $categoria]);

        return $this->json(
            [
                'results' => $subcategorias,
            ]
        );
    }
}
