<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\AstilleroCotizaServicio;
use AppBundle\Entity\AstilleroServicio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ValorSistema;

/**
 * Astillerocotizacion controller.
 *
 * @Route("astillero")
 */
class AstilleroCotizacionController extends Controller
{
    /**
     * Lists all astilleroCotizacion entities.
     *
     * @Route("/", name="astillero_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $astilleroCotizacions = $em->getRepository('AppBundle:AstilleroCotizacion')->findAll();

        return $this->render('astillerocotizacion/index.html.twig', array(
            'astilleroCotizacions' => $astilleroCotizacions,
            'astillerocotizaciones' => 1
        ));
    }

    /**
     * Creates a new astilleroCotizacion entity.
     *
     * @Route("/nueva-cotizacion", name="astillero_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $astilleroCotizacion = new AstilleroCotizacion();
        $astilleroGrua = new AstilleroCotizaServicio();
        $astilleroSuelo = new AstilleroCotizaServicio();
        $astilleroRampa = new AstilleroCotizaServicio();
        $astilleroKarcher = new AstilleroCotizaServicio();
        $astilleroVarada = new AstilleroCotizaServicio();

        $astilleroCotizacion
            ->addAcservicio($astilleroGrua)
            ->addAcservicio($astilleroSuelo)
            ->addAcservicio($astilleroRampa)
            ->addAcservicio($astilleroKarcher)
            ->addAcservicio($astilleroVarada);

        $dolar = $this->getDoctrine()
            ->getRepository(ValorSistema::class)
            ->find(1)
            ->getValor();
        $iva = $this->getDoctrine()
            ->getRepository(ValorSistema::class)
            ->find(2)
            ->getValor();

        $form = $this->createForm('AppBundle\Form\AstilleroCotizacionType', $astilleroCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $granSubtotal = 0;
            $granIva = 0;
            $granTotal = 0;

            // Uso de grua
            $servicio = $this->getDoctrine()
                            ->getRepository(AstilleroServicio::class)
                            ->find(1);
            $cantidad = $astilleroGrua->getCantidad();
            $precio = $astilleroGrua->getPrecio();
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;

            $astilleroGrua
                ->setAstilleroservicio($servicio)
                ->setServicio(null)
                ->setEstatus(1)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total)
            ;
            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granTotal+=$total;

            // Uso de suelo
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicio::class)
                ->find(2);
            $cantidad = $astilleroCotizacion->getDiasEstadia();
            $precio = $astilleroSuelo->getPrecio();
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;

            $astilleroSuelo
                ->setAstilleroservicio($servicio)
                ->setServicio(null)
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total)
            ;
            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granTotal+=$total;

            // Uso de rampa
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicio::class)
                ->find(3);
            $cantidad = 1;
            $precio = $astilleroRampa->getPrecio();
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;

            $astilleroRampa
                ->setAstilleroservicio($servicio)
                ->setServicio(null)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total)
            ;
            if($astilleroRampa->getEstatus()){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granTotal+=$total;
            }

            // Uso de karcher
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicio::class)
                ->find(4);
            $cantidad =1;
            $precio = $astilleroKarcher->getPrecio();
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;

            $astilleroKarcher
                ->setAstilleroservicio($servicio)
                ->setServicio(null)
                ->setCantidad($cantidad)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total)
            ;
            if($astilleroKarcher->getEstatus()){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granTotal+=$total;
            }

            // sacar varada y botadura
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicio::class)
                ->find(5);
            $cantidad = $astilleroVarada->getCantidad();
            $precio = $astilleroVarada->getPrecio();
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;

            $astilleroVarada
                ->setAstilleroservicio($servicio)
                ->setServicio(null)
                ->setSubtotal($subTotal)
                ->setIva($ivaTot)
                ->setTotal($total)
            ;
            if($astilleroVarada->getEstatus()){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granTotal+=$total;
            }

            foreach ($astilleroCotizacion->getAcservicios() as $servAst){
              if($servAst->getAstilleroservicio()==null){

                  $cantidad = $servAst->getCantidad();
                  $precio = $servAst->getPrecio();
                  $subTotal = $cantidad * $precio;
                  $ivaTot = ($subTotal * $iva)/100;
                  $total = $subTotal + $ivaTot;


                  $servAst
                      ->setSubtotal($subTotal)
                      ->setIva($ivaTot)
                      ->setTotal($total)
                      ->setEstatus(true);

                  $granSubtotal+=$subTotal;
                  $granIva+=$ivaTot;
                  $granTotal+=$total;
              }
            }

            //------------------------------------------------
            $fechaHoraActual = new \DateTime('now');
            $astilleroCotizacion
                ->setDolar($dolar)
                ->setIva($iva)
                ->setSubtotal($granSubtotal)
                ->setIvatotal($granIva)
                ->setTotal($granTotal)
                ->setFecharegistro($fechaHoraActual);

            $em->persist($astilleroCotizacion);
            $em->flush();

            return $this->redirectToRoute('astillero_show', array('id' => $astilleroCotizacion->getId()));
        }

        return $this->render('astillerocotizacion/new.html.twig', array(
            'astilleroCotizacion' => $astilleroCotizacion,
            'valdolar' => $dolar,
            'valiva' => $iva,
            'form' => $form->createView(),
            'astilleronuevacotizacion' => 1
        ));
    }

    /**
     * Finds and displays a astilleroCotizacion entity.
     *
     * @Route("/{id}", name="astillero_show")
     * @Method("GET")
     */
    public function showAction(AstilleroCotizacion $astilleroCotizacion)
    {
        $deleteForm = $this->createDeleteForm($astilleroCotizacion);

        return $this->render('astillerocotizacion/show.html.twig', array(
            'astilleroCotizacion' => $astilleroCotizacion,
            'delete_form' => $deleteForm->createView(),
            'astillerocotizaciones' => 1
        ));
    }

    /**
     * Displays a form to edit an existing astilleroCotizacion entity.
     *
     * @Route("/{id}/edit", name="astillero_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, AstilleroCotizacion $astilleroCotizacion)
    {
        $deleteForm = $this->createDeleteForm($astilleroCotizacion);
        $editForm = $this->createForm('AppBundle\Form\AstilleroCotizacionType', $astilleroCotizacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('astillero_edit', array('id' => $astilleroCotizacion->getId()));
        }

        return $this->render('astillerocotizacion/edit.html.twig', array(
            'astilleroCotizacion' => $astilleroCotizacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a astilleroCotizacion entity.
     *
     * @Route("/{id}", name="astillero_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AstilleroCotizacion $astilleroCotizacion)
    {
        $form = $this->createDeleteForm($astilleroCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($astilleroCotizacion);
            $em->flush();
        }

        return $this->redirectToRoute('astillero_index');
    }

    /**
     * Creates a form to delete a astilleroCotizacion entity.
     *
     * @param AstilleroCotizacion $astilleroCotizacion The astilleroCotizacion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AstilleroCotizacion $astilleroCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('astillero_delete', array('id' => $astilleroCotizacion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
