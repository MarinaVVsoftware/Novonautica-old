<?php

namespace AppBundle\Controller\Tienda;

use AppBundle\Entity\MonederoMovimiento;
use AppBundle\Entity\Tienda\Peticion;
use AppBundle\Entity\Tienda\Solicitud;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Solicitud controller.
 *
 * @Route("tienda/solicitud")
 */
class SolicitudController extends Controller
{
    /**
     * Lists all solicitud entities.
     *
     * @Route("/", name="tienda_solicitud_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('tienda/solicitud/index.html.twig', ['title' => 'Tienda']);
    }

    /**
     * @Route("/solicitudes", name="tienda_solicitud_index_data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUsuariosDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'tienda');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Creates a new solicitud entity.
     *
     * @Route("/new", name="tienda_solicitud_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine();

        $solicitud = new Solicitud();

        $this->denyAccessUnlessGranted('TIENDA_CREATE', $solicitud);

        $producto = new Peticion();

        $valorsistema = $em->getRepository('AppBundle:ValorSistema')->find(1);
        $valordolar = $valorsistema->getDolar();

        $solicitud->setFolio($valorsistema->getFolioMarina());
        $solicitud->addProducto($producto);

        $form = $this->createForm('AppBundle\Form\Tienda\SolicitudType', $solicitud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $solicitud->setValordolar($valordolar);
            $total = $solicitud->getTotal();
            $totalfinal = ($total / $valordolar) * 100;
            $solicitud->setTotalusd($totalfinal);

            $valorsistema->setFolioMarina($valorsistema->getFolioMarina() + 1);

            $em->persist($solicitud);
            $em->flush();

            return $this->redirectToRoute('tienda_solicitud_index');
        }

        return $this->render('tienda/solicitud/new.html.twig', array(
            'title' => 'Nueva Solicitud',
            'solicitud' => $solicitud,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/nopagado/{id}", name="tienda_solicitud_nopagado")
     * @Method({"GET", "POST"})
     */
    public function rechazarrAction(Solicitud $solicitud)
    {
        $em = $this->getDoctrine()->getManager();
        $solicitudes = $em->getRepository('AppBundle:Tienda\Solicitud');
        if ($solicitud->getPagado() >= 2){
            $solicitudes->validarSolicitud($solicitud->getId(), 0, 's.pagado');
        }
        return $this->redirectToRoute('tienda_solicitud_index');
    }

    /**
     * @Route("/noentregado/{id}", name="tienda_solicitud_noentregado")
     * @Method({"GET", "POST"})
     */
    public function noentregadoAction(Solicitud $solicitud)
    {
        $em = $this->getDoctrine()->getManager();
        $solicitudes = $em->getRepository('AppBundle:Tienda\Solicitud');
        if ($solicitud->getEntregado() >= 2){
            $solicitudes->validarSolicitud($solicitud->getId(), 0, 's.entregado');
        }
        return $this->redirectToRoute('tienda_solicitud_index');
    }

    /**
     * @Route("/entregar/{id}", name="tienda_solicitud_entregar")
     * @Method({"GET", "POST"})
     */
    public function entregarAction(Solicitud $solicitud)
    {
        $em = $this->getDoctrine()->getManager();
        $solicitudes = $em->getRepository('AppBundle:Tienda\Solicitud');
        if ($solicitud->getEntregado() >= 2){
            $solicitudes->validarSolicitud($solicitud->getId(), 1, 's.entregado');
        }elseif ($solicitud->getEntregado() == 1) {
            $solicitudes->validarSolicitud($solicitud->getId(), 0, 's.entregado');
        }elseif ($solicitud->getEntregado() == 0) {
            $solicitudes->validarSolicitud($solicitud->getId(), 1, 's.entregado');
        }
        return $this->redirectToRoute('tienda_solicitud_index');
    }

