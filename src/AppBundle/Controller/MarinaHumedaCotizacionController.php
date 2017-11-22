<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\MarinaHumedaServicio;
use AppBundle\Entity\MarinaHumedaTarifa;
use AppBundle\Entity\ValorSistema;
use AppBundle\Form\MarinaHumedaCotizacionType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Marinahumedacotizacion controller.
 *
 * @Route("/marina/cotizacion")
 */
class MarinaHumedaCotizacionController extends Controller
{
    /**
     * Enlista todas las cotizaciones
     *
     * @Route("/", name="marina-humeda_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $marinaHumedaCotizacions = $em->getRepository('AppBundle:MarinaHumedaCotizacion')->findAll();

        return $this->render('marinahumeda/cotizacion/index.html.twig', [
            'title' => 'Cotizaciones',
            'marinaHumedaCotizacions' => $marinaHumedaCotizacions,
        ]);
    }

    /**
     * Crea una nueva cotizacion
     *
     * @Route("/nueva", name="marina-humeda_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $marinaDiasEstadia = new MarinaHumedaCotizaServicios();
        $marinaElectricidad = new MarinaHumedaCotizaServicios();

        $dolarBase = $this->getDoctrine()
            ->getRepository(ValorSistema::class)
            ->find(1)
            ->getValor();
        $iva = $this->getDoctrine()
            ->getRepository(ValorSistema::class)
            ->find(2)
            ->getValor();
        $mensaje = $this->getDoctrine()
            ->getRepository(ValorSistema::class)
            ->find(4)
            ->getDescripcion();

        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($marinaDiasEstadia)
            ->addMarinaHumedaCotizaServicios($marinaElectricidad)
            ->setMensaje($mensaje);


        $form = $this->createForm(MarinaHumedaCotizacionType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $granSubtotal = 0;
            $granIva = 0;
            $granDescuento=0;
            $granTotal = 0;
            $descuento = $marinaHumedaCotizacion->getDescuento();
            $eslora = $marinaHumedaCotizacion->getBarco()->getEslora();
            $dolar = $marinaHumedaCotizacion->getDolar();

            $llegada = $marinaHumedaCotizacion->getFechaLlegada();
            $salida = $marinaHumedaCotizacion->getFechaSalida();
            $diferenciaDias = date_diff($llegada, $salida);
            $cantidadDias = ($diferenciaDias->days);

            // Días Estadía
            $tiposervicio = 1;
            $precio = $marinaDiasEstadia->getPrecio()->getCosto();
            $subTotal = $cantidadDias * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaDiasEstadia
                ->setTipo($tiposervicio)
                ->setEstatus(1)
                ->setCantidad($cantidadDias)
                ->setPrecio($precio)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            // Conexión a electricidad
            $tiposervicio = 2;
            $precio = $marinaElectricidad->getPrecioAux()->getCosto();
            $subTotal = $cantidadDias * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaElectricidad
                ->setTipo($tiposervicio)
                ->setEstatus(1)
                ->setCantidad($cantidadDias)
                ->setPrecio($precio)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            //-------------------------------------------------

            $fechaHoraActual = new \DateTime('now');
            $foliobase = $this->getDoctrine()
                                ->getRepository(ValorSistema::class)
                                ->find(3)
                                ->getValor();
            $folionuevo = $foliobase + 1;

            $marinaHumedaCotizacion
                ->setIva($iva)
                ->setSubtotal($granSubtotal)
                ->setIvatotal($granIva)
                ->setDescuentototal($granDescuento)
                ->setTotal($granTotal)
                ->setValidanovo(0)
                ->setValidacliente(0)
                ->setEstatus(1)
                ->setFecharegistro($fechaHoraActual)
                ->setFolio($folionuevo)
                ->setFoliorecotiza(0);
            $folioactualiza = $this->getDoctrine()
                                    ->getRepository(ValorSistema::class)
                                    ->find(3)
                                    ->setValor($folionuevo);

            $em->persist($marinaHumedaCotizacion);
            $em->flush();

            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
//            return $this->redirectToRoute('marina-humeda_index');

        }

        return $this->render('marinahumeda/cotizacion/new.html.twig', [
            'title' => 'Nueva cotización',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'valdolar' => $dolarBase,
            'valiva' => $iva,
            'form' => $form->createView()
        ]);


    }

    /**
     * Muestra una cotizacion en base a su id
     *
     * @Route("/{id}", name="marina-humeda_show")
     * @Method("GET")
     */
    public function showAction(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);

