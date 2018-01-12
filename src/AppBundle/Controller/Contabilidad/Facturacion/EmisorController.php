<?php

namespace AppBundle\Controller\Contabilidad\Facturacion;

use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Emisor controller.
 *
 * @Route("contabilidad/facturacion/emisor")
 */
class EmisorController extends Controller
{
    /**
     * Lists all emisor entities.
     *
     * @Route("/", name="contabilidad_facturacion_emisor")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $emisorRepo = $em->getRepository('AppBundle:Contabilidad\Facturacion\Emisor');

        $page = $request->query->get('page') ?: 1;
        $length = $request->query->get('length') ?: 10;
        $emisor = $request->query->get('emisor') ?: null;
        $emisor = $emisor ? $emisorRepo->find($emisor) : new Emisor();

        $form = $this->createForm('AppBundle\Form\Contabilidad\Facturacion\EmisorType', $emisor);
        $form->handleRequest($request);

        $pagination = $emisorRepo->pagination($page, $length);
        $pages = ceil($pagination->count() / $length);
        $emisors = $pagination->getQuery()->getResult();
        $deleteForms = [];

        foreach ($emisors as $formEmisor) {
            $deleteForms[] = $this->createDeleteForm($formEmisor)->createView();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($emisor);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('contabilidad/facturacion/emisor/index.html.twig', [
            'title' => 'Emisores',
            'emisors' => $emisors,
            'emisor' => $emisor,
            'form' => $form->createView(),
            'deleteForms' => $deleteForms,
            'page' => $page,
            'pages' => $pages

        ]);
    }

    /**
     * Deletes a emisor entity.
     *
     * @Route("/{id}", name="contabilidad_facturacion_emisor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Emisor $emisor)
    {
        $form = $this->createDeleteForm($emisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($emisor);
            $em->flush();
        }

        return $this->redirectToRoute('contabilidad_facturacion_emisor');
    }

    /**
     * Creates a form to delete a emisor entity.
     *
     * @param Emisor $emisor The emisor entity
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Emisor $emisor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contabilidad_facturacion_emisor_delete', ['id' => $emisor->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