    ///////////////////////////   TIENDA PAGOS //////////////////////////////
    ///
    /**
     * @Route("/{id}/pago", name="solicitud_pago_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Solicitud $solicitud
     * @return RedirectResponse|Response
     */
    public function editPagoAction(Request $request, Solicitud $solicitud)
    {
        $totPagado = 0;
        $totPagadoMonedero = 0;
        $listaPagos = new ArrayCollection();

        foreach ($solicitud->getPagos() as $pago) {
            if($pago->getDivisa()=='MXN'){
                $pesos = ($pago->getCantidad()*$pago->getDolar())/100;
                $pago->setCantidad($pesos);
            }
            $listaPagos->add($pago);
        }

        $form = $this->createForm('AppBundle\Form\Tienda\TiendaRegistraPagoType', $solicitud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $continuarpago = true;
            $total = $solicitud->getTotalusd();
            $pagado = $solicitud->getPagado();
            $monedero = $solicitud->getNombrebarco()->getCliente()->getMonederomarinahumeda();

            $em = $this->getDoctrine()->getManager();

            foreach ($listaPagos as $pago) {
                if (false === $solicitud->getPagos()->contains($pago)) {
                    $pago->getTiendaSolicitud()->removePago($pago);
                    $em->persist($pago);
                    $em->remove($pago);
                }
            }

            foreach ($solicitud->getPagos() as $pago) {
                if($pago->getDivisa()=='MXN'){
                    $unpago = ($pago->getCantidad()/$pago->getDolar())*100;
                    $pago->setCantidad($unpago);

                }else{
                    $unpago = $pago->getCantidad();
                }
                $totPagado += $unpago;

                if($pago->getMetodopago() == 'Monedero' && $pago->getId() == null){
                    $totPagadoMonedero += $unpago;
                    $monederotot = $monedero - $totPagadoMonedero;
                    $notaMonedero = 'Pago de articulos de la tienda';

                    $fechaHoraActual = new \DateTime('now');
                    $monederoMovimiento = new MonederoMovimiento();
                    $monederoMovimiento
                        ->setCliente($solicitud->getNombrebarco()->getCliente())
                        ->setFecha($fechaHoraActual)
                        ->setMonto($unpago)
                        ->setOperacion(2)
                        ->setResultante($monederotot)
                        ->setTipo(3)
                        ->setDescripcion($notaMonedero);
                    $em->persist($monederoMovimiento);
                }
            }
            if (($total + 1) < $totPagado) {
                $this->addFlash('notice', 'Error! Se ha intentado pagar más del total');
            } else {
                if ($monedero < $totPagadoMonedero) {
                    $this->addFlash('notice', 'Error! Fondos insuficientes en el monedero');
                } else {
                    $faltante = $total - $totPagado;
                    if ($faltante <= 0.5) {
                        $solicitud->setRegistroPagoCompletado(new \DateTimeImmutable());
                        $solicitud->setPagado(1);
                    } else {
                        $solicitud->setPagado(0);
                    }
                    $monederoRestante =  $monedero - $totPagadoMonedero;
                    $solicitud->setCantidadpagado($totPagado);
                    $solicitud->getNombrebarco()->getCliente()->setMonederomarinahumeda($monederoRestante);
                    $em->persist($solicitud);
                    $em->flush();
                    return $this->redirectToRoute('tienda_solicitud_ver', ['id' => $solicitud->getId()]);
                }
            }
        }

        return $this->render('tienda/pagos/edit.html.twig', [
            'solicitud' => $solicitud,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing solicitud entity.
     *
     * @Route("/{id}", name="tienda_solicitud_ver")
     * @Method({"GET"})
     *
     * @param Solicitud $solicitud
     *
     * @return Response
     */
    public function editAction(Solicitud $solicitud)
    {
        $solicitud = $this->getDoctrine()
            ->getRepository('AppBundle:Tienda\Solicitud')->find($solicitud->getId());

        return $this->render('tienda/solicitud/show.html.twig', ['solicitud' => $solicitud]);
    }

    /**
     * Deletes a solicitud entity.
     *
     * Route("/{id}", name="tienda_solicitud_delete")
     * Method("DELETE")
     */
    public function deleteAction(Request $request, Solicitud $solicitud)
    {
        $form = $this->createDeleteForm($solicitud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($solicitud);
            $em->flush();
        }

        return $this->redirectToRoute('tienda_solicitud_index');
    }

    /**
     * Creates a form to delete a solicitud entity.
     *
     * @param Solicitud $solicitud The solicitud entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Solicitud $solicitud)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tienda_solicitud_delete', array('id' => $solicitud->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
