<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Evento;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Evento controller.
 *
 * @Route("/agenda")
 */
class EventoController extends Controller
{
    /**
     * @Route("/", name="evento_index")
     */
    public function displayMarinaAgenda(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $eventos = $em->getRepository('AppBundle:Evento')->eventosVisibles($this->getUser());
        return $this->render('evento/marina-agenda.twig', [
            'marinaEventos' => $eventos,
        ]);
    }

    /**
     * Creates a new evento entity.
     *
     * @Route("/nuevo", name="evento_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $evento = new Evento();

        $this->denyAccessUnlessGranted('AGENDA_CREATE',$evento);

        $form = $this->createForm('AppBundle\Form\EventoType', $evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $evento->setUsuario($this->getUser());
            $em->persist($evento);
            $em->flush();
            return $this->redirectToRoute('evento_show', [
                'id' => $evento->getId()
            ]);
        }

        return $this->render('evento/new.html.twig', [
            'evento' => $evento,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="evento_show")
     * Finds and displays a evento entity.
     *
     * @Method("GET")
     */
    public function showAction(Evento $evento)
    {
        $editable = false;
        if($this->getUser() === $evento->getUsuario()){
            $editable = true;
        }
        return $this->render('evento/show.html.twig', [
            'evento' => $evento,
            'editable' => $editable
        ]);
    }

    /**
     * Displays a form to edit an existing evento entity.
     *
     * @Route("/{id}/editar", name="evento_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Evento $evento)
    {
        $this->denyAccessUnlessGranted('AGENDA_EDIT',$evento);

        if($this->getUser() !== $evento->getUsuario()){
            throw new NotFoundHttpException();
        }

        $deleteForm = $this->createDeleteForm($evento);
        $editForm = $this->createForm('AppBundle\Form\EventoType', $evento);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evento_show', array('id' => $evento->getId()));
        }

        return $this->render('evento/edit.html.twig', array(
            'evento' => $evento,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a evento entity.
     *
     * @Route("/{id}", name="evento_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Evento $evento)
    {
        $this->denyAccessUnlessGranted('AGENDA_DELETE',$evento);

        if($this->getUser() !== $evento->getUsuario()){
            throw new NotFoundHttpException();
        }

        $form = $this->createDeleteForm($evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($evento);
            $em->flush();
        }

        return $this->redirectToRoute('evento_index');
    }

    /**
     * Creates a form to delete a evento entity.
     *
     * @param Evento $evento The evento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Evento $evento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evento_delete', array('id' => $evento->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
