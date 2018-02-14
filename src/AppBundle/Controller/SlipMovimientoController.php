<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Slip;
use AppBundle\Entity\SlipMovimiento;
use AppBundle\Serializer\NotNullObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     * @Route("/mapa/zona-a", name="slip_zona_a")
     */
    public function displayZonaAAdministracion()
    {
        $fechaBuscar = new \DateTime('now');
        $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:SlipMovimiento');
        $totSlips = 176;
        $slipsOcupados = $em->granTotalSlipsOcupados($fechaBuscar);
        $numOcupados = count($slipsOcupados);
        $porcentajeOcupado = ($numOcupados * 100) / $totSlips;
        $dibujoSlip = $em->pintaSlipsMapa($slipsOcupados);
        $ocupacionTiposSlips = $em->calculoOcupaciones($fechaBuscar);
        return $this->render(':marinahumeda/mapa:slip-zona-a.html.twig', [
            'title' => 'Slip Zona A',
            'fechaBuscar' => $fechaBuscar,
            'totSlips' => $totSlips,
            'numOcupados' => $numOcupados,
            'porcentajeOcupado' => $porcentajeOcupado,
            'dibujoSlip' => $dibujoSlip,
            'ocupacionTiposSlips' => $ocupacionTiposSlips
        ]);
    }

    /**
     * @Route("/mapa/zona-b", name="slip_zona_b")
     */
    public function displayZonaBAdministracion()
    {
        $fechaBuscar = new \DateTime('now');
        $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:SlipMovimiento');
        $totSlips = 176;
        $slipsOcupados = $em->granTotalSlipsOcupados($fechaBuscar);
        $numOcupados = count($slipsOcupados);
        $porcentajeOcupado = ($numOcupados * 100) / $totSlips;
        $dibujoSlip = $em->pintaSlipsMapa($slipsOcupados);
        $ocupacionTiposSlips = $em->calculoOcupaciones($fechaBuscar);
        return $this->render(':marinahumeda/mapa:slip-zona-b.html.twig', [
            'title' => 'Slip Zona B',
            'fechaBuscar' => $fechaBuscar,
            'totSlips' => $totSlips,
            'numOcupados' => $numOcupados,
            'porcentajeOcupado' => $porcentajeOcupado,
            'dibujoSlip' => $dibujoSlip,
            'ocupacionTiposSlips' => $ocupacionTiposSlips
        ]);
    }

    /**
     * @Route("/mapa/zona-c", name="slip_zona_c")
     */
    public function displayZonaCAdministracion()
    {
        $fechaBuscar = new \DateTime('now');
        $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:SlipMovimiento');
        $totSlips = 176;
        $slipsOcupados = $em->granTotalSlipsOcupados($fechaBuscar);
        $numOcupados = count($slipsOcupados);
        $porcentajeOcupado = ($numOcupados * 100) / $totSlips;
        $dibujoSlip = $em->pintaSlipsMapa($slipsOcupados);
        $ocupacionTiposSlips = $em->calculoOcupaciones($fechaBuscar);
        return $this->render(':marinahumeda/mapa:slip-zona-c.html.twig', [
            'title' => 'Slip Zona C',
            'fechaBuscar' => $fechaBuscar,
            'totSlips' => $totSlips,
            'numOcupados' => $numOcupados,
            'porcentajeOcupado' => $porcentajeOcupado,
            'dibujoSlip' => $dibujoSlip,
            'ocupacionTiposSlips' => $ocupacionTiposSlips
        ]);
    }

    /**
     * @Route("/mapa/zona-d", name="slip_zona_d")
     */
    public function displayZonaDAdministracion()
    {
        $fechaBuscar = new \DateTime('now');
        $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:SlipMovimiento');
        $totSlips = 176;
        $slipsOcupados = $em->granTotalSlipsOcupados($fechaBuscar);
        $numOcupados = count($slipsOcupados);
        $porcentajeOcupado = ($numOcupados * 100) / $totSlips;
        $dibujoSlip = $em->pintaSlipsMapa($slipsOcupados);
        $ocupacionTiposSlips = $em->calculoOcupaciones($fechaBuscar);
        return $this->render(':marinahumeda/mapa:slip-zona-d.html.twig', [
            'title' => 'Slip Zona D',
            'fechaBuscar' => $fechaBuscar,
            'totSlips' => $totSlips,
            'numOcupados' => $numOcupados,
            'porcentajeOcupado' => $porcentajeOcupado,
            'dibujoSlip' => $dibujoSlip,
            'ocupacionTiposSlips' => $ocupacionTiposSlips
        ]);
    }

    /**
     * @Route("/mapa", name="marina-administracion")
     */
    public function displayMarinaAdministracion()
    {
        $fechaBuscar = new \DateTime('now');
        $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:SlipMovimiento');
        $totSlips = 176;
        $slipsOcupados = $em->granTotalSlipsOcupados($fechaBuscar);
        $numOcupados = count($slipsOcupados);
        $porcentajeOcupado = ($numOcupados * 100) / $totSlips;
        $dibujoSlip = $em->pintaSlipsMapa($slipsOcupados);
        $ocupacionTiposSlips = $em->calculoOcupaciones($fechaBuscar);

        return $this->render('marinahumeda/mapa/marina-administracion.html.twig', [
            'title' => 'Slips',
            'fechaBuscar' => $fechaBuscar,
            'totSlips' => $totSlips,
            'numOcupados' => $numOcupados,
            'porcentajeOcupado' => $porcentajeOcupado,
            'dibujoSlip' => $dibujoSlip,
            'ocupacionTiposSlips' => $ocupacionTiposSlips
        ]);
    }

    /**
     * Lists all slipMovimiento entities.
     *
     * @Route("/ocupacion", name="slipmovimiento_index")
     * @Method("GET")
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
     * @Route("/mapa-slips", name="mapa-slips")
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function currentSlipsAction(Request $request)
    {
        $smRepo = $this->getDoctrine()->getRepository('AppBundle:SlipMovimiento');
        $currentSlips = $smRepo->getCurrentOcupation();

        $porcentajes = [
            46 => [
                'total' => 109,
                'ocupacion' => 0,
                'porcentaje' => 0
            ],
            61 => [
                'total' => 46,
                'ocupacion' => 0,
                'porcentaje' => 0
            ],
            72 => [
                'total' => 13,
                'ocupacion' => 0,
                'porcentaje' => 0
            ],
            120 => [
                'total' => 8,
                'ocupacion' => 0,
                'porcentaje' => 0
            ]
        ];

        foreach ($currentSlips as $slip) {
            $pies = $slip->getSlip()->getPies();
            $porcentajes[$pies]['ocupacion']++;
            $porcentajes[$pies]['porcentaje'] = round(($porcentajes[$pies]['ocupacion'] * 100) / $porcentajes[$pies]['total'], 2);
        }

        if ($request->isXmlHttpRequest()) {
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $normalizer = new ObjectNormalizer($classMetadataFactory);

            $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder(), new XmlEncoder()]);
            $response = $serializer->serialize($currentSlips, 'json', ['groups' => ['currentOcupation']]);
            return new Response($response);
        }

        return $this->render('marinahumeda/mapa/mapa.html.twig', [
            'title' => 'Slips',
            'porcentajes' => $porcentajes
        ]);
    }


    /**
     * @Route("/mapa-slips/{id}/detail", name="detalle-slip")
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function showSlipDetailAction(Request $request, Slip $slip)
    {
        $smRepo = $this->getDoctrine()->getRepository('AppBundle:SlipMovimiento');
        $currentSlips = $smRepo->getCurrentOcupation($slip->getId());

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);

        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $response = $serializer->serialize($currentSlips, 'json', ['groups' => ['currentOcupation']]);
        return new Response($response);
    }

    /**
     * @Route("/mapa-slips/{id}/assign", name="assign-slip")
     *
     * @param Request $request
     * @return Response
     */
    public function createAssignSlipAction(Request $request, Slip $slip)
    {
        $slipMovimiento = new Slipmovimiento();
        $form = $this->createForm('AppBundle\Form\SlipMovimientoType', $slipMovimiento, [
            'action' => $this->generateUrl('assign-slip', ['id' => $slip->getId()])
        ]);
        $form->remove('slip'); // Remover hasta que se acepte este controlador
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $slipMovimiento
                ->setFechaLlegada($slipMovimiento->getMarinahumedacotizacion()->getFechaLlegada())
                ->setFechaSalida($slipMovimiento->getMarinahumedacotizacion()->getFechaSalida())
                ->setSlip($slip)
                ->getMarinahumedacotizacion()->setSlip($slip);

            $em->persist($slipMovimiento);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }
        /*
         * En este caso se hace una validacion para no reasignarle un slip a una cotizacion
         * La validacion se hace desde la entidad SlipMovimiento
         *
         * else {
            return $this->render('marinahumeda/mapa/form/assign-slip.html.twig');
        }*/

        return $this->render('marinahumeda/mapa/form/assign-slip.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/buscar/{eslora}/{id}.{_format}", name="ajax_buscar_slips", defaults={"_format"="JSON"})
     * @Method({"GET"})
     */
    public function buscaSlipActionTodo($eslora, Request $request, Slip $slip)
    {
        $em = $this->getDoctrine()->getManager();
        //$slipsProbables = $em->getRepository('AppBundle:Slip')->findBy(['pies'=>$eslora]);
        $qb = $em->getRepository('AppBundle:Slip')->createQueryBuilder('s');
        $slipsProbables = $qb
            ->where('s.pies >= ' . $eslora)
            ->getQuery()
            ->getResult();
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizer = new ObjectNormalizer();
//        $normalizer->setCircularReferenceLimit(1);
//        $normalizer->setCircularReferenceHandler(function ($object) {
//            return $object->getId();
//        });
        $normalizer->setIgnoredAttributes(['mhcotizaciones', 'movimientos']);
        $normalizers = [$normalizer];
        $serializer = new Serializer($normalizers, $encoders);
        //dump($slipsProbables);
        //dump($serializer->serialize($slipsProbables,$request->getRequestFormat()));
        return new Response($serializer->serialize($slipsProbables, $request->getRequestFormat()));
        //return new Response('');
    }

    /**
     * @Route("/buscar-movimiento/{slip}/{llegada}/{salida}/{id}.{_format}", name="ajax_buscar__movimientos_slip", defaults={"_format"="JSON"})
     * @Method({"GET"})
     */
    public function buscaMovimientoSlipActionTodo($slip, $llegada, $salida, Request $request)
    {
        $dateTime = new DateTimeNormalizer();

        $em = $this->getDoctrine()->getManager();
        //$slipsProbables = $em->getRepository('AppBundle:Slip')->findBy(['pies'=>$eslora]);
        $qb = $em->getRepository('AppBundle:SlipMovimiento')->createQueryBuilder('sm');
        $slipsProbables = $qb
            ->where('sm.slip = :slipcomparar AND ((:fecha_llegada BETWEEN sm.fechaLlegada AND sm.fechaSalida) OR (:fecha_salida BETWEEN sm.fechaLlegada AND sm.fechaSalida))')
            ->getQuery()
            ->setParameter('slipcomparar', $slip)
            ->setParameter('fecha_llegada', new \DateTime($llegada), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('fecha_salida', new \DateTime($salida), \Doctrine\DBAL\Types\Type::DATETIME)
            ->getResult();


        $encoders = [new XmlEncoder(), new JsonEncoder()];

        $normalizer = new ObjectNormalizer();
//        $normalizer->setCircularReferenceLimit(1);
//        $normalizer->setCircularReferenceHandler(function ($object) {
//            return $object->getId();
//        });
        $normalizer->setIgnoredAttributes(['marinahumedacotizacion', 'slip']);

        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $normalizer], $encoders);
        //dump($slipsProbables);
        //dump($serializer->serialize($slipsProbables,$request->getRequestFormat()));
        return new Response($serializer->serialize($slipsProbables, $request->getRequestFormat()));
        //return new Response('');
    }

    /**
     * Creates a new slipMovimiento entity.
     *
     * @Route("/ocupacion/nuevo", name="slipmovimiento_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $slipMovimiento = new Slipmovimiento();
        $form = $this->createForm('AppBundle\Form\SlipMovimientoType', $slipMovimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            $esloraBarco = $slipMovimiento->getMarinahumedacotizacion()->getBarco()->getEslora();
//            $esloraSlip = $slipMovimiento->getSlip()->getPies();
            // Ya no se requiere verificar el tamaño del slip
            /*if ($esloraSlip < $esloraBarco) {
                $this->addFlash(
                    'notice',
                    'Error, el tamaño del slip es menor que la eslora de la embarcación'
                );
            } else {*/
                $em = $this->getDoctrine()->getManager();
                $slip = $slipMovimiento->getSlip()->getId();
                $llegada = $slipMovimiento->getMarinahumedacotizacion()->getFechaLlegada();
                $salida = $slipMovimiento->getMarinahumedacotizacion()->getFechaSalida();
                $slipMovimiento->getMarinahumedacotizacion()->setSlip($slipMovimiento->getSlip());
                $qb = $em->getRepository('AppBundle:SlipMovimiento')->createQueryBuilder('sm');
                $slipsProbables = $qb
                    ->where('sm.slip = :slipcomparar AND ((:fecha_llegada BETWEEN sm.fechaLlegada AND sm.fechaSalida) OR (:fecha_salida BETWEEN sm.fechaLlegada AND sm.fechaSalida))')
                    ->getQuery()
                    ->setParameter('slipcomparar', $slip)
                    ->setParameter('fecha_llegada', $llegada)
                    ->setParameter('fecha_salida', $salida)
                    ->getResult();
                if (empty($slipsProbables)) {
                    $slipMovimiento->setFechaLlegada($llegada)->setFechaSalida($salida);
                    $em->persist($slipMovimiento);
                    $em->flush();
                    return $this->redirectToRoute('slipmovimiento_index');
                } else {
                    $this->addFlash(
                        'notice',
                        'Error, el slip que intenta asignar ya esta ocupado'
                    );
                }
