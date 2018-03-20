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
use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Monedero controller.
 *
 * @Route("/marina/monedero")
 */
class MarinaHumedaMonederoController extends Controller
{
    /**
     * Lista de todos los monederos
     *
     * @Route("/", name="mh_monedero_index")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'MHCMonedero');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }

        return $this->render('marinahumeda/monedero/index.html.twig', [
            'title' => 'Monedero',
        ]);
    }

    /**
     * Muestra un monedero en especifico
     *
     * @Route("/{id}", name="mh_monedero_ver")
     * @Method("GET")
     *
     * @param Cliente $cliente
     *
     * @return Response
     */
    public function showAction(Request $request, Cliente $cliente, DataTablesInterface $dataTables)
    {
        if($request->isXmlHttpRequest()){
            try {
                $results = $dataTables->handle($request, 'MHCMonederoMovimiento');
                return $this->json($results);
            } catch (HttpException $e){
                return $this->json($e->getMessage(),$e->getStatusCode());
            }
        }
        return $this->render('marinahumeda/monedero/show.html.twig', [
            'cliente' => $cliente,
            'title' => 'Movimientos Monedero Cliente'
        ]);
    }


    /**
     * Edita un monedero
     *
     * @Route("/{id}/editar", name="mh_monedero_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Cliente $cliente
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Cliente $cliente)
    {
        $editForm = $this->createForm('AppBundle\Form\MonederoType', $cliente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mh_monedero_ver',['cliente'=>$cliente]);
        }
        return $this->render('marinahumeda/monedero/edit.html.twig', array(
            'cliente' => $cliente,
            'edit_form' => $editForm->createView(),
            'monederoMenuMh' => 1,
        ));
    }

    /**
     * @Route("/{id}/operacion", name="mh_monedero_operacion")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Cliente $cliente
     *
     * @return RedirectResponse|Response
     */
    public function movimientoAction(Request $request, Cliente $cliente)
    {
        $monederoMovimiento = new MonederoMovimiento();

        $monederoMovimiento->setCliente($cliente);

        $form = $this->createForm('AppBundle\Form\MonederoType', $monederoMovimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $montoTotal = 0;
            $montoActual = $cliente->getMonederomarinahumeda();
            $montoProcesar = $monederoMovimiento->getMonto();
            $operacion = $monederoMovimiento->getOperacion();

            if($operacion==1){ //suma
                $montoTotal = $montoActual + $montoProcesar;
            }else{
                if($operacion==2){ //resta
                        $montoTotal = $montoActual - $montoProcesar;
                }else{
                    $montoTotal = $montoActual;
                }
            }
            //-------------------------------------------------
            if($montoTotal<0){
                //error se restara mas de lo que se tiene $this->addFlash(
                $this->addFlash('notice', 'Error! la cantidad que se resta es mayor que el monto que se tiene');
            }else{
                $fechaHoraActual = new \DateTime('now');
                $monederoMovimiento
                    ->setFecha($fechaHoraActual)
                    ->setResultante($montoTotal)
                    ->setTipo(1);
                $cliente->setMonederomarinahumeda($montoTotal);

                $em->persist($monederoMovimiento);
                $em->persist($cliente);
                $em->flush();


                return $this->redirectToRoute('mh_monedero_ver',['id'=>$cliente->getId()]);
            }


        }
        return $this->render('marinahumeda/monedero/operacion.html.twig', array(
            'monederoMovimiento' => $monederoMovimiento,
            'form' => $form->createView(),
            'monederoMenuMh' => 1,
        ));
    }

}