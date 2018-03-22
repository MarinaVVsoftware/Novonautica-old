<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/21/18
 * Time: 14:11
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PerfilController extends Controller
{
    /**
     * Creates a new usuario entity.
     *
     * @Route("/perfil", name="perfil")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function showPerfilAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('AppBundle:Usuario')->find($this->getUser()->getId());

        $form = $this->createForm('AppBundle\Form\PerfilType', $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($usuario);
            $em->flush();

            return $this->redirectToRoute('perfil');
        }

        return $this->render('usuario/perfil.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }
}