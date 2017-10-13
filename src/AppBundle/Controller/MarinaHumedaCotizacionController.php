<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Form\MarinaHumedaCotizacionType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Marinahumedacotizacion controller.
 *
 * @Route("marina-humeda")
 */
class MarinaHumedaCotizacionController extends Controller
{
    /**
     * Lists all marinaHumedaCotizacion entities.
     *
     * @Route("/cotizaciones", name="marina-humeda_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $marinaHumedaCotizacions = $em->getRepository('AppBundle:MarinaHumedaCotizacion')->findAll();

        return $this->render('marinahumedacotizacion/index.html.twig', array(
            'marinaHumedaCotizacions' => $marinaHumedaCotizacions,
            'marinacotizaciones' => 1

        ));
    }

    /**
     * Creates a new marinaHumedaCotizacion entity.
     *
     * @Route("/nueva-cotizacion", name="marina-humeda_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $marinaDiasEstadia = new MarinaHumedaCotizaServicios();
        $marinaDiasAdicionales = new MarinaHumedaCotizaServicios();
        $marinaGasolina = new MarinaHumedaCotizaServicios();
        $marinaAgua = new MarinaHumedaCotizaServicios();
        $marinaElectricidad = new MarinaHumedaCotizaServicios();
        $marinaDezasolve = new MarinaHumedaCotizaServicios();
        $marinaLimpieza = new MarinaHumedaCotizaServicios();
        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($marinaDiasEstadia)
            ->addMarinaHumedaCotizaServicios($marinaDiasAdicionales)
            ->addMarinaHumedaCotizaServicios($marinaGasolina)
            ->addMarinaHumedaCotizaServicios($marinaAgua)
            ->addMarinaHumedaCotizaServicios($marinaElectricidad)
            ->addMarinaHumedaCotizaServicios($marinaDezasolve)
            ->addMarinaHumedaCotizaServicios($marinaLimpieza)
            ;
        $form = $this->createForm(MarinaHumedaCotizacionType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $granSubtotal = 0;
            $granIva = 0;
            $granDescuento=0;
            $granTotal = 0;
            $dolar = 18;
            $iva = 0.16;
            $descuento = $marinaHumedaCotizacion->getDescuento();

            $nombre = 'Días Estadía';
            $cantidad = $marinaDiasEstadia->getCantidad();
            $precio = $marinaDiasEstadia->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaDiasEstadia
                ->setServicio($nombre)
                ->setEstatus(1)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            $nombre = 'Días Adicionales';
            $cantidad = $marinaDiasAdicionales->getCantidad();
            //$precio = $marinaHCS->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaDiasAdicionales
                ->setServicio($nombre)
                ->setEstatus(1)
                ->setPrecio($precio)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            $nombre = 'Abastecimiento de gasolina';
            $cantidad = $marinaGasolina->getCantidad();
            $precio = $marinaGasolina->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaGasolina
                ->setServicio($nombre)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            if($marinaGasolina->getEstatus() == 1){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granDescuento+=$descuentoTot;
                $granTotal+=$total;
            }

            $nombre = 'Abastecimiento de agua';
            $cantidad = 1;
            $precio = $marinaAgua->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaAgua
                ->setServicio($nombre)
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            $nombre = 'Conexión a electricidad';
            $cantidad = 1;
            $precio = $marinaElectricidad->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaElectricidad
                ->setServicio($nombre)
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            $nombre = 'Dezasolve';
            $cantidad = 1;
            $precio = $marinaDezasolve->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaDezasolve
                ->setServicio($nombre)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            if($marinaDezasolve->getEstatus() == 1){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granDescuento+=$descuentoTot;
                $granTotal+=$total;
            }

            $nombre = 'Limpieza de locación';
            $cantidad = 1;
            $precio = $marinaLimpieza->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaLimpieza
                ->setServicio($nombre)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            if($marinaLimpieza->getEstatus() == 1){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granDescuento+=$descuentoTot;
                $granTotal+=$total;
            }

            //-------------------------------------------------
            $marinaHumedaCotizacion
                ->setDolar($dolar)
                ->setIva($iva)
                ->setSubtotal($granSubtotal)
                ->setIvatotal($granIva)
                ->setDescuentototal($granDescuento)
                ->setTotal($granTotal);
            $em->persist($marinaHumedaCotizacion);
            $em->flush();

//            return $this->redirectToRoute('marina-humeda_show', array('id' => $marinaHumedaCotizacion->getId()));
            return $this->redirectToRoute('marina-humeda_index');

        }

        return $this->render('marinahumedacotizacion/new.html.twig', array(
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
            'marinanuevacotizacion' => 1
        ));


    }

    /**
     * Finds and displays a marinaHumedaCotizacion entity.
     *
     * @Route("/{id}", name="marina-humeda_show")
     * @Method("GET")
     */
    public function showAction(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);

