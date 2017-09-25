<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Barco;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Barco controller.
 *
 * @Route("barco")
 */
class BarcoController extends Controller
{
    /**
     * Lists all barco entities.
     *
     * @Route("/", name="barco_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $barcos = $em->getRepository('AppBundle:Barco')->findAll();

        return $this->render('barco/index.html.twig', array(
            'barcos' => $barcos,
        ));
    }

    /**
     * Creates a new barco entity.
     *
     * @Route("/new", name="barco_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $barco = new Barco();
        $form = $this->createForm('AppBundle\Form\BarcoType', $barco);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($barco);
            $em->flush();

            return $this->redirectToRoute('barco_show', array('id' => $barco->getId()));
        }

        return $this->render('barco/new.html.twig', array(
            'barco' => $barco,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a barco entity.
     *
     * @Route("/{id}", name="barco_show")
     * @Method("GET")
     */
    public function showAction(Barco $barco)
    {
        $deleteForm = $this->createDeleteForm($barco);

        return $this->render('barco/show.html.twig', array(
            'barco' => $barco,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing barco entity.
     *
     * @Route("/{id}/edit", name="barco_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Barco $barco)
    {
        $deleteForm = $this->createDeleteForm($barco);
        $editForm = $this->createForm('AppBundle\Form\BarcoType', $barco);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('barco_edit', array('id' => $barco->getId()));
        }

        return $this->render('barco/edit.html.twig', array(
            'barco' => $barco,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a barco entity.
     *
     * @Route("/{id}", name="barco_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Barco $barco)
    {
        $form = $this->createDeleteForm($barco);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($barco);
            $em->flush();
        }

        return $this->redirectToRoute('barco_index');
    }

    /**
     * Creates a form to delete a barco entity.
     *
     * @param Barco $barco The barco entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Barco $barco)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('barco_delete', array('id' => $barco->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
