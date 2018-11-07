<?php

namespace AppBundle\Controller\Tienda;

use AppBundle\Entity\Tienda\Producto;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Producto controller.
 *
 * @Route("tienda/producto")
 */
class ProductoController extends Controller
{
    /**
     * Lists all producto entities.
     *
     * @Route("/", name="tienda_producto_index")
     * @Method({"GET", "POST"})
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $productos = $em->getRepository(Producto::class);

        $producto = $request->query->get('producto') ?: null;
        $producto = $producto ? $productos->find($producto) : new Producto();

        $form = $this->createForm('AppBundle\Form\Tienda\ProductoType', $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($producto);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(':tienda/producto:index.html.twig', [
            'title' => 'Productos',
            'producto' => $producto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="tienda_producto_borrar")
     * @Method({"GET", "POST"})
     * @param Producto $producto
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function eliminarAction(Producto $producto)
    {
        $em = $this->getDoctrine()->getManager();

        $producto->isActive() ? $producto->setIsActive(false) : $producto->setIsActive(true);

        $em->flush();

        return $this->redirectToRoute('tienda_producto_index');
    }

    /**
     * @Route("/productos", name="tienda_producto_index_data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUsuariosDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'tienda_producto');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