//            }
        }

        return $this->render('slipmovimiento/new.html.twig', array(
            'slipMovimiento' => $slipMovimiento,
            'form' => $form->createView(),
            'title' => 'Asignar Slip'
        ));
    }

    /**
     * Displays a form to edit an existing slipMovimiento entity.
     *
     * @Route("/ocupacion/{id}/editar", name="slipmovimiento_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SlipMovimiento $slipMovimiento)
    {
        $slipActual = $slipMovimiento->getSlip()->getNum();
        $deleteForm = $this->createDeleteForm($slipMovimiento);
        $editForm = $this->createForm('AppBundle\Form\SlipMovimientoType', $slipMovimiento);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $esloraBarco = $slipMovimiento->getMarinahumedacotizacion()->getBarco()->getEslora();
            $esloraSlip = $slipMovimiento->getSlip()->getPies();
            if ($esloraSlip < $esloraBarco) {
                $this->addFlash(
                    'notice',
                    'Error, el tamaño del slip es menor que la eslora de la embarcación'
                );
            } else {
                $em = $this->getDoctrine()->getManager();
                $slip = $slipMovimiento->getSlip()->getId();
                $llegada = $slipMovimiento->getMarinahumedacotizacion()->getFechaLlegada();
                $salida = $slipMovimiento->getMarinahumedacotizacion()->getFechaSalida();
                $qb = $em->getRepository('AppBundle:SlipMovimiento')->createQueryBuilder('sm');
                $slipsProbables = $qb
                    ->where('sm.slip = :slipcomparar AND ((:fecha_llegada BETWEEN sm.fechaLlegada AND sm.fechaSalida) OR (:fecha_salida BETWEEN sm.fechaLlegada AND sm.fechaSalida))')
                    ->getQuery()
                    ->setParameter('slipcomparar', $slip)
                    ->setParameter('fecha_llegada', $llegada)
                    ->setParameter('fecha_salida', $salida)
                    ->getResult();
                if (empty($slipsProbables)) {
                    $slipMovimiento->setFechaLlegada($llegada)->setFechaSalida($salida);
                    $em->persist($slipMovimiento);
                    $em->flush();
                    return $this->redirectToRoute('slipmovimiento_index');
                } else {
                    $this->addFlash(
                        'notice',
                        'Error, el slip que intenta asignar ya esta ocupado'
                    );
                }
            }
        }

        return $this->render('slipmovimiento/edit.html.twig', array(
            'slipMovimiento' => $slipMovimiento,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Editar Slip Asignado',
            'slipActual' => $slipActual
        ));
    }

    /**
     * Deletes a slipMovimiento entity.
     *
//     * @Route("/ocupacion/{id}", name="slipmovimiento_delete")
//     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SlipMovimiento $slipMovimiento)
    {
        $form = $this->createDeleteForm($slipMovimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($slipMovimiento);
            $em->flush();
        }

        return $this->redirectToRoute('slipmovimiento_index');
    }

    /**
     * Creates a form to delete a slipMovimiento entity.
     *
     * @param SlipMovimiento $slipMovimiento The slipMovimiento entity
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(SlipMovimiento $slipMovimiento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('slipmovimiento_delete', array('id' => $slipMovimiento->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
