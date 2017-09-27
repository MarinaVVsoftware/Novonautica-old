<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Motor;
use AppBundle\Entity\Barco;
use AppBundle\Entity\Cliente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @Route("/{cliente}/nuevo", name="barco_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request,Cliente $cliente)
    {
        $barco = new Barco();
        $motor = new Motor();
        $barco->addMotore($motor);
        $form = $this->createForm('AppBundle\Form\BarcoType', $barco);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $barco->setCliente($cliente);
            $em->persist($barco);
            $em->flush();

            return $this->redirectToRoute('cliente_show', array('id' => $barco->getCliente()->getId()));
        }

        return $this->render('barco/new.html.twig', array(
            'barco' => $barco,
            'cliente' => $cliente,
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
//        $deleteForm = $this->createDeleteForm($barco);
//        $editForm = $this->createForm('AppBundle\Form\BarcoType', $barco);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('barco_edit', array('id' => $barco->getId()));
//        }
//
//        return $this->render('barco/edit.html.twig', array(
//            'barco' => $barco,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
        $em = $this->getDoctrine()->getManager();
        $barco = $em->getRepository(Barco::class)->find($barco->getId());
        $cliente = $barco->getCliente();
        if (!$barco) {
            throw $this->createNotFoundException('No hay barcos encontrados para el id '.$barco->getId());
        }

        $originalMotores = new ArrayCollection();

        foreach ($barco->getMotores() as $motor) {
            $originalMotores->add($motor);
        }
        $deleteForm = $this->createDeleteForm($barco);
        $editForm = $this->createForm('AppBundle\Form\BarcoType', $barco);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($originalMotores as $motor){
                if (false === $barco->getMotores()->contains($motor)) {
                    // remove the Task from the Tag
                    $motor->getBarco()->removeMotore($motor);

                    // if it was a many-to-one relationship, remove the relationship like this
                     //$motor->setBarco(null);

                    $em->persist($motor);

                    // if you wanted to delete the Tag entirely, you can also do that
                     $em->remove($motor);
                }
            }
            $em->persist($barco);
            $em->flush();

            // redirect back to some edit page
//            return $this->redirectToRoute('barco_edit', array('id' => $barco->getId()));
            return $this->redirectToRoute('cliente_show', array('id' => $barco->getCliente()->getId()));
        }
        return $this->render('barco/edit.html.twig', array(
            'barco' => $barco,
            'cliente' => $cliente,
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
