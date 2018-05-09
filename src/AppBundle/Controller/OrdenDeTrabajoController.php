<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Astillero\Contratista;
use AppBundle\Entity\OrdenDeTrabajo;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Ordendetrabajo controller.
 *
 * @Route("astillero/odt")
 */
class OrdenDeTrabajoController extends Controller
{
    /**
     * Lists all ordenDeTrabajo entities.
     *
     * @Route("/", name="ordendetrabajo_index")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'ODT');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }

        return $this->render('ordendetrabajo/index.html.twig', [
            'title' => 'Ordenes de trabajo'
        ]);
    }

    /**
     * Creates a new ordenDeTrabajo entity.
     *
     * @Route("/nueva", name="ordendetrabajo_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param \Swift_Mailer $mailer
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $ordenDeTrabajo = new Ordendetrabajo();
        $this->denyAccessUnlessGranted('ROLE_ODT_CREATE', $ordenDeTrabajo);

        $precioTotal = 0;
        $utilidadvvTotal = 0;
        $preciovvTotal = 0;
        $ivaTotal = 0;
        $granTotal = 0;
        $saldoTotal = 0;
        $pagosTotal = 0;

        $form = $this->createForm('AppBundle\Form\OrdenDeTrabajoType', $ordenDeTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $iva = $ordenDeTrabajo->getAstilleroCotizacion()->getIva();
            $notificados = [];
            foreach ($ordenDeTrabajo->getContratistas() as $contratista) {
                $precioTotal += $contratista->getPrecio();
                $utilidadvvTotal += $contratista->getUtilidadvv();
                $preciovvTotal += $contratista->getPreciovv();
                $ivatot = ($contratista->getPrecio() * $iva) / 100;
                $total = $contratista->getPrecio() + $ivatot;
                $porcentajevv = $contratista->getProveedor()->getPorcentaje();
                $contratista
                    ->setPorcentajevv($porcentajevv)
                    ->setIvatot($ivatot)
                    ->setTotal($total);
                $ivaTotal += $ivatot;
                $granTotal += $total;
                if($contratista->getProveedor()->getCorreo()){
                    array_push($notificados,$contratista->getProveedor()->getCorreo());
                }
            }
            $fechaHoraActual = new \DateTime('now');
            $ordenDeTrabajo
                ->setPrecioTotal($precioTotal)
                ->setUtilidadvvTotal($utilidadvvTotal)
                ->setPreciovvTotal($preciovvTotal)
                ->setIvaTotal($ivaTotal)
                ->setGranTotal($granTotal)
                ->setPagosTotal(0)
                ->setSaldoTotal($granTotal)
                ->setFecha($fechaHoraActual);
            $em->persist($ordenDeTrabajo);
            $em->flush();

            //enviar correo para avisar a contratistas y proveedores
            if(!empty($notificados)){
                if($ordenDeTrabajo->getAstilleroCotizacion()->getFoliorecotiza() == 0){
                    $folio = $ordenDeTrabajo->getAstilleroCotizacion()->getFolio();
                }else{
                    $folio = $ordenDeTrabajo->getAstilleroCotizacion()->getFolio().'-'.$ordenDeTrabajo->getAstilleroCotizacion()->getFoliorecotiza();
                }
                $message = (new \Swift_Message('¡Asignación de orden de trabajo!'));
                $message->setFrom('noresponder@novonautica.com');
                $message->setTo($notificados);
                $message->setBody(
                    $this->renderView('mail/asignacionODT.twig', [
                        'folio' => $folio
                    ]),
                    'text/html'
                );
                $mailer->send($message);
            }

            return $this->redirectToRoute('ordendetrabajo_index');
        }

        return $this->render('ordendetrabajo/new.html.twig', array(
            'ordenDeTrabajo' => $ordenDeTrabajo,
            'form' => $form->createView(),
            'title' => 'Nueva Orden de Trabajo'
        ));
    }

    /**
     * @Route("/buscarcotizacion", name="odt_busca_cotizacion")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function buscarCotizacionAction(Request $request)
    {
        $idcotizacion = $request->get('idcotizacion');
        $em = $this->getDoctrine()->getManager();

        $cotizacion = $em->getRepository('AppBundle:AstilleroCotizacion')
            ->createQueryBuilder('ac')
            ->select('ac', 'cliente', 'barco', 'AstilleroCotizaServicio', 'AstilleroServicioBasico', 'AstilleroProducto', 'AstilleroServicio')
            ->join('ac.cliente', 'cliente')
            ->join('ac.barco', 'barco')
            ->join('ac.acservicios', 'AstilleroCotizaServicio')
            ->leftJoin('AstilleroCotizaServicio.astilleroserviciobasico', 'AstilleroServicioBasico')
            ->leftJoin('AstilleroCotizaServicio.producto', 'AstilleroProducto')
            ->leftJoin('AstilleroCotizaServicio.servicio', 'AstilleroServicio')
            ->andWhere('ac.id = ' . $idcotizacion)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $this->json($cotizacion);
    }

    /**
     * Finds and displays a ordenDeTrabajo entity.
     *
     * @Route("/{id}", name="ordendetrabajo_show")
     * @Method("GET")
     *
     * @param OrdenDeTrabajo $ordenDeTrabajo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(OrdenDeTrabajo $ordenDeTrabajo)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT', $ordenDeTrabajo);
        $deleteForm = $this->createDeleteForm($ordenDeTrabajo);

        return $this->render('ordendetrabajo/show.html.twig', [
            'title' => 'Detalle ODT',
            'ordenDeTrabajo' => $ordenDeTrabajo,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing ordenDeTrabajo entity.
     *
     * @Route("/{id}/editar", name="ordendetrabajo_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param OrdenDeTrabajo $ordenDeTrabajo
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, OrdenDeTrabajo $ordenDeTrabajo)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT_CONTRATISTA_EDIT', $ordenDeTrabajo);

        $precioTotal = 0;
        $utilidadvvTotal = 0;
        $preciovvTotal = 0;
        $ivaTotal = 0;
        $granTotal = 0;
        $saldoTotal = 0;
        $materialesTotal = 0;
        $pagosTotal = 0;
        $em = $this->getDoctrine()->getManager();

        $originalContratistas = new ArrayCollection();
        foreach ($ordenDeTrabajo->getContratistas() as $contratista) {
            $originalContratistas->add($contratista);
        }
        $deleteForm = $this->createDeleteForm($ordenDeTrabajo);
        $editForm = $this->createForm('AppBundle\Form\OrdenDeTrabajoType', $ordenDeTrabajo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $iva = $ordenDeTrabajo->getAstilleroCotizacion()->getIva();
            //$this->getDoctrine()->getManager()->flush();
            foreach ($originalContratistas as $contratista) {
                if (false === $ordenDeTrabajo->getContratistas()->contains($contratista)) {

                    // remove the Task from the Tag
                    $contratista->getAstilleroODT()->removeContratista($contratista);

                    // if it was a many-to-one relationship, remove the relationship like this
                    //$motor->setBarco(null);
                    $em->persist($contratista);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($contratista);
                } else {
                    $precioTotal += $contratista->getPrecio();
                    $utilidadvvTotal += $contratista->getUtilidadvv();
                    $preciovvTotal += $contratista->getPreciovv();
                    $ivatot = ($contratista->getPrecio() * $iva) / 100;
                    $total = $contratista->getPrecio() + $ivatot;
                    $porcentajevv = $contratista->getProveedor()->getPorcentaje();
                    $contratista
                        ->setPorcentajevv($porcentajevv)
                        ->setIvatot($ivatot)
                        ->setTotal($total);
                    $ivaTotal += $ivatot;
                    $granTotal += $total;

                }
            }
            foreach ($ordenDeTrabajo->getContratistas() as $contratistanuevo) {
                if ($contratistanuevo->getId() == null) {
                    $precioTotal += $contratistanuevo->getPrecio();
                    $utilidadvvTotal += $contratistanuevo->getUtilidadvv();
                    $preciovvTotal += $contratistanuevo->getPreciovv();

                    $ivatot = ($contratistanuevo->getPrecio() * $iva) / 100;
                    $total = $contratistanuevo->getPrecio() + $ivatot;
                    $porcentajevv = $contratistanuevo->getProveedor()->getPorcentaje();
                    $contratistanuevo
                        ->setPorcentajevv($porcentajevv)
                        ->setIvatot($ivatot)
                        ->setTotal($total);
                    $ivaTotal += $ivatot;
                    $granTotal += $total;
                }
            }
            $ordenDeTrabajo
                ->setPrecioTotal($precioTotal)
                ->setUtilidadvvTotal($utilidadvvTotal)
                ->setPreciovvTotal($preciovvTotal)
                ->setSaldoTotal($granTotal)
                ->setIvaTotal($ivaTotal)
                ->setGranTotal($granTotal);
            $em->persist($ordenDeTrabajo);
            $em->flush();

            return $this->redirectToRoute('ordendetrabajo_show', ['id' => $ordenDeTrabajo->getId()]);
        }

        return $this->render('ordendetrabajo/edit.html.twig', [
            'title' => 'Editar ODT',
            'ordenDeTrabajo' => $ordenDeTrabajo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/contratista-pago", name="ordendetrabajo_contratista_pago")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Contratista $contratista
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function pagoAction(Request $request, Contratista $contratista)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT_PAGO', $contratista);
        $pagadoMXN =0;
        $dolar = $contratista->getAstilleroODT()->getAstilleroCotizacion()->getDolar();
        $originalPagos = new ArrayCollection();
        foreach ($contratista->getContratistapagos() as $pago) {
            $originalPagos->add($pago);
            if($pago->getDivisa()=='USD'){
                $pagadoMXN+=($pago->getCantidad()*$dolar)/100;
            }else{
                $pagadoMXN+=$pago->getCantidad();
            }
        }
        $saldoMXN = $contratista->getTotal() - $pagadoMXN;
        $editForm = $this->createForm('AppBundle\Form\Astillero\ContratistaPagoType', $contratista);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($originalPagos as $pago) {
                if (false === $contratista->getContratistapagos()->contains($pago)) {
                    $pago->getContratista()->removeContratistapago($pago);
                    $em->persist($pago);
                    $em->remove($pago);
                }
            }
            $cantidadPago = 0;
            $saldo = 0;
            foreach ($contratista->getContratistapagos() as $unpago){
                if($unpago->getDivisa()=='USD'){
                    $cantidadPago+=($unpago->getCantidad()*$dolar)/100;
                }else{
                    $cantidadPago+=$unpago->getCantidad();
                }
                $saldo = $contratista->getTotal() - $cantidadPago;
                $unpago->setSaldo($saldo);
            }
            if ($saldo < -1) {
                $this->addFlash(
                    'notice',
                    'Error! Se ha intentado pagar más que el saldo restante'
                );
            } else {
                $em->persist($contratista);
                $em->flush();
                return $this->redirectToRoute('ordendetrabajo_show', ['id' => $contratista->getAstilleroODT()->getId()]);
            }

        }

        return $this->render('ordendetrabajo/pago.html.twig', [
            'title' => 'Registrar Pago Contratista',
            'contratista' => $contratista,
            'edit_form' => $editForm->createView(),
            'pagadoMXN' => $pagadoMXN,
            'saldoMXN' => $saldoMXN
        ]);
    }

    /**
     * @Route("/{id}/actividad", name="ordendetrabajo_contratista_actividad")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Contratista $contratista
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function actividadAction(Request $request, Contratista $contratista)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT_ACTIVIDAD', $contratista);
        $originalActividades = new ArrayCollection();
        $oldFotos = new ArrayCollection();

        /** @var Contratista\Actividad $actividad */
        foreach ($contratista->getContratistaactividades() as $a => $actividad) {
            $originalActividades->add($actividad);

            $realOldFotos = new ArrayCollection();

            foreach ($actividad->getFotos() as $foto) {
                $realOldFotos->add($foto);
            }

            $oldFotos->add($realOldFotos);
        }

