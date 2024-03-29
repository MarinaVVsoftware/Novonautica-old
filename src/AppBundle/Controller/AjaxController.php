<?php
/**
 * Created by PhpStorm.
 * User: Luis
 * Date: 16/10/2017
 * Time: 11:36 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Barco;
use AppBundle\Entity\Cliente;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


/**
 * Ajax controller.
 *
 * @Route("ajax")
 */
class AjaxController extends Controller
{

    /**
     * @Route("/buscacliente", name="ajax_busca_cliente")
     * @Method({"GET"})
     */
    public function buscaClienteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $encoders = array(new JsonEncoder());
//            $normalizers = array(new ObjectNormalizer());

            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceLimit(1);
            // Add Circular reference handler
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            $normalizers = array($normalizer);

            $serializer = new Serializer($normalizers, $encoders);

            $em = $this->getDoctrine()->getManager();
            $clientes = $em->getRepository('AppBundle:Cliente')->find($request->get('id'));


            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(array(
                'response' => 'success',
                'posts' => $serializer->serialize($clientes, 'json')
            ));
            return $response;
        } else {
            return 'no';
        }
    }

    /**
     * @Route("/buscabarco", name="ajax_busca_barco")
     * @Method({"GET"})
     */
    public function buscaBarcoAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $encoders = array(new JsonEncoder());
            //$normalizers = array(new ObjectNormalizer());

            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceLimit(1);
            // Add Circular reference handler
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            $normalizers = array($normalizer);

            $serializer = new Serializer($normalizers, $encoders);

            $em = $this->getDoctrine()->getManager();
            $barcos = $em->getRepository('AppBundle:Barco')->find($request->get('id'));

            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(array(
                'response' => 'success',
                'posts' => $serializer->serialize($barcos, 'json')
            ));
            return $response;
        }
    }

    /**
     * @Route("/buscaeventos", name="ajax_busca_eventos")
     * @Method({"GET"})
     */
    public function buscaEventosAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $em = $this->getDoctrine()->getManager();
            $eventos = $em->getRepository('AppBundle:Evento')->findAll();

            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(array(
                'response' => 'success',
                'posts' => $serializer->serialize($eventos, 'json')
            ));
            return $response;
        }
    }

    /**
     * @Route("/buscaclientetodo/{id}.{_format}", name="ajax_busca_cliente_todo", defaults={"_format"="JSON"})
     * @Method({"GET"})
     */
    public function buscaClienteActionTodo(Request $request, Cliente $cliente)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];

        $normalizer = new ObjectNormalizer();

        $normalizer->setIgnoredAttributes([
            'cliente',
            'barco',
            'password',
            'estatus',
            'razonesSociales',
            'monederomovimientos',
            'monederomarinahumeda',
            'mHcotizaciones',
            'mhcotizacionesadicionales',
            'astilleroCotizaciones',
            'astillerocotizaciones',
            'embarcacion',
        ]);

        $normalizers = [new DateTimeNormalizer(), $normalizer];
        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($cliente, $request->getRequestFormat()));
    }

    /**
     * @Route("/buscabarcotodo/{id}", name="ajax_busca_barco_todo")
     * @Method({"GET"})
     */
    public function buscaBarcoActionTodo(Request $request, Barco $barco)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);

        $response = $serializer->normalize($barco, null, ['groups' => ['cotizaciones']]);

        return $this->json($response);
    }

}

