<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/16/17
 * Time: 16:19
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/marina", name="marina-slip")
 */
class SlipController extends Controller
{
    /**
     * @Route("/slip", name="marina-administracion")
     */
    public function displayMarinaAdministracion()
    {
        return $this->render('marinahumeda/marina-administracion.twig', [
            'title' => 'Slip'
        ]);
    }
}