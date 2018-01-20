<?php

namespace AppBundle\Controller\Tienda;

use AppBundle\Entity\Tienda\Peticion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Peticion controller.
 *
 * @Route("tienda_peticion")
 */
class PeticionController extends Controller
{
    /**
     * Lists all peticion entities.
     *
     * @Route("/", name="tienda_peticion_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $peticions = $em->getRepository('AppBundle:Tienda\Peticion')->findAll();

        return $this->render('tienda/peticion/index.html.twig', array(
            'peticions' => $peticions,
        ));
    }

    /**
     * Creates a new peticion entity.
     *
     * @Route("/new", name="tienda_peticion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $peticion = new Peticion();
        $form = $this->createForm('AppBundle\Form\Tienda\PeticionType', $peticion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($peticion);
            $em->flush();

            return $this->redirectToRoute('tienda_peticion_show', array('id' => $peticion->getId()));
        }

        return $this->render('tienda/peticion/new.html.twig', array(
            'peticion' => $peticion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a peticion entity.
     *
     * @Route("/{id}", name="tienda_peticion_show")
     * @Method("GET")
     */
    public function showAction(Peticion $peticion)
    {
        $deleteForm = $this->createDeleteForm($peticion);

        return $this->render('tienda/peticion/show.html.twig', array(
            'peticion' => $peticion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing peticion entity.
     *
     * @Route("/{id}/edit", name="tienda_peticion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Peticion $peticion)
    {
        $deleteForm = $this->createDeleteForm($peticion);
        $editForm = $this->createForm('AppBundle\Form\Tienda\PeticionType', $peticion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tienda_peticion_edit', array('id' => $peticion->getId()));
        }

        return $this->render('tienda/peticion/edit.html.twig', array(
            'peticion' => $peticion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a peticion entity.
     *
     * @Route("/{id}", name="tienda_peticion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Peticion $peticion)
    {
        $form = $this->createDeleteForm($peticion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($peticion);
            $em->flush();
        }

        return $this->redirectToRoute('tienda_peticion_index');
    }

    /**
     * Creates a form to delete a peticion entity.
     *
     * @param Peticion $peticion The peticion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Peticion $peticion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tienda_peticion_delete', array('id' => $peticion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
