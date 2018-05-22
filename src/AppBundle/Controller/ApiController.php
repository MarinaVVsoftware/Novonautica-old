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
        $embarcaciones = $this->getDoctrine()->getManager()->getRepository('AppBundle:Embarcacion')->filtrarBarcos($request);
        return $this->json($embarcaciones);
    }

    /**
     * @Route("/marcas.json")
     * @Method("GET")
     *
     * @return Response
     */
    public function buscarMarcasAction()
    {
        $marcas = $this->getDoctrine()->getManager()->getRepository('AppBundle:EmbarcacionMarca')->encuentraMarcas();
        return $this->json($marcas);
    }

    /**
     * @Route("/paises.json")
     * @Method("GET")
     *
     * @return Response
     */
    public function buscarPaisesAction()
    {
        $paises = $this->getDoctrine()->getManager()->getRepository('AppBundle:Pais')->encuentraPaises();
        return $this->json($paises);
    }

    /**
     * @Route("/anios.json")
     * @Method("GET")
     *
     * @return Response
     */
    public function buscarAniosAction()
    {
        $anios = $this->getDoctrine()->getManager()->getRepository('AppBundle:Embarcacion')->encuentraAniosUnicos();
        return $this->json($anios);
    }

}