<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ValorSistema;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Valorsistema controller.
 *
 * @Route("ajustes/valores")
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
}
