<?php

namespace AppBundle\Controller\Tienda;

use AppBundle\Entity\Tienda\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Producto controller.
 *
 * @Route("tienda")
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
     * Creates a new producto entity.
     *
     * @Route("/new", name="tienda_producto_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $producto = new Producto();
        $form = $this->createForm('AppBundle\Form\Tienda\ProductoType', $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($producto);
            $em->flush();

            return $this->redirectToRoute('tienda_producto_show', array('id' => $producto->getId()));
        }

        return $this->render('tienda/producto/new.html.twig', array(
            'producto' => $producto,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a producto entity.
     *
     * @Route("/{id}", name="tienda_producto_show")
     * @Method("GET")
     */
    public function showAction(Producto $producto)
    {
        $deleteForm = $this->createDeleteForm($producto);

        return $this->render('tienda/producto/show.html.twig', array(
            'producto' => $producto,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing producto entity.
     *
     * @Route("/{id}/edit", name="tienda_producto_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Producto $producto)
    {
        $deleteForm = $this->createDeleteForm($producto);
        $editForm = $this->createForm('AppBundle\Form\Tienda\ProductoType', $producto);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tienda_producto_edit', array('id' => $producto->getId()));
        }

        return $this->render('tienda/producto/edit.html.twig', array(
            'producto' => $producto,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($producto);
            $em->flush();
        }

        return $this->redirectToRoute('tienda_producto_index');
    }

    /**
     * Creates a form to delete a producto entity.
     *
     * @param Producto $producto The producto entity
     *
     * @return \Symfony\Component\Form\Form The form
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
