<?php

namespace AppBundle\Controller\Tienda;

use AppBundle\Entity\Tienda\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $productos = $em->getRepository('AppBundle:Tienda\Producto');

        $producto = $request->query->get('producto') ?: null;
        $producto = $producto ? $productos->find($producto) : new Producto();
        $listado = $productos->findAll();

        $form = $this->createForm('AppBundle\Form\Tienda\ProductoType', $producto);
        $form->handleRequest($request);

        $deleteForms = [];

        foreach ($listado as $formborrar) {
            $deleteForms[] = $this->createDeleteForm($formborrar)->createView();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($producto);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(':tienda/producto:index.html.twig', [
            'title' => 'Productos',
            'producto' => $producto,
            'productos' => $listado,
            'form' => $form->createView(),
            'deleteForms' => $deleteForms,
        ]);
    }

    /**
     * Deletes a producto entity.
     *
     * @Route("/{id}", name="tienda_producto_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Producto $producto)
    {
        $form = $this->createDeleteForm($producto);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        $encontrar = $em->getRepository('AppBundle:Tienda\Peticion')->findby(array('peticion' => $producto->getId()));

        if (empty($encontrar)){
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($producto);
            $em->flush();
        }
            $this->addFlash('notice', 'El producto ha sido eliminado');
            return $this->redirectToRoute('tienda_producto_index');
        }else{
            $this->addFlash('notice', 'No puede eliminar este producto hasta que se elimine la solicitud que la contiene');
            return $this->redirectToRoute('tienda_producto_index');
        }
    }

    /**
     * Creates a form to delete a producto entity.
     *
     * @param Producto $producto The producto entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Producto $producto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tienda_producto_delete', array('id' => $producto->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
