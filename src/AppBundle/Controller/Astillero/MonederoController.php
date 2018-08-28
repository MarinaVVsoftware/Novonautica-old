<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 27/08/2018
 * Time: 04:32 PM
 */

namespace AppBundle\Controller\Astillero;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\MonederoMovimiento;
use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("astillero/monedero")
 */
class MonederoController extends Controller
{
    /**
     * @Route("/", name="a_monedero_index")
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
                $results = $dataTables->handle($request, 'AMonedero');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }
        return $this->render('marinahumeda/monedero/index.html.twig', [
            'title' => 'Monederos Astillero',
            'index' => 'a_monedero_index'
        ]);
    }

    /**
     * @Route("/{id}", name="a_monedero_show")
     * @Method("GET")
     *
     * @param Request $request
     * @param Cliente $cliente
     *
     * @param DataTablesInterface $dataTables
     * @return JsonResponse|Response
     */
    public function showAction(Request $request, Cliente $cliente, DataTablesInterface $dataTables)
    {
        if($request->isXmlHttpRequest()){
            try {
                $results = $dataTables->handle($request, 'AMonederoMovimiento');
                return $this->json($results);
            } catch (HttpException $e){
                return $this->json($e->getMessage(),$e->getStatusCode());
            }
        }
        return $this->render('marinahumeda/monedero/show.html.twig', [
            'cliente' => $cliente,
            'title' => 'Movimientos Monedero Astillero',
            'index' => 'a_monedero_index',
            'operacion' => 'a_monedero_operacion',
            'show' => 'a_monedero_show',
            'montoActual' => $cliente->getMonederoAstillero(),
            'divisa' => 'MXN',
        ]);
    }

    /**
     * @Route("/{id}/operacion", name="a_monedero_operacion")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Cliente $cliente
     *
     * @return RedirectResponse|Response
     */
    public function movimientoAction(Request $request, Cliente $cliente)
    {
        $movimiento = new MonederoMovimiento();
        $movimiento->setCliente($cliente);
        $montoActual = $cliente->getMonederoAstillero();
        $form = $this->createForm('AppBundle\Form\MonederoType', $movimiento);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $montoProcesar = $movimiento->getMonto();
            $operacion = $movimiento->getOperacion();
            // 1 = suma, 2 = resta
            $montoTotal = $operacion===1 ? $montoActual + $montoProcesar:$montoActual - $montoProcesar;
            if($montoTotal<0){
                $this->addFlash('notice',
                    'Error! la cantidad que se intenta restar es mayor que el monto actual del monedero.');
            }else{
                $movimiento
                    ->setFecha(new \DateTime('now'))
                    ->setResultante($montoTotal)
                    ->setTipo(2);
                $cliente->setMonederoAstillero($montoTotal);
                $em->persist($movimiento);
                $em->persist($cliente);
                $em->flush();
                return $this->redirectToRoute('a_monedero_show',['id'=>$cliente->getId()]);
            }
        }
        return $this->render('marinahumeda/monedero/operacion.html.twig', [
            'monederoMovimiento' => $movimiento,
            'title' => 'Movimiento Monedero Astillero',
            'form' => $form->createView(),
            'montoActual' =>  $montoActual,
            'divisa' => 'MXN',
            'show' => 'a_monedero_show'
        ]);
    }

}