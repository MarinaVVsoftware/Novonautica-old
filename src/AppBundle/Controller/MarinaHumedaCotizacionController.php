<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Form\MarinaHumedaCotizacionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Marinahumedacotizacion controller.
 *
 * @Route("marina-humeda")
 */
class MarinaHumedaCotizacionController extends Controller
{
    /**
     * Lists all marinaHumedaCotizacion entities.
     *
     * @Route("/", name="marina-humeda_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $marinaHumedaCotizacions = $em->getRepository('AppBundle:MarinaHumedaCotizacion')->findAll();

        return $this->render('marinahumedacotizacion/index.html.twig', array(
            'marinaHumedaCotizacions' => $marinaHumedaCotizacions,
        ));
    }

    /**
     * Creates a new marinaHumedaCotizacion entity.
     *
     * @Route("/new", name="marina-humeda_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $form = $this->createForm(MarinaHumedaCotizacionType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // ... save the meetup, redirect etc.
        }

        return $this->render('marinahumedacotizacion/new.html.twig', array(
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
        ));

//        $marinaHumedaCotizacion = new Marinahumedacotizacion();
//        $form = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionType', $marinaHumedaCotizacion);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($marinaHumedaCotizacion);
//            $em->flush();
//
//            return $this->redirectToRoute('marina-humeda_show', array('id' => $marinaHumedaCotizacion->getId()));
//        }
//
//        return $this->render('marinahumedacotizacion/new.html.twig', array(
//            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
//            'form' => $form->createView(),
//        ));


    }

    /**
     * Finds and displays a marinaHumedaCotizacion entity.
     *
     * @Route("/{id}", name="marina-humeda_show")
     * @Method("GET")
     */
    public function showAction(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);

        return $this->render('marinahumedacotizacion/show.html.twig', array(
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing marinaHumedaCotizacion entity.
     *
     * @Route("/{id}/edit", name="marina-humeda_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);
        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionType', $marinaHumedaCotizacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marina-humeda_edit', array('id' => $marinaHumedaCotizacion->getId()));
        }

        return $this->render('marinahumedacotizacion/edit.html.twig', array(
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a marinaHumedaCotizacion entity.
     *
     * @Route("/{id}", name="marina-humeda_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $form = $this->createDeleteForm($marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marinaHumedaCotizacion);
            $em->flush();
        }

        return $this->redirectToRoute('marina-humeda_index');
    }

    /**
     * Creates a form to delete a marinaHumedaCotizacion entity.
     *
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion The marinaHumedaCotizacion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda_delete', array('id' => $marinaHumedaCotizacion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /**
     * @Route("/agenda", name="marina-agenda")
     */
    public function displayMarinaAgenda(Request $request)
    {
        return $this->render('marina-agenda.twig', [
            'marinaagenda' => 1
        ]);
    }
    /**
     * @Route("/agenda/nuevo-evento", name="marina-agenda-nuevo-evento")
     */
    public function displayMarinaAgendaNuevoEvento(Request $request)
    {
        return $this->render('marina-agenda-nuevo-evento.twig', [
            'marinaagenda' => 1
        ]);
    }
    /**
     * @Route("/administracion", name="marina-administracion")
     */
    public function displayMarinaAdministracion(Request $request)
    {
        return $this->render('marina-administracion.twig', [
            'marinaadministracion' => 1
        ]);
    }
    /**
     * @Route("/nueva-cotizacion", name="marina-nueva-cotizacion")
     */
    public function displayMarinaNuevaCotizacion(Request $request)
    {
        return $this->render('marina-nueva-cotizacion.twig', [
            'marinanuevacotizacion' => 1
        ]);
    }
    /**
     * @Route("/cotizaciones", name="marina-cotizaciones")
     */
    public function displayMarinaCotizaciones(Request $request)
    {
        return $this->render('marina-cotizaciones.twig', [
            'marinacotizaciones' => 1
        ]);
    }

}
