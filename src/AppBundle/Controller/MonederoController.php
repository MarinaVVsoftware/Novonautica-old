<?php
/**
 * Created by PhpStorm.
 * User: Luis
 * Date: 09/11/2017
 * Time: 09:30 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\MonederoMovimiento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Monedero controller.
 *
 * @Route("monedero")
 */
class MonederoController extends Controller
{
    /**
     * Lists all cliente entities.
     *
     * @Route("/", name="monedero_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientes = $em->getRepository('AppBundle:Cliente')->findAll();

        return $this->render('monedero/index.html.twig', array(
            'clientes' => $clientes,
            'monederoMenu' => 1
        ));
    }

    /**
     * Finds and displays a marinaHumedaCotizacion entity.
     *
     * @Route("/{id}", name="monedero_ver")
     * @Method("GET")
     */
    public function showAction(Cliente $cliente)
    {

        return $this->render('monedero/show.html.twig', array(
            'cliente' => $cliente,
            'monederoMenu' => 1
        ));
    }


    /**
     * Displays a form to edit an existing cliente entity.
     *
     * @Route("/{id}/editar", name="monedero_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cliente $cliente)
    {
        $editForm = $this->createForm('AppBundle\Form\MonederoType', $cliente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('monedero_index');
        }
        return $this->render('monedero/edit.html.twig', array(
            'cliente' => $cliente,
            'edit_form' => $editForm->createView(),
            'monederoMenu' => 1,
        ));
    }

    /**
     *
     * @Route("/{id}/operacion", name="monedero_operacion")
     * @Method({"GET", "POST"})
     */
    public function movimientoAction(Request $request,Cliente $cliente)
    {
        $monederoMovimiento = new MonederoMovimiento();

        $monederoMovimiento->setCliente($cliente);

        $form = $this->createForm('AppBundle\Form\MonederoType', $monederoMovimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $montoTotal = 0;
            $montoActual = $cliente->getMontomonedero();
            $montoProcesar = $monederoMovimiento->getMonto();
            $operacion = $monederoMovimiento->getOperacion();

            if($operacion==1){
                $montoTotal = $montoActual + $montoProcesar;
            }else{
                if($operacion==2){
                    $montoTotal = $montoActual - $montoProcesar;
                }else{
                    $montoTotal = $montoActual;
                }
            }
            //-------------------------------------------------
            $fechaHoraActual = new \DateTime('now');
            $monederoMovimiento
                ->setFecha($fechaHoraActual)
                ->setResultante($montoTotal);
            $cliente->setMontomonedero($montoTotal);

            $em->persist($monederoMovimiento);
            $em->persist($cliente);
            $em->flush();


            return $this->redirectToRoute('monedero_index');
        }
        return $this->render('monedero/operacion.html.twig', array(
            'monederoMovimiento' => $monederoMovimiento,
            'form' => $form->createView(),
            'monederoMenu' => 1,
        ));
    }

}