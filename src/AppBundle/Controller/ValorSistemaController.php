<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ValorSistema;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Valorsistema controller.
 *
 * @Route("ajustes")
 */
class ValorSistemaController extends Controller
{
    /**
     * Lists all valorSistema entities.
     *
     * @Route("/", name="ajustes_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $valorSistemas = $em->getRepository('AppBundle:ValorSistema')->findAll();

        return $this->render('valorsistema/index.html.twig', array(
            'valorSistemas' => $valorSistemas,
            'ajustesmenu' => 1
        ));
    }

//    /**
//     * Creates a new valorSistema entity.
//     *
//     * @Route("/new", name="ajustes_new")
//     * @Method({"GET", "POST"})
//     */
//    public function newAction(Request $request)
//    {
//        $valorSistema = new Valorsistema();
//        $form = $this->createForm('AppBundle\Form\ValorSistemaType', $valorSistema);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($valorSistema);
//            $em->flush();
//
//            return $this->redirectToRoute('ajustes_show', array('id' => $valorSistema->getId()));
//        }
//
//        return $this->render('valorsistema/new.html.twig', array(
//            'valorSistema' => $valorSistema,
//            'form' => $form->createView(),
//        ));
//    }

//    /**
//     * Finds and displays a valorSistema entity.
//     *
//     * @Route("/{id}", name="ajustes_show")
//     * @Method("GET")
//     */
//    public function showAction(ValorSistema $valorSistema)
//    {
//        $deleteForm = $this->createDeleteForm($valorSistema);
//
//        return $this->render('valorsistema/show.html.twig', array(
//            'valorSistema' => $valorSistema,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

    /**
     * Displays a form to edit an existing valorSistema entity.
     *
     * @Route("/{id}/editar", name="ajustes_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ValorSistema $valorSistema)
    {
        //$deleteForm = $this->createDeleteForm($valorSistema);
        $editForm = $this->createForm('AppBundle\Form\ValorSistemaType', $valorSistema);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //return $this->redirectToRoute('ajustes_edit', array('id' => $valorSistema->getId()));
            return $this->redirectToRoute('ajustes_index');
        }

        return $this->render('valorsistema/edit.html.twig', array(
            'valorSistema' => $valorSistema,
            'edit_form' => $editForm->createView(),
            'ajustesmenu' => 1
            //'delete_form' => $deleteForm->createView(),
        ));
    }

//    /**
//     * Deletes a valorSistema entity.
//     *
//     * @Route("/{id}", name="ajustes_delete")
//     * @Method("DELETE")
//     */
//    public function deleteAction(Request $request, ValorSistema $valorSistema)
//    {
//        $form = $this->createDeleteForm($valorSistema);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($valorSistema);
//            $em->flush();
//        }
//
//        return $this->redirectToRoute('ajustes_index');
//    }

//    /**
//     * Creates a form to delete a valorSistema entity.
//     *
//     * @param ValorSistema $valorSistema The valorSistema entity
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createDeleteForm(ValorSistema $valorSistema)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('ajustes_delete', array('id' => $valorSistema->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }
}
