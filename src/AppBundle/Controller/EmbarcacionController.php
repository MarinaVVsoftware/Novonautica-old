<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Embarcacion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Embarcacion controller.
 *
 * @Route("/embarcacion")
 */
class EmbarcacionController extends Controller
{
    /**
     * Lists all embarcacion entities.
     *
     * @Route("/", name="embarcacion_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $embarcacions = $em->getRepository('AppBundle:Embarcacion')->findAll();

        return $this->render('embarcacion/index.html.twig', array(
            'embarcacions' => $embarcacions,
        ));
    }

    /**
     * Creates a new embarcacion entity.
     *
     * @Route("/new", name="embarcacion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $embarcacion = new Embarcacion();
        $form = $this->createForm('AppBundle\Form\EmbarcacionType', $embarcacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($embarcacion);
            $em->flush();

            return $this->redirectToRoute('embarcacion_show', array('id' => $embarcacion->getId()));
        }

        return $this->render('embarcacion/new.html.twig', array(
            'embarcacion' => $embarcacion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a embarcacion entity.
     *
     * @Route("/{id}", name="embarcacion_show")
     * @Method("GET")
     */
    public function showAction(Embarcacion $embarcacion)
    {
        $deleteForm = $this->createDeleteForm($embarcacion);

        return $this->render('embarcacion/show.html.twig', array(
            'embarcacion' => $embarcacion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing embarcacion entity.
     *
     * @Route("/{id}/edit", name="embarcacion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Embarcacion $embarcacion)
    {
        $deleteForm = $this->createDeleteForm($embarcacion);
        $editForm = $this->createForm('AppBundle\Form\EmbarcacionType', $embarcacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('embarcacion_edit', array('id' => $embarcacion->getId()));
        }

        return $this->render('embarcacion/edit.html.twig', array(
            'embarcacion' => $embarcacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a embarcacion entity.
     *
     * @Route("/{id}", name="embarcacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Embarcacion $embarcacion)
    {
        $form = $this->createDeleteForm($embarcacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($embarcacion);
            $em->flush();
        }

        return $this->redirectToRoute('embarcacion_index');
    }

    /**
     * Creates a form to delete a embarcacion entity.
     *
     * @param Embarcacion $embarcacion The embarcacion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Embarcacion $embarcacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('embarcacion_delete', array('id' => $embarcacion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
