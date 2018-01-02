<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Correo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Correo controller.
 *
 * @Route("historial-correo")
 */
class CorreoController extends Controller
{
    /**
     * Lists all correo entities.
     *
     * @Route("/", name="historial-correo_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$correos = $em->getRepository('AppBundle:Correo')->findAll();
        $cRepo = $em->getRepository('AppBundle:Correo');
        $correos = $cRepo->OrdenaUnoLista();
        return $this->render('correo/index.html.twig', array(
            'correos' => $correos,
            'title' => 'Historial Correos'
        ));
    }

    /**
     * Finds and displays a correo entity.
     *
     * @Route("/{id}", name="historial-correo_show")
     * @Method("GET")
     */
    public function showAction(Correo $correo)
    {

        return $this->render('correo/show.html.twig', array(
            'correo' => $correo,
        ));
    }
}