        $editForm = $this->createForm('AppBundle\Form\Astillero\ContratistaActividadType', $contratista);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($originalActividades as $a => $actividad) {
                if (!$contratista->getContratistaactividades()->contains($actividad)) {
                    $actividad->setContratista(null);
                    $em->remove($actividad);
                }

                foreach ($oldFotos[$a] as $dFoto) {
                    if (!$actividad->getFotos()->contains($dFoto)) {
                        $dFoto->setActividad(null);
                        $em->remove($dFoto);
                    }
                }
            }

            foreach ($contratista->getContratistaactividades() as $act1) {
                $ban = false;

                foreach ($originalActividades as $act2) {
                    if ($act2->getId() == $act1->getId()) {
                        $ban = true;
                    }
                }

                if (!$ban) {
                    $act1->setUsuario($this->getUser()->getNombre());
                }
            }

            $em->persist($contratista);
            $em->flush();

            return $this->redirectToRoute('ordendetrabajo_show', ['id' => $contratista->getAstilleroODT()->getId()]);
        }

        return $this->render('ordendetrabajo/actividad.html.twig', [
            'title' => 'Registrar Actividad Contratista',
            'contratista' => $contratista,
            'edit_form' => $editForm->createView()
        ]);
    }

    /**
     * Deletes a ordenDeTrabajo entity.
     *
     * @Route("/{id}", name="ordendetrabajo_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param OrdenDeTrabajo $ordenDeTrabajo
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, OrdenDeTrabajo $ordenDeTrabajo)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT_DELETE', $ordenDeTrabajo);

        $form = $this->createDeleteForm($ordenDeTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ordenDeTrabajo);
            $em->flush();
        }

        return $this->redirectToRoute('ordendetrabajo_index');
    }

    /**
     * Creates a form to delete a ordenDeTrabajo entity.
     *
     * @param OrdenDeTrabajo $ordenDeTrabajo The ordenDeTrabajo entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(OrdenDeTrabajo $ordenDeTrabajo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordendetrabajo_delete', ['id' => $ordenDeTrabajo->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
