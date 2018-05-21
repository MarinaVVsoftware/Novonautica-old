<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 21/05/2018
 * Time: 10:58 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Api controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/embarcacion.json", defaults={"_format" = "json"})
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function buscarEmbarcacionAction(Request $request)
    {
        $embarcaciones = $this->getDoctrine()->getManager()->getRepository('AppBundle:Embarcacion')->filtrarBarcos($request);
        return $this->json($embarcaciones);
    }
}