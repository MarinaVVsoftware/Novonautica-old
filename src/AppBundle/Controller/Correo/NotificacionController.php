<?php

namespace AppBundle\Controller\Correo;

use AppBundle\Entity\Correo\Notificacion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Notificacion controller.
 *
 * @Route("historial-correo/notificacion")
 */
class NotificacionController extends Controller
{
    /**
     * Lists all notificacion entities.
     *
     * @Route("/", name="historial-correo_notificacion_index")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Correo\Notificacion');

        $notificaciones = $repo->findAll();
        $deleteForms = [];
        foreach ($notificaciones as $key => $notificacion) {
            $deleteForms[$key] = $this->createDeleteForm($notificacion)->createView();
        }

        $notificacion = $request->query->get('n');
        $notificacion = null === $notificacion ? new Notificacion() : $repo->find($notificacion);

        $form = $this->createForm('AppBundle\Form\Correo\NotificacionType', $notificacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($notificacion);
            $em->flush();

            return $this->redirectToRoute('historial-correo_notificacion_index');
        }

        return $this->render('correo/notificacion/index.html.twig', [
            'notificados' => $notificaciones,
            'deleteForms' => $deleteForms,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a notificacion entity.
     *
     * @Route("/{id}", name="historial-correo_notificacion_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Notificacion $notificacion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Notificacion $notificacion)
    {
        $form = $this->createDeleteForm($notificacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($notificacion);
            $em->flush();
        }

        return $this->redirectToRoute('historial-correo_notificacion_index');
    }

    /**
     * Creates a form to delete a notificacion entity.
     *
     * @param Notificacion $notificacion The notificacion entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Notificacion $notificacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('historial-correo_notificacion_delete', ['id' => $notificacion->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
