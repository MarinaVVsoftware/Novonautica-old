<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AstilleroServicioBasico;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Astilleroserviciobasico controller.
 *
 * @Route("astillero/servicio-basico")
 */
class AstilleroServicioBasicoController extends Controller
{
    /**
     * Lists all astilleroServicioBasico entities.
     *
     * @Route("/", name="astillero_servicio-basico_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $astilleroServicioBasicos = $em->getRepository('AppBundle:AstilleroServicioBasico')->findAll();

        return $this->render('astilleroserviciobasico/index.html.twig', array(
            'astilleroServicioBasicos' => $astilleroServicioBasicos,
            'title' => 'Astillero Servicio Básico'
        ));
    }


    /**
     * Displays a form to edit an existing astilleroServicioBasico entity.
     *
     * @Route("/{id}/editar", name="astillero_servicio-basico_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, AstilleroServicioBasico $astilleroServicioBasico)
    {

        $editForm = $this->createForm('AppBundle\Form\AstilleroServicioBasicoType', $astilleroServicioBasico);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('astillero_servicio-basico_index');
        }

        return $this->render('astilleroserviciobasico/edit.html.twig', array(
            'astilleroServicioBasico' => $astilleroServicioBasico,
            'edit_form' => $editForm->createView(),
            'title' => 'Editar servicio básico'

        ));
    }


}
