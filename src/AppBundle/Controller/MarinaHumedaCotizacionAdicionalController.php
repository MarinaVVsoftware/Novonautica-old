<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaCotizacionAdicional;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Marinahumedacotizacionadicional controller.
 *
 * @Route("marina-humeda-cotizacion-adicional")
 */
class MarinaHumedaCotizacionAdicionalController extends Controller
{
    /**
     * Lists all marinaHumedaCotizacionAdicional entities.
     *
     * @Route("/", name="marina-humeda-cotizacion-adicional_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $marinaHumedaCotizacionAdicionals = $em->getRepository('AppBundle:MarinaHumedaCotizacionAdicional')->findAll();

        return $this->render('marinahumeda/cotizacionadicional/index.html.twig', array(
            'marinaHumedaCotizacionAdicionals' => $marinaHumedaCotizacionAdicionals,
            'menumarinaadicional' => 1
        ));
    }

    /**
     * Creates a new marinaHumedaCotizacionAdicional entity.
     *
     * @Route("/nuevo", name="marina-humeda-cotizacion-adicional_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $marinaHumedaCotizacionAdicional = new Marinahumedacotizacionadicional();
        $form = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionAdicionalType', $marinaHumedaCotizacionAdicional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($marinaHumedaCotizacionAdicional);
            $em->flush();

            return $this->redirectToRoute('marina-humeda-cotizacion-adicional_show', array('id' => $marinaHumedaCotizacionAdicional->getId()));
        }

        return $this->render('marinahumeda/cotizacionadicional/new.html.twig', array(
            'marinaHumedaCotizacionAdicional' => $marinaHumedaCotizacionAdicional,
            'form' => $form->createView(),
            'menumarinaadicional' => 1
        ));
    }

    /**
     * Finds and displays a marinaHumedaCotizacionAdicional entity.
     *
     * @Route("/{id}", name="marina-humeda-cotizacion-adicional_show")
     * @Method("GET")
     */
    public function showAction(MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacionAdicional);

        return $this->render('marinahumeda/cotizacionadicional/show.html.twig', array(
            'marinaHumedaCotizacionAdicional' => $marinaHumedaCotizacionAdicional,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing marinaHumedaCotizacionAdicional entity.
     *
     * @Route("/{id}/editar", name="marina-humeda-cotizacion-adicional_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacionAdicional);
        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionAdicionalType', $marinaHumedaCotizacionAdicional);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marina-humeda-cotizacion-adicional_edit', array('id' => $marinaHumedaCotizacionAdicional->getId()));
        }

        return $this->render('marinahumeda/cotizacionadicional/edit.html.twig', array(
            'marinaHumedaCotizacionAdicional' => $marinaHumedaCotizacionAdicional,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'menumarinaadicional' => 1
        ));
    }

    /**
     * Deletes a marinaHumedaCotizacionAdicional entity.
     *
     * @Route("/{id}", name="marina-humeda-cotizacion-adicional_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional)
    {
        $form = $this->createDeleteForm($marinaHumedaCotizacionAdicional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marinaHumedaCotizacionAdicional);
            $em->flush();
        }

        return $this->redirectToRoute('marina-humeda-cotizacion-adicional_index');
    }

    /**
     * Creates a form to delete a marinaHumedaCotizacionAdicional entity.
     *
     * @param MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional The marinaHumedaCotizacionAdicional entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda-cotizacion-adicional_delete', array('id' => $marinaHumedaCotizacionAdicional->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
