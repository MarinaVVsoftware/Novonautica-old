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
     * @Route("/embarcacion.json")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function buscarEmbarcacionAction(Request $request)
    {
        $headers = $request->headers->all();
        $embarcaciones = $this->getDoctrine()->getManager()->getRepository('AppBundle:Embarcacion')->filtrarBarcos($request);
        if($headers['origin'][0] === 'http://www.oceandeal.com' || $headers['origin'][0] === 'https://www.oceandeal.com'){
            return $this->json($embarcaciones,200,['Access-Control-Allow-Origin'=> $headers['origin'][0]]);
        }else{
            return $this->json(null);
        }
    }

    /**
     * @Route("/marcas.json")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function buscarMarcasAction(Request $request)
    {
        $headers = $request->headers->all();
        $marcas = $this->getDoctrine()->getManager()->getRepository('AppBundle:EmbarcacionMarca')->encuentraMarcas();
        if($headers['origin'][0] === 'http://www.oceandeal.com' || $headers['origin'][0] === 'https://www.oceandeal.com'){
            return $this->json($marcas,200,['Access-Control-Allow-Origin'=> $headers['origin'][0]]);
        }else{
            return $this->json(null);
        }
    }

    /**
     * @Route("/paises.json")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function buscarPaisesAction(Request $request)
    {
        $headers = $request->headers->all();
        $paises = $this->getDoctrine()->getManager()->getRepository('AppBundle:Pais')->encuentraPaises();
        if($headers['origin'][0] === 'http://www.oceandeal.com' || $headers['origin'][0] === 'https://www.oceandeal.com'){
            return $this->json($paises,200,['Access-Control-Allow-Origin'=> $headers['origin'][0]]);
        }else{
            return $this->json(null);
        }
    }

    /**
     * @Route("/anios.json")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function buscarAniosAction(Request $request)
    {
        $headers = $request->headers->all();
        $anios = $this->getDoctrine()->getManager()->getRepository('AppBundle:Embarcacion')->encuentraAniosUnicos();
        if($headers['origin'][0] === 'http://www.oceandeal.com' || $headers['origin'][0] === 'https://www.oceandeal.com'){
            return $this->json($anios,200,['Access-Control-Allow-Origin'=> $headers['origin'][0]]);
        }else{
            return $this->json(null);
        }
    }
}