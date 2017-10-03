<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Motor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Motor controller.
 *
 * @Route("motor")
 */
class MotorController extends Controller
{
    /**
     * Lists all motor entities.
     *
     * @Route("/", name="motor_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $motors = $em->getRepository('AppBundle:Motor')->findAll();

        return $this->render('motor/index.html.twig', array(
            'motors' => $motors,
        ));
    }

    /**
     * Creates a new motor entity.
     *
     * @Route("/new", name="motor_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $motor = new Motor();
        $form = $this->createForm('AppBundle\Form\MotorType', $motor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($motor);
            $em->flush();

            return $this->redirectToRoute('motor_show', array('id' => $motor->getId()));
        }

        return $this->render('motor/new.html.twig', array(
            'motor' => $motor,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a motor entity.
     *
     * @Route("/{id}", name="motor_show")
     * @Method("GET")
     */
    public function showAction(Motor $motor)
    {
        $deleteForm = $this->createDeleteForm($motor);

        return $this->render('motor/show.html.twig', array(
            'motor' => $motor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing motor entity.
     *
     * @Route("/{id}/edit", name="motor_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Motor $motor)
    {
        $deleteForm = $this->createDeleteForm($motor);
        $editForm = $this->createForm('AppBundle\Form\MotorType', $motor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('motor_edit', array('id' => $motor->getId()));
        }

        return $this->render('motor/edit.html.twig', array(
            'motor' => $motor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a motor entity.
     *
     * @Route("/{id}", name="motor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Motor $motor)
    {
        $form = $this->createDeleteForm($motor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($motor);
            $em->flush();
        }

        return $this->redirectToRoute('motor_index');
    }

    /**
     * Creates a form to delete a motor entity.
     *
     * @param Motor $motor The motor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Motor $motor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('motor_delete', array('id' => $motor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
