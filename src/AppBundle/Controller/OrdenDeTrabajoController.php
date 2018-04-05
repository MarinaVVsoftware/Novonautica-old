<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Astillero\Contratista;
use AppBundle\Entity\OrdenDeTrabajo;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Response;
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
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $ordenDeTrabajos = $em->getRepository('AppBundle:OrdenDeTrabajo')->findAll();
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'ODT');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }
        return $this->render('ordendetrabajo/index.html.twig', array(
            'title' => 'Ordenes de trabajo'
        ));
    }

    /**
     * Creates a new ordenDeTrabajo entity.
     *
     * @Route("/nueva", name="ordendetrabajo_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $precioTotal = 0;
        $utilidadvvTotal = 0;
        $preciovvTotal = 0;
        $ivaTotal = 0;
        $granTotal = 0;
        $saldoTotal = 0;
        $pagosTotal = 0;
        $ordenDeTrabajo = new Ordendetrabajo();
        $form = $this->createForm('AppBundle\Form\OrdenDeTrabajoType', $ordenDeTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $iva = $ordenDeTrabajo->getAstilleroCotizacion()->getIva();

            foreach ($ordenDeTrabajo->getContratistas() as $contratista){
                $precioTotal+=$contratista->getPrecio();
                $utilidadvvTotal+=$contratista->getUtilidadvv();
                $preciovvTotal+=$contratista->getPreciovv();
                $saldoTotal+=$contratista->getSaldo();
                $pagosTotal+=$contratista->getPagos();

                $ivatot = ($contratista->getPrecio() * $iva)/100;
                $total = $contratista->getPrecio() + $ivatot;
                $porcentajevv = $contratista->getProveedor()->getPorcentaje();
                $contratista
                    ->setPorcentajevv($porcentajevv)
                    ->setIvatot($ivatot)
                    ->setTotal($total);
                $ivaTotal+=$ivatot;
                $granTotal+=$total;
            }
            $fechaHoraActual = new \DateTime('now');
            $ordenDeTrabajo
                ->setPrecioTotal($precioTotal)
                ->setUtilidadvvTotal($utilidadvvTotal)
                ->setPreciovvTotal($preciovvTotal)
                ->setSaldoTotal($saldoTotal)
                ->setPagosTotal($pagosTotal)
                ->setIvaTotal($ivaTotal)
                ->setGranTotal($granTotal)
                ->setFecha($fechaHoraActual)
            ;
            $em->persist($ordenDeTrabajo);
            $em->flush();

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
     */
    public function buscarCotizacionAction(Request $request){
        $idcotizacion=$request->get('idcotizacion');
        $em = $this->getDoctrine()->getManager();

        $cotizacion = $em->getRepository('AppBundle:AstilleroCotizacion')
            ->createQueryBuilder('ac')
            ->select('ac','cliente','barco','AstilleroCotizaServicio','AstilleroServicioBasico','AstilleroProducto','AstilleroServicio')
            ->join('ac.cliente','cliente')
            ->join('ac.barco','barco')
            ->join('ac.acservicios','AstilleroCotizaServicio')
            ->leftJoin('AstilleroCotizaServicio.astilleroserviciobasico','AstilleroServicioBasico')
            ->leftJoin('AstilleroCotizaServicio.producto','AstilleroProducto')
            ->leftJoin('AstilleroCotizaServicio.servicio','AstilleroServicio')
            ->andWhere('ac.id = '.$idcotizacion)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $this->json($cotizacion);
    }

    /**
     * Finds and displays a ordenDeTrabajo entity.
     *
     * @Route("/{id}", name="ordendetrabajo_show")
     * @Method("GET")
     */
    public function showAction(OrdenDeTrabajo $ordenDeTrabajo)
    {
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
     */
    public function editAction(Request $request, OrdenDeTrabajo $ordenDeTrabajo)
    {
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
        foreach ($ordenDeTrabajo->getContratistas() as $contratista){
            $originalContratistas->add($contratista);
        }
        $deleteForm = $this->createDeleteForm($ordenDeTrabajo);
        $editForm = $this->createForm('AppBundle\Form\OrdenDeTrabajoType', $ordenDeTrabajo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $iva = $ordenDeTrabajo->getAstilleroCotizacion()->getIva();
            //$this->getDoctrine()->getManager()->flush();
            foreach ($originalContratistas as $contratista){
                if (false === $ordenDeTrabajo->getContratistas()->contains($contratista)) {

                        // remove the Task from the Tag
                        $contratista->getAstilleroODT()->removeContratista($contratista);

                        // if it was a many-to-one relationship, remove the relationship like this
                        //$motor->setBarco(null);
                        $em->persist($contratista);

                        // if you wanted to delete the Tag entirely, you can also do that
                        $em->remove($contratista);
                }else{
                    $precioTotal+=$contratista->getPrecio();
                    $utilidadvvTotal+=$contratista->getUtilidadvv();
                    $preciovvTotal+=$contratista->getPreciovv();
                    $saldoTotal+=$contratista->getSaldo();
                    $pagosTotal+=$contratista->getPagos();

                    $ivatot = ($contratista->getPrecio() * $iva)/100;
                    $total = $contratista->getPrecio() + $ivatot;
                    $porcentajevv = $contratista->getProveedor()->getPorcentaje();
                    $contratista
                        ->setPorcentajevv($porcentajevv)
                        ->setIvatot($ivatot)
                        ->setTotal($total);
                    $ivaTotal+=$ivatot;
                    $granTotal+=$total;

                }
            }
            foreach ($ordenDeTrabajo->getContratistas() as $contratistanuevo){
                if($contratistanuevo->getId() == null){
                    $precioTotal+=$contratistanuevo->getPrecio();
                    $utilidadvvTotal+=$contratistanuevo->getUtilidadvv();
                    $preciovvTotal+=$contratistanuevo->getPreciovv();
                    $saldoTotal+=$contratistanuevo->getSaldo();
                    $pagosTotal+=$contratistanuevo->getPagos();

                    $ivatot = ($contratistanuevo->getPrecio() * $iva)/100;
                    $total = $contratistanuevo->getPrecio() + $ivatot;
                    $porcentajevv = $contratistanuevo->getProveedor()->getPorcentaje();
                    $contratistanuevo
                        ->setPorcentajevv($porcentajevv)
                        ->setIvatot($ivatot)
                        ->setTotal($total);
                    $ivaTotal+=$ivatot;
                    $granTotal+=$total;
                }
            }
            $ordenDeTrabajo
                ->setPrecioTotal($precioTotal)
                ->setUtilidadvvTotal($utilidadvvTotal)
                ->setPreciovvTotal($preciovvTotal)
                ->setSaldoTotal($saldoTotal)
                ->setPagosTotal($pagosTotal)
                ->setIvaTotal($ivaTotal)
                ->setGranTotal($granTotal)
            ;
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
     * Deletes a ordenDeTrabajo entity.
     *
     * @Route("/{id}", name="ordendetrabajo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OrdenDeTrabajo $ordenDeTrabajo)
    {
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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrdenDeTrabajo $ordenDeTrabajo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordendetrabajo_delete', array('id' => $ordenDeTrabajo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
