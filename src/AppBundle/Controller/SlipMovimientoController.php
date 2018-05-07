<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\Slip;
use AppBundle\Entity\SlipMovimiento;
use AppBundle\Serializer\NotNullObjectNormalizer;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Slipmovimiento controller.
 *
 * @Route("marina/slip")
 */
class SlipMovimientoController extends Controller
{
    /**
     * Lists all slipMovimiento entities.
     *
     * @Route("/ocupacion", name="slipmovimiento_index")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $datatables = $this->get('datatables');
                $results = $datatables->handle($request, 'SlipMovimiento');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getCode());
            }
        }

        return $this->render('slipmovimiento/index.html.twig', ['title' => 'Movimientos de Slips']);
    }

    /**
     * @Route("/mapa", name="mapa")
     * @Method("GET")
     */
    public function currentSlipsAction()
    {
        return $this->render('marinahumeda/mapa/mapa.html.twig', [
            'title' => 'Slips',
        ]);
    }

    /**
     * @Route("/mapa-slips/data", name="mapa-data")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function fillMapAction(Request $request)
    {
        $fecha = $request->request->get('f') ? $request->request->get('f') : new \DateTime();

        $repository = $this->getDoctrine()->getRepository('AppBundle:SlipMovimiento');
        $currentSlips = $repository->getCurrentOcupation();
        $stats = $repository->getCurrentOcupationStats($fecha);


        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder()]);

        $data = [
            'data' => $stats,
            'boats' => $currentSlips,
        ];

        try {
            $response = $serializer->serialize($data, 'json', ['groups' => ['currentOcupation']]);
            return JsonResponse::fromJsonString($response)->setEncodingOptions(JSON_NUMERIC_CHECK);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/mapa/{slip}/detail", name="detalle-slip")
     *
     * @param $slip
     *
     * @return Response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function showSlipDetailAction($slip)
    {
        $smRepo = $this->getDoctrine()->getRepository('AppBundle:SlipMovimiento');
        $currentSlips = $smRepo->getCurrentOcupation($slip);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);

        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $response = $serializer->serialize($currentSlips, 'json', ['groups' => ['currentOcupation']]);
        return new Response($response);
    }

    /**
     * @Route("/mapa/{id}/assign", name="assign-slip")
     *
     * @param Request $request
     * @param Slip $slip
     *
     * @return Response
     */
    public function createAssignSlipAction(Request $request, Slip $slip)
    {
        $slipMovimiento = new Slipmovimiento();
        $form = $this->createForm('AppBundle\Form\SlipMovimientoType', $slipMovimiento, [
            'action' => $this->generateUrl('assign-slip', ['id' => $slip->getId()])
        ]);
        $form->remove('slip');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $cotizacion = $slipMovimiento->getMarinahumedacotizacion();
            $fechaLlegada = $cotizacion->getFechaLlegada();
            $fechaSalida = $cotizacion->getFechaSalida();
            $smExists = $em->getRepository('AppBundle:SlipMovimiento')
                ->isSlipOpen($slip->getId(), $fechaLlegada, $fechaSalida);

            if ($smExists) {
                $this->addFlash('danger', 'La cotización asignada al slip, coincide con otra cotización');
                return $this->redirect($request->headers->get('referer'));
            }

            $slipMovimiento
                ->setFechaLlegada($slipMovimiento->getMarinahumedacotizacion()->getFechaLlegada())
                ->setFechaSalida($slipMovimiento->getMarinahumedacotizacion()->getFechaSalida())
                ->setSlip($slip)
                ->getMarinahumedacotizacion()->setSlip($slip);

            $em->persist($slipMovimiento);
            $em->flush();

            // TODO: No redireccionar, solo enviar una respuesta de movimiento creado
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('marinahumeda/mapa/form/assign-slip.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mapa/{id}/relocate", name="relocate-slip")
     *
     * @param Request $request
     * @param SlipMovimiento $slipMovimiento
     *
     * @return Response
     */
    public function relocateSlipAction(Request $request, SlipMovimiento $slipMovimiento)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(
            'AppBundle\Form\SlipMovimientoType',
            $slipMovimiento,
            [
                'action' => $this->generateUrl(
                    'relocate-slip',
                    [
                        'id' => $slipMovimiento->getId(),
                    ]
                )
            ]);

        $form->remove('marinahumedacotizacion');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slip = $slipMovimiento->getSlip();
            $slipMovimiento->getMarinahumedacotizacion()->setSlip($slip);

            $em->persist($slipMovimiento);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('marinahumeda/mapa/form/assign-slip.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mapa/{slip}/isopen", name="check-open-slip")
     *
     * @param Request $request
     * @param string $slip
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function checkOpenSlipAction(Request $request, $slip)
    {
        $start = $request->request->get('start');
        $end = $request->request->get('end');

        // TODO: Mostrar el slip si esta ocupado

        $smRepo = $this->getDoctrine()->getRepository('AppBundle:SlipMovimiento');

        try {
            $openSlip = $smRepo->isSlipOpen($slip, $start, $end);
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $normalizer = new ObjectNormalizer($classMetadataFactory);
            $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder()]);

            $response = $serializer->serialize($openSlip, 'json', ['groups' => ['currentOcupation']]);

            return JsonResponse::fromJsonString($response);
        } catch (NonUniqueResultException $e) {
            return $this->json($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/mapa/{id}/remove", name="remove-slip")
     * @Method("DELETE")
     *
     * @param SlipMovimiento $slipMovimiento
     *
     * @return Response
     */
    public function removeSlipAction(SlipMovimiento $slipMovimiento)
    {
        $em = $this->getDoctrine()->getManager();

        $slipMovimiento->getMarinahumedacotizacion()->setSlip(null);

        $em->remove($slipMovimiento);
        $em->flush();

        return new Response(null, Response::HTTP_CREATED);
    }
}
