<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EmbarcacionModelo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Embarcacionmodelo controller.
 *
 * @Route("embarcacion/modelo")
 */
class EmbarcacionModeloController extends Controller
{

    /**
     * Creates a new embarcacionModelo entity.
     *
     * @Route("/new", name="embarcacion_modelo")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $modeloRepo = $em->getRepository('AppBundle:EmbarcacionModelo');
        $query = $request->query;

        $page = (int)$query->get('page') ?: 1;

        $embarcacionMarca = (int)$query->get('marca') ?: null;
        $embarcacionMarca = $embarcacionMarca ? $em->getRepository('AppBundle:EmbarcacionMarca')->find($embarcacionMarca) : null;

        $embarcacionModelo = (int)$query->get('modelo') ?: null;
        $embarcacionModelo = $embarcacionModelo ? $modeloRepo->find($embarcacionModelo) : new Embarcacionmodelo();

        if (!$embarcacionModelo->getMarca()) {
            $embarcacionModelo->setMarca($embarcacionMarca);
        }

        $form = $this->createForm('AppBundle\Form\EmbarcacionModeloType', $embarcacionModelo);
        $form->handleRequest($request);

        $paginacion = $modeloRepo->paginacion($page, 10, 'DESC', $embarcacionMarca);
        $modelos = $paginacion['modelos']->getQuery()->getResult();

        $deleteForms = [];
        foreach ($modelos as $modelo) {
            $deleteForms[] = $this->createDeleteForm($modelo['id'])->createView();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($embarcacionModelo);
            $em->flush();

            return $this->redirectToRoute('embarcacion_modelo');
        }

        return $this->render('embarcacion/modelo/new.html.twig', [
            'embarcacionModelo' => $embarcacionModelo,
            'embarcacionMarca' => $embarcacionMarca,
            'modelos' => $modelos,
            'form' => $form->createView(),
            'deleteForms' => $deleteForms,
            'page' => $page,
            'pages' => $paginacion['pages']
        ]);
    }

    /**
     * Deletes a embarcacionModelo entity.
     *
     * @Route("/{id}", name="embarcacion_modelo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EmbarcacionModelo $embarcacionModelo)
    {
        $form = $this->createDeleteForm($embarcacionModelo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($embarcacionModelo);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param EmbarcacionModelo $embarcacionModelo
     *
     * @return Form The form
     */
    private function createDeleteForm($embarcacionModelo)
    {
        $embarcacionModelo = !is_object($embarcacionModelo) ? $embarcacionModelo : $embarcacionModelo->getId();

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('embarcacion_modelo_delete', ['id' => $embarcacionModelo]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
