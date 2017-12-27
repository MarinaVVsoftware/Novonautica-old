<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\Slip;
use AppBundle\Entity\SlipMovimiento;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Slipmovimiento controller.
 *
 * @Route("marina/slip", name="marina-slip")
 */
class SlipMovimientoController extends Controller
{
    /**
     * @Route("/mapa", name="marina-administracion")
     */
    public function displayMarinaAdministracion()
    {
        return $this->render('marinahumeda/mapa/marina-administracion.twig', [
            'title' => 'Slip'
        ]);
    }
    /**
     * @Route("/mapa/zona-a", name="slip_zona_a")
     */
    public function displayZonaAAdministracion()
    {
        return $this->render('marinahumeda/mapa/slip-zona-a.twig', [
            'title' => 'Slip Zona A'
        ]);
    }
    /**
     * @Route("/mapa/zona-b", name="slip_zona_b")
     */
    public function displayZonaBAdministracion()
    {
        return $this->render('marinahumeda/mapa/slip-zona-b.twig', [
            'title' => 'Slip Zona B'
        ]);
    }
    /**
     * @Route("/mapa/zona-c", name="slip_zona_c")
     */
    public function displayZonaCAdministracion()
    {
        return $this->render('marinahumeda/mapa/slip-zona-c.twig', [
            'title' => 'Slip Zona C'
        ]);
    }
    /**
     * @Route("/mapa/zona-d", name="slip_zona_d")
     */
    public function displayZonaDAdministracion()
    {
        return $this->render('marinahumeda/mapa/slip-zona-d.twig', [
            'title' => 'Slip Zona D'
        ]);
    }

    /**
     * @Route("/buscar/{eslora}/{id}.{_format}", name="ajax_buscar_slips", defaults={"_format"="JSON"})
     * @Method({"GET"})
     */
    public function buscaSlipActionTodo($eslora,Request $request, Slip $slip)
    {
        $em = $this->getDoctrine()->getManager();
        //$slipsProbables = $em->getRepository('AppBundle:Slip')->findBy(['pies'=>$eslora]);
        $qb = $em->getRepository('AppBundle:Slip')->createQueryBuilder('s');
        $slipsProbables = $qb
                            ->where('s.pies >= '.$eslora)
                            ->getQuery()
                            ->getResult();
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizer = new ObjectNormalizer();
//        $normalizer->setCircularReferenceLimit(1);
//        $normalizer->setCircularReferenceHandler(function ($object) {
//            return $object->getId();
//        });
        $normalizer->setIgnoredAttributes(['mhcotizaciones','movimientos']);
        $normalizers = [$normalizer];
        $serializer = new Serializer($normalizers, $encoders);
        //dump($slipsProbables);
        //dump($serializer->serialize($slipsProbables,$request->getRequestFormat()));
        return new Response($serializer->serialize($slipsProbables,$request->getRequestFormat()));
        //return new Response('');
    }

    /**
     * @Route("/buscar-movimiento/{slip}/{llegada}/{salida}/{id}.{_format}", name="ajax_buscar__movimientos_slip", defaults={"_format"="JSON"})
     * @Method({"GET"})
     */
    public function buscaMovimientoSlipActionTodo($slip,$llegada,$salida,Request $request)
    {
        $dateTime = new DateTimeNormalizer();

        $em = $this->getDoctrine()->getManager();
        //$slipsProbables = $em->getRepository('AppBundle:Slip')->findBy(['pies'=>$eslora]);
        $qb = $em->getRepository('AppBundle:SlipMovimiento')->createQueryBuilder('sm');
        $slipsProbables = $qb
            ->where('sm.slip = :slipcomparar AND ((:fecha_llegada BETWEEN sm.fechaLlegada AND sm.fechaSalida) OR (:fecha_salida BETWEEN sm.fechaLlegada AND sm.fechaSalida))')
            ->getQuery()
            ->setParameter('slipcomparar',$slip)
            ->setParameter('fecha_llegada', new \DateTime($llegada), \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('fecha_salida', new \DateTime($salida), \Doctrine\DBAL\Types\Type::DATETIME)
            ->getResult()
        ;


        $encoders = [new XmlEncoder(), new JsonEncoder()];

        $normalizer = new ObjectNormalizer();
//        $normalizer->setCircularReferenceLimit(1);
//        $normalizer->setCircularReferenceHandler(function ($object) {
//            return $object->getId();
//        });
        $normalizer->setIgnoredAttributes(['marinahumedacotizacion','slip']);

        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $normalizer], $encoders);
        //dump($slipsProbables);
        //dump($serializer->serialize($slipsProbables,$request->getRequestFormat()));
        return new Response($serializer->serialize($slipsProbables,$request->getRequestFormat()));
        //return new Response('');
    }

    /**
     * Lists all slipMovimiento entities.
     *
     * @Route("/ocupacion", name="slipmovimiento_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $smovRepositorio = $em->getRepository('AppBundle:SlipMovimiento');
        $slipMovimientos = $smovRepositorio->ordenaFechasEstadia();

        return $this->render('slipmovimiento/index.html.twig', array(
            'slipMovimientos' => $slipMovimientos,
            'title' => 'Movimientos Slip'
        ));
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
            $em = $this->getDoctrine()->getManager();

            $fll = $slipMovimiento->getMarinahumedacotizacion()->getFechaLlegada();
            $fs = $slipMovimiento->getMarinahumedacotizacion()->getFechaSalida();
            $slipMovimiento->setFechaLlegada($fll)->setFechaSalida($fs);
            $em->persist($slipMovimiento);
            $em->flush();

            return $this->redirectToRoute('slipmovimiento_index');
        }

        return $this->render('slipmovimiento/new.html.twig', array(
            'slipMovimiento' => $slipMovimiento,
            'form' => $form->createView(),
            'title' => 'Asignar Slip'
        ));
    }

    /**
     * Finds and displays a slipMovimiento entity.
     *
     * @Route("/ocupacion/{id}", name="slipmovimiento_show")
     * @Method("GET")
     */
    public function showAction(SlipMovimiento $slipMovimiento)
    {
        $deleteForm = $this->createDeleteForm($slipMovimiento);

        return $this->render('slipmovimiento/show.html.twig', array(
            'slipMovimiento' => $slipMovimiento,
            'delete_form' => $deleteForm->createView(),
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
        $deleteForm = $this->createDeleteForm($slipMovimiento);
        $editForm = $this->createForm('AppBundle\Form\SlipMovimientoType', $slipMovimiento);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('slipmovimiento_index');
        }

        return $this->render('slipmovimiento/edit.html.twig', array(
            'slipMovimiento' => $slipMovimiento,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Editar Slip Asignado'
        ));
    }

    /**
     * Deletes a slipMovimiento entity.
     *
     * @Route("/ocupacion/{id}", name="slipmovimiento_delete")
     * @Method("DELETE")
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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SlipMovimiento $slipMovimiento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('slipmovimiento_delete', array('id' => $slipMovimiento->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
