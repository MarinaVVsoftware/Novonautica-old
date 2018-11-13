<?php

namespace AppBundle\Controller\Astillero;

use AppBundle\Entity\Astillero\Producto;
use AppBundle\Entity\Astillero\Proveedor;
use AppBundle\Form\Astillero\ProductoType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Producto controller.
 *
 * @Route("astillero/producto")
 */
class ProductoController extends Controller
{
    /**
     * @Route("/", name="astillero_producto_index")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $productos = $em->getRepository(Producto::class);
        $proveedoresRepository = $em->getRepository(Proveedor::class);

        $producto = $request->query->get('producto') ?: null;
        $producto = $producto ? $productos->find($producto) : new Producto();

        if (!$producto->getId()) {
            $producto->addProveedore(
                $proveedoresRepository->findFirst()
            );
        }

        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($producto);
            $em->flush();

            return $this->redirectToRoute('astillero_producto_index');
        }

        return $this->render(
            'astillero/producto/index.html.twig',
            [
                'title' => 'Astillero Productos',
                'producto' => $producto,
                'form' => $form->createView(),
                'deleteForm' => $producto->getId() ? $this->createDeleteForm($producto)->createView() : null,
            ]
        );
    }

    /**
     * @Route("/productos")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function indexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'AstilleroProducto');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route(
     *     "/buscarproducto/{id}.{_format}",
     *     name="ajax_astillero_busca_producto",
     *     defaults={"_format"="json"}
     *     )
     * @param $id
     *
     * @return JsonResponse
     */
    public function buscarAction($id)
    {
        $productoRepository = $this->getDoctrine()->getRepository(Producto::class);

        return new JsonResponse(
            $productoRepository->obtenerProducto($id),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/{id}", name="astillero_producto_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Producto $producto
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Producto $producto)
    {
        $form = $this->createDeleteForm($producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($producto);
                $em->flush();

                return $this->redirectToRoute('astillero_producto_index');
            } catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash(
                    'error',
                    'Error!, No se puede borrar este producto, esta siendo utilizado en las cotizaciones'
                );
            }

        }

        return $this->redirectToRoute(
            $request->headers->get('referer')
        );
    }

    /**
     * Creates a form to delete a producto entity.
     *
     * @param Producto $producto The producto entity
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Producto $producto)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'astillero_producto_delete',
                    ['id' => $producto->getId()]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
    }
}
