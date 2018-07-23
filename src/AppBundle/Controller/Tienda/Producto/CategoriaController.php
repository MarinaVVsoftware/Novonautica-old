<?php
/**
 * User: inrumi
 * Date: 7/23/18
 * Time: 13:16
 */

namespace AppBundle\Controller\Tienda\Producto;


use AppBundle\Entity\Tienda\Producto\Categoria;
use AppBundle\Form\Tienda\Producto\CategoriaType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoriaController
 * @package AppBundle\Controller\Tienda\Producto
 * @Route("/tienda/producto/categoria")
 */
class CategoriaController extends AbstractController
{
    /**
     * @Route("/", name="tienda_producto_categoria_index")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categoriaRepository = $em->getRepository(Categoria::class);

        $categoria = $request->query->get('emisor') ?: null;
        $categoria = $categoria ? $categoriaRepository->find($categoria) : new Categoria();

        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categoria);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('tienda/producto/categoria/index.html.twig', [
            'title' => 'Categorias',
            'categoria' => $categoria,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/categorias")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUsuariosDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'categoria/producto');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
