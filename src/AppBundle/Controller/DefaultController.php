<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
//    /**
//     * @Route("/", name="homepage")
//     */
//    public function indexAction(Request $request)
//    {
//        // replace this example code with whatever you need
//        return $this->render('default/index.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
//        ]);
//    }
//    /**
//     * @Route("/inicio", name="demo1")
//     */
//    public function displayAction(Request $request)
//    {
//        // replace this example code with whatever you need
//        return $this->render('default/demo.twig', [
//            //'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
//        ]);
//    }
    /**
     * @Route("/inicio", name="incio")
     */
    public function displayAdminIndex(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('inicio.twig', [
        ]);
    }
    /**
     * @Route("/clientes/agregar", name="agregar-cliente")
     */
    public function displayNuevoCliente(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('cliente-agregar.twig', [
        ]);
    }
    /**
     * @Route("/clientes/listado", name="lista-cliente")
     */
    public function displayListaCliente(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('cliente-listado.twig', [
        ]);
    }
}
