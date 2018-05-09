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
use Symfony\Component\Form\FormInterface;
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
        $fecha = $request->query->get('f')
            ? \DateTime::createFromFormat('d/m/Y', $request->query->get('f'))
            : new \DateTime();

        $repository = $this->getDoctrine()->getRepository('AppBundle:SlipMovimiento');
        $currentSlips = $repository->getCurrentOcupation($fecha);
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
     * @param Request $request
     * @param $slip
     *
     * @return Response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function showSlipDetailAction(Request $request, $slip)
    {
        $cotizacion = $request->query->get('cotizacion');

        $smRepo = $this->getDoctrine()->getRepository('AppBundle:SlipMovimiento');
        $currentSlips = $smRepo->getSlipInformation($slip, $cotizacion);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);

        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $response = $serializer->serialize($currentSlips, 'json', ['groups' => ['currentOcupation']]);

        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/mapa/{id}/assign", name="assign-slip")
     *
     * @param Request $request
     * @param Slip $slip
     *
     * @return Response
     * @throws NonUniqueResultException
     */
    public function createAssignSlipAction(Request $request, Slip $slip)
    {
        $slipMovimiento = new Slipmovimiento();
        $form = $this->createForm('AppBundle\Form\SlipMovimientoType', $slipMovimiento, [
            'action' => $this->generateUrl('assign-slip', ['id' => $slip->getId()])
        ]);
        $form->remove('slip');
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->json([
                'code' => Response::HTTP_BAD_REQUEST,
                'type' => 'validation',
                'errors' => $this->getErrorsFromForm($form),
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $cotizacion = $slipMovimiento->getMarinahumedacotizacion();
            $fechaLlegada = $cotizacion->getFechaLlegada();
            $fechaSalida = $cotizacion->getFechaSalida();
            $smExists = $em->getRepository('AppBundle:SlipMovimiento')
                ->isSlipOpen($slip->getId(), $fechaLlegada, $fechaSalida);

            if ($smExists) {
                return $this->json([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'type' => 'validation',
                    'message' => ['La cotización asignada al slip, coincide con otra cotización']
                ], Response::HTTP_BAD_REQUEST);
            }

            $slipMovimiento
                ->setFechaLlegada($slipMovimiento->getMarinahumedacotizacion()->getFechaLlegada())
                ->setFechaSalida($slipMovimiento->getMarinahumedacotizacion()->getFechaSalida())
                ->setSlip($slip)
                ->getMarinahumedacotizacion()->setSlip($slip);

            $em->persist($slipMovimiento);
            $em->flush();

            return $this->json('', Response::HTTP_CREATED);
        }

        return $this->render('marinahumeda/mapa/form/assign-slip.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mapa/{id}/lock", name="lock-slip")
     *
     * @param Request $request
     * @param Slip $slip
     *
     * @return Response
     * @throws NonUniqueResultException
     */
    public function createLockSlipAction(Request $request, Slip $slip)
    {
        $slipMovimiento = new Slipmovimiento();
        $form = $this->createForm('AppBundle\Form\SlipMovimientoLockType', $slipMovimiento, [
            'action' => $this->generateUrl('lock-slip', ['id' => $slip->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $smExists = $em->getRepository('AppBundle:SlipMovimiento')
                ->isSlipOpen($slip->getId(), $slipMovimiento->getFechaLlegada(), $slipMovimiento->getFechaSalida());

            if ($smExists) {
                return $this->json([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'type' => 'validation',
                    'message' => ['No se puede bloquear este slip, coincide con una cotización']
                ], Response::HTTP_BAD_REQUEST);
            }

            $slipMovimiento
                ->setSlip($slip);

            $em->persist($slipMovimiento);
            $em->flush();

            return $this->json('', Response::HTTP_CREATED);
        }

        return $this->render('marinahumeda/mapa/form/lock-slip.html.twig', [
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
        $start = \DateTime::createFromFormat('d-m-Y', $request->query->get('start'));
        $end = \DateTime::createFromFormat('d-m-Y', $request->query->get('end'));

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

        if (null === $slipMovimiento->getNota()) {
            $slipMovimiento->getMarinahumedacotizacion()->setSlip(null);
        }

        $em->remove($slipMovimiento);
        $em->flush();

        return new Response(null, Response::HTTP_CREATED);
    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }
}
