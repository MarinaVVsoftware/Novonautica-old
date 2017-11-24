<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CuentaBancaria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Cuentabancarium controller.
 *
 * @Route("ajustes/cuenta-bancaria")
 */
class CuentaBancariaController extends Controller
{
    /**
     * Lists all cuentaBancarium entities.
     *
     * @Route("/", name="cuenta-bancaria_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cuentaBancarias = $em->getRepository('AppBundle:CuentaBancaria')->findAll();

        return $this->render('cuentabancaria/index.html.twig', array(
            'cuentaBancarias' => $cuentaBancarias,
        ));
    }

    /**
     * Creates a new cuentaBancarium entity.
     *
     * @Route("/nuevo", name="cuenta-bancaria_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cuentaBancarium = new Cuentabancaria();
        $form = $this->createForm('AppBundle\Form\CuentaBancariaType', $cuentaBancarium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cuentaBancarium);
            $em->flush();

            return $this->redirectToRoute('cuenta-bancaria_index');
        }

        return $this->render('cuentabancaria/new.html.twig', array(
            'cuentaBancarium' => $cuentaBancarium,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cuentaBancarium entity.
     *
     * @Route("/{id}", name="cuenta-bancaria_show")
     * @Method("GET")
     */
    public function showAction(CuentaBancaria $cuentaBancarium)
    {
        $deleteForm = $this->createDeleteForm($cuentaBancarium);

        return $this->render('cuentabancaria/show.html.twig', array(
            'cuentaBancarium' => $cuentaBancarium,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cuentaBancarium entity.
     *
     * @Route("/{id}/editar", name="cuenta-bancaria_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, CuentaBancaria $cuentaBancarium)
    {
        $deleteForm = $this->createDeleteForm($cuentaBancarium);
        $editForm = $this->createForm('AppBundle\Form\CuentaBancariaType', $cuentaBancarium);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cuenta-bancaria_index');
        }

        return $this->render('cuentabancaria/edit.html.twig', array(
            'cuentaBancarium' => $cuentaBancarium,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cuentaBancarium entity.
     *
     * @Route("/{id}", name="cuenta-bancaria_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, CuentaBancaria $cuentaBancarium)
    {
        $form = $this->createDeleteForm($cuentaBancarium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cuentaBancarium);
            $em->flush();
        }

        return $this->redirectToRoute('cuenta-bancaria_index');
    }

    /**
     * Creates a form to delete a cuentaBancarium entity.
     *
     * @param CuentaBancaria $cuentaBancarium The cuentaBancarium entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CuentaBancaria $cuentaBancarium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cuenta-bancaria_delete', array('id' => $cuentaBancarium->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
