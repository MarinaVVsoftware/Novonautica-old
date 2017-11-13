<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaServicio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Marinahumedaservicio controller.
 *
 * @Route("marina-humeda-servicio")
 */
class MarinaHumedaServicioController extends Controller
{
    /**
     * Lists all marinaHumedaServicio entities.
     *
     * @Route("/", name="marina-humeda-servicio_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $marinaHumedaServicios = $em->getRepository('AppBundle:MarinaHumedaServicio')->findAll();

        return $this->render('marinahumeda/servicio/index.html.twig', array(
            'marinaHumedaServicios' => $marinaHumedaServicios,
            'marinaserviciomenu' => 1
        ));
    }

    /**
     * Creates a new marinaHumedaServicio entity.
     *
     * @Route("/new", name="marina-humeda-servicio_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $marinaHumedaServicio = new Marinahumedaservicio();
        $form = $this->createForm('AppBundle\Form\MarinaHumedaServicioType', $marinaHumedaServicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($marinaHumedaServicio);
            $em->flush();

            return $this->redirectToRoute('marina-humeda-servicio_index');
        }

        return $this->render('marinahumeda/servicio/new.html.twig', array(
            'marinaHumedaServicio' => $marinaHumedaServicio,
            'form' => $form->createView(),
            'marinaserviciomenu' => 1
        ));
    }

    /**
     * Finds and displays a marinaHumedaServicio entity.
     *
     * @Route("/{id}", name="marina-humeda-servicio_show")
     * @Method("GET")
     */
    public function showAction(MarinaHumedaServicio $marinaHumedaServicio)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaServicio);

        return $this->render('marinahumeda/servicio/show.html.twig', array(
            'marinaHumedaServicio' => $marinaHumedaServicio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing marinaHumedaServicio entity.
     *
     * @Route("/{id}/edit", name="marina-humeda-servicio_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MarinaHumedaServicio $marinaHumedaServicio)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaServicio);
        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaServicioType', $marinaHumedaServicio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marina-humeda-servicio_index');
        }

        return $this->render('marinahumeda/servicio/edit.html.twig', array(
            'marinaHumedaServicio' => $marinaHumedaServicio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'marinaserviciomenu' => 1
        ));
    }

    /**
     * Deletes a marinaHumedaServicio entity.
     *
     * @Route("/{id}", name="marina-humeda-servicio_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MarinaHumedaServicio $marinaHumedaServicio)
    {
        $form = $this->createDeleteForm($marinaHumedaServicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marinaHumedaServicio);
            $em->flush();
        }

        return $this->redirectToRoute('marina-humeda-servicio_index');
    }

    /**
     * Creates a form to delete a marinaHumedaServicio entity.
     *
     * @param MarinaHumedaServicio $marinaHumedaServicio The marinaHumedaServicio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MarinaHumedaServicio $marinaHumedaServicio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda-servicio_delete', array('id' => $marinaHumedaServicio->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