        return $this->render('marinahumedacotizacion/show.html.twig', array(
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'delete_form' => $deleteForm->createView(),
            'marinacotizaciones' => 1
        ));
    }

    /**
     * Displays a form to edit an existing marinaHumedaCotizacion entity.
     *
     * @Route("/{id}/editar", name="marina-humeda_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $servicios = $marinaHumedaCotizacion->getMHCservicios();

        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);
        $editForm = $this->createForm( 'AppBundle\Form\MarinaHumedaCotizacionType', $marinaHumedaCotizacion);
        dump($servicios);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $granSubtotal = 0;
            $granIva = 0;
            $granDescuento=0;
            $granTotal = 0;
            $iva = $marinaHumedaCotizacion->getIva();
            $descuento = $marinaHumedaCotizacion->getDescuento();

            //$nombre = 'Días Estadía';
            $cantidad = $servicios[0]->getCantidad();
            $precio = $servicios[0]->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $servicios[0]
                ->setEstatus(1)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            //$nombre = 'Días Adicionales';
            $cantidad = $servicios[1]->getCantidad();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $servicios[1]
                ->setEstatus(1)
                ->setPrecio($precio)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            //$nombre = 'Abastecimiento de gasolina';
            $cantidad = $servicios[2]->getCantidad();
            $precio = $servicios[2]->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $servicios[2]
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            if($servicios[2]->getEstatus() == 1){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granDescuento+=$descuentoTot;
                $granTotal+=$total;
            }

            //$nombre = 'Abastecimiento de agua';
            $cantidad = 1;
            $precio = $servicios[3]->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $servicios[3]
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            //$nombre = 'Conexión a electricidad';
            $cantidad = 1;
            $precio = $servicios[4]->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $servicios[4]
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            //$nombre = 'Dezasolve';
            $cantidad = 1;
            $precio = $servicios[5]->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $servicios[5]
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            if($servicios[5]->getEstatus() == 1){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granDescuento+=$descuentoTot;
                $granTotal+=$total;
            }

            //$nombre = 'Limpieza de locación';
            $cantidad = 1;
            $precio = $servicios[6]->getPrecio();

            $subTotal = $cantidad * $precio;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = $subTotal * $iva;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $servicios[6]
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            if($servicios[6]->getEstatus() == 1){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granDescuento+=$descuentoTot;
                $granTotal+=$total;
            }

            $marinaHumedaCotizacion
                ->setSubtotal($granSubtotal)
                ->setIvatotal($granIva)
                ->setDescuentototal($granDescuento)
                ->setTotal($granTotal);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marina-humeda_edit', array('id' => $marinaHumedaCotizacion->getId()));
        }

        return $this->render('marinahumedacotizacion/edit.html.twig', array(
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'marinacotizaciones' => 1
        ));
    }

    /**
     * Deletes a marinaHumedaCotizacion entity.
     *
     * @Route("/{id}", name="marina-humeda_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $form = $this->createDeleteForm($marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marinaHumedaCotizacion);
            $em->flush();
        }

        return $this->redirectToRoute('marina-humeda_index');
    }

    /**
     * Creates a form to delete a marinaHumedaCotizacion entity.
     *
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion The marinaHumedaCotizacion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda_delete', array('id' => $marinaHumedaCotizacion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /**
     * @Route("/agenda", name="marina-agenda")
     */
    public function displayMarinaAgenda(Request $request)
    {
        return $this->render('marina-agenda.twig', [
            'marinaagenda' => 1
        ]);
    }
    /**
     * @Route("/agenda/nuevo-evento", name="marina-agenda-nuevo-evento")
     */
    public function displayMarinaAgendaNuevoEvento(Request $request)
    {
        return $this->render('marina-agenda-nuevo-evento.twig', [
            'marinaagenda' => 1
        ]);
    }
    /**
     * @Route("/administracion", name="marina-administracion")
     */
    public function displayMarinaAdministracion(Request $request)
    {
        return $this->render('marina-administracion.twig', [
            'marinaadministracion' => 1
        ]);
    }

}