        return $this->render('marinahumeda/cotizacion/show.html.twig', [
            'title' => 'Cotización',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Genera el pdf de una cotizacion en base a su id
     *
     * @Route("/{id}/pdf", name="marina-pdf")
     * @Method("GET")
     *
     * @param MarinaHumedaCotizacion $mhc
     *
     * @return PdfResponse
     */
    public function displayMarinaPDF(MarinaHumedaCotizacion $mhc)
    {
        $html = $this->renderView('marinahumeda/cotizacion/cotizacionpdf.html.twig', [
            'title' => 'Cotizacion-'.$mhc->getFolio().'.pdf',
            'marinaHumedaCotizacion' => $mhc
        ]);

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            'Cotizacion-'.$mhc->getFolio().'-'.$mhc->getFoliorecotiza().'.pdf', 'application/pdf', 'inline'
        );
    }

    /**
     * Confirma la respuesta de un cliente a una cotizacion
     *
     * @Route("/{token}/confirma", name="respuesta-cliente")
     * @Method({"GET"})
     *
     * @param $token
     *
     * @return Response
     */
    public function repuestaCliente($token)
    {
        $em = $this->getDoctrine()->getManager();

        $cotizacionAceptar = $em->getRepository(MarinaHumedaCotizacion::class)
            ->findOneBy(['tokenacepta'=>$token]);

        if($cotizacionAceptar){
            if($cotizacionAceptar->getValidacliente() == 0){
                //$cotizacionAceptar->setValidacliente(2);
                //$em->persist($cotizacionAceptar);
                //$em->flush();

                if($cotizacionAceptar->getFoliorecotiza()==0){
                    $folio = $cotizacionAceptar->getFolio();
                }else{
                    $folio = $cotizacionAceptar->getFolio().'-'.$cotizacionAceptar->getFoliorecotiza();
                }
                $mensaje1 = '¡Enhorabuena!';
                $mensaje2 = 'La cotización '.$folio.' ha sido aprobada.';
                $mensaje3 = 'Para seguir adelante con su servicio es requerido el pago de los servicios que serán proporcionados. A continuación seleccione un método de pago.';
                $suformulario = 1;
            }else{
                $mensaje1 = '¡Error!';
                $mensaje2 = 'Cotización ya respondida';
                $mensaje3 = '';
                $suformulario = 0;
            }
        }else{
            $cotizacionRechazar = $em->getRepository(MarinaHumedaCotizacion::class)
                ->findOneBy(['tokenrechaza'=>$token]);
            if($cotizacionRechazar){
                if($cotizacionRechazar->getValidacliente() == 0) {
                    //$cotizacionRechazar->setValidacliente(1);
                    //$em->persist($cotizacionRechazar);
                    //$em->flush();

                    if($cotizacionRechazar->getFoliorecotiza()==0){
                        $folio = $cotizacionRechazar->getFolio();
                    }else{
                        $folio = $cotizacionRechazar->getFolio().'-'.$cotizacionRechazar->getFoliorecotiza();
                    }
                    $mensaje1 = '¡Oh-oh!';
                    $mensaje2 = 'La cotización '.$folio.' no ha sido aprobada.';
                    $mensaje3 = 'Nos gustaría saber su opinión o comentarios del motivo de su rechazo.';
                    $suformulario = 2;
                }else{
                    $mensaje1 = '¡Error!';
                    $mensaje2 = 'Cotización ya respondida';
                    $mensaje3 = '';
                    $suformulario = 0;
                }
            }else{
                throw new NotFoundHttpException();
            }
        }


        return $this->render('marinahumeda/cotizacion/respuesta-cliente.twig', [
            'mensaje1' => $mensaje1,
            'mensaje2' => $mensaje2,
            'mensaje3' => $mensaje3,
            'suformulario' => $suformulario
        ]);

    }

    /**
     * Muestra una cotizacion para recotizar
     *
     * @Route("/{id}/recotizar", name="marina-humeda_recotizar")
     * @Method({"GET", "POST"})
     */
    public function recotizaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior)
    {
        if ($marinaHumedaCotizacionAnterior->getEstatus() == 0 ||
            $marinaHumedaCotizacionAnterior->getValidacliente() ==2 ||
            $marinaHumedaCotizacionAnterior->getValidanovo() == 0 ||
            ($marinaHumedaCotizacionAnterior->getValidanovo() == 2 && $marinaHumedaCotizacionAnterior->getValidacliente() ==0)
            ) {
            throw new NotFoundHttpException();
        }

        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $foliorecotizado = $marinaHumedaCotizacionAnterior->getFoliorecotiza()+1;
        $cliente = $marinaHumedaCotizacionAnterior->getCliente();
        $barco = $marinaHumedaCotizacionAnterior->getBarco();

        $marinaHumedaCotizacion
            ->setCliente($cliente)
            ->setBarco($barco)
            ->setFechaLlegada($marinaHumedaCotizacionAnterior->getFechaLlegada())
            ->setFechaSalida($marinaHumedaCotizacionAnterior->getFechaSalida())
            ->setSlip($marinaHumedaCotizacionAnterior->getSlip())
            ->setDescuento($marinaHumedaCotizacionAnterior->getDescuento())
            ->setDolar($marinaHumedaCotizacionAnterior->getDolar())
            ->setIva($marinaHumedaCotizacionAnterior->getIva())
            ->setSubtotal($marinaHumedaCotizacionAnterior->getSubtotal())
            ->setIvatotal($marinaHumedaCotizacionAnterior->getIvatotal())
            ->setDescuentototal($marinaHumedaCotizacionAnterior->getDescuentototal())
            ->setTotal($marinaHumedaCotizacionAnterior->getTotal())
            ->setValidanovo(0)
            ->setValidacliente(0)
            ->setFolio($marinaHumedaCotizacionAnterior->getFolio())
            ->setFoliorecotiza($foliorecotizado)
            ->setMensaje($marinaHumedaCotizacionAnterior->getMensaje())
            ;


        $servicios = $marinaHumedaCotizacionAnterior->getMHCservicios();

        $marinaDiasEstadia = new MarinaHumedaCotizaServicios();
        $marinaDiasEstadia
            ->setTipo($servicios[0]->getTipo())
            ->setCantidad($servicios[0]->getCantidad())
            ->setPrecio($servicios[0]->getPrecio())
            ->setSubtotal($servicios[0]->getSubtotal())
            ->setIva($servicios[0]->getIva())
            ->setDescuento($servicios[0]->getDescuento())
            ->setTotal($servicios[0]->getTotal())
            ->setEstatus($servicios[0]->getEstatus())
        ;

        $marinaElectricidad = new MarinaHumedaCotizaServicios();
        $marinaElectricidad
            ->setTipo($servicios[1]->getTipo())
            ->setCantidad($servicios[1]->getCantidad())
            ->setPrecio($servicios[1]->getPrecio())
            ->setSubtotal($servicios[1]->getSubtotal())
            ->setIva($servicios[1]->getIva())
            ->setDescuento($servicios[1]->getDescuento())
            ->setTotal($servicios[1]->getTotal())
            ->setEstatus($servicios[1]->getEstatus())
        ;

        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($marinaDiasEstadia)
            ->addMarinaHumedaCotizaServicios($marinaElectricidad);
        $dolar = $marinaHumedaCotizacionAnterior->getDolar();
        $iva = $marinaHumedaCotizacionAnterior->getIva();

        $form = $this->createForm(MarinaHumedaCotizacionType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $granSubtotal = 0;
            $granIva = 0;
            $granDescuento=0;
            $granTotal = 0;
            $descuento = $marinaHumedaCotizacion->getDescuento();
            $eslora = $marinaHumedaCotizacion->getBarco()->getEslora();

            // Días Estadía

            $llegada = $marinaHumedaCotizacion->getFechaLlegada();
            $salida = $marinaHumedaCotizacion->getFechaSalida();

            $diferenciaDias = date_diff($llegada, $salida);

            $cantidad = ($diferenciaDias->days)+1;
            $precio = $marinaDiasEstadia->getPrecio()->getCosto();

            $subTotal = $cantidad * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaDiasEstadia
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setPrecio($precio)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granDescuento+=$descuentoTot;
            $granTotal+=$total;

            // Conexión a electricidad
            $cantidad = $marinaElectricidad->getCantidad();
            $precio = $marinaElectricidad->getPrecioAux()->getCosto();

            $subTotal = $cantidad * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaElectricidad
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

            //-------------------------------------------------
            $fechaHoraActual = new \DateTime('now');
            $marinaHumedaCotizacion
                ->setCliente($cliente)
                ->setBarco($barco)
                ->setDolar($dolar)
                ->setIva($iva)
                ->setSubtotal($granSubtotal)
                ->setIvatotal($granIva)
                ->setDescuentototal($granDescuento)
                ->setTotal($granTotal)
                ->setValidanovo(0)
                ->setValidacliente(0)
                ->setEstatus(1)
                ->setFecharegistro($fechaHoraActual);
            $marinaHumedaCotizacionAnterior
                ->setEstatus(0);
            $em->persist($marinaHumedaCotizacion);
            $em->persist($marinaHumedaCotizacionAnterior);
            $em->flush();

            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);

        }
        return $this->render('marinahumeda/cotizacion/recotizar.html.twig', [
            'title' => 'Recotización',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Displays a form to edit an existing marinaHumedaCotizacion entity.
     *
     * @Route("/{id}/validar", name="marina-humeda_validar")
     * @Method({"GET", "POST"})
     **/
    public function validaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion,\Swift_Mailer $mailer)
    {
        if ($marinaHumedaCotizacion->getEstatus() == 0 ||
            $marinaHumedaCotizacion->getValidanovo() == 1 ||
            $marinaHumedaCotizacion->getValidanovo() == 2
        //    $marinaHumedaCotizacion->getValidacliente() ==1 ||
        //    $marinaHumedaCotizacion->getValidacliente() ==2
        ) {
            throw new NotFoundHttpException();
        }


        $valorSistema = new ValorSistema();

        $servicios = $marinaHumedaCotizacion->getMHCservicios();

        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);
        $editForm = $this->createForm( 'AppBundle\Form\MarinaHumedaCotizacionType', $marinaHumedaCotizacion);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            if($marinaHumedaCotizacion->getValidanovo()==2){
                $tokenAcepta = $valorSistema->generaToken(100);
                $tokenRechaza = $valorSistema->generaToken(100);
                $marinaHumedaCotizacion
                    ->setTokenacepta($tokenAcepta)
                    ->setTokenrechaza($tokenRechaza);

                // Enviar correo de confirmacion
                $message = (new \Swift_Message('¡Cotizacion de servicios!'))
                    ->setFrom('noresponder@novonautica.com')
                    ->setTo($marinaHumedaCotizacion->getCliente()->getCorreo())
                    ->setBcc('admin@novonautica.com')
                    ->setBody(
                        $this->renderView('marinahumeda/cotizacion/correo-clientevalida.twig', [
                            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
                            'tokenAcepta' => $tokenAcepta,
                            'tokenRechaza' => $tokenRechaza
                        ]),
                        'text/html'
                    );

                $mailer->send($message);

            }

            $this->getDoctrine()->getManager()->flush();



            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }

        return $this->render('marinahumeda/cotizacion/validar.html.twig', [
            'title' => 'Validación',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
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
     * Crea un formulario para eliminar una cotizacion
     *
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion The marinaHumedaCotizacion entity
     *
     * @return Form The form
     */
    private function createDeleteForm(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda_delete', array('id' => $marinaHumedaCotizacion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
