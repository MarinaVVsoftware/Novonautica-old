<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 21/05/2018
 * Time: 10:58 AM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Embarcacion;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
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
        return $this->compruebaDominio($headers['origin'][0],$embarcaciones);
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
        return $this->compruebaDominio($headers['origin'][0],$marcas);
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
        return $this->compruebaDominio($headers['origin'][0],$paises);
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
        return $this->compruebaDominio($headers['origin'][0],$anios);
    }

    /**
     * @Route("/{id}/brochure.pdf")
     * @Method("GET")
     *
     * @param Embarcacion $embarcacion
     * @param Request $request
     *
     * @return PdfResponse|Response
     */
    public function showBrochureAction(Request $request,Embarcacion $embarcacion)
    {
        $head = $this->renderView('embarcacion/pdf/head.html.twig');
        $body = $this->renderView('embarcacion/pdf/body.html.twig', [
            'title' => 'brochure.pdf',
            'embarcacion' => $embarcacion
        ]);

        $options = [
            'margin-top' => 24,
            'margin-right' => 5,
            'margin-left' => 5,
            'margin-bottom' => 5,
            'header-html' => utf8_decode($head),
        ];

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($body, $options),
            'brochure.pdf', 'application/pdf', 'inline'
        );
    }

    private function compruebaDominio($dominio,$objeto){
        switch ($dominio){
            case 'http://www.oceandeal.com':
                return $this->json($objeto,200,['Access-Control-Allow-Origin'=> $dominio]);
            case 'https://www.oceandeal.com':
                return $this->json($objeto,200,['Access-Control-Allow-Origin'=> $dominio]);
            case 'http://oceandeal.com':
                return $this->json($objeto,200,['Access-Control-Allow-Origin'=> $dominio]);
            case 'https://oceandeal.com':
                return $this->json($objeto,200,['Access-Control-Allow-Origin'=> $dominio]);
            default:
                return $this->json(null);
        }
    }
}