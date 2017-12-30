<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\AstilleroCotizaServicio;
use AppBundle\Entity\AstilleroServicioBasico;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ValorSistema;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Astillerocotizacion controller.
 *
 * @Route("/astillero")
 */
class AstilleroCotizacionController extends Controller
{
    /**
     * Enlista todas las cotizaciones de astillero
     *
     * @Route("/", name="astillero_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $astilleroCotizacions = $em->getRepository('AppBundle:AstilleroCotizacion')->findAll();

        return $this->render('astillero/cotizacion/index.html.twig', [
            'title' => 'Cotizaciones',
            'astilleroCotizacions' => $astilleroCotizacions,
        ]);
    }
    /**
     * @Route("/aceptaciones", name="astillero-aceptaciones")
     */
    public function displayAstilleroAceptaciones()
    {
        return $this->render('astillero-aceptaciones.twig', [
            'title' => 'Aceptaciones'
        ]);
    }

    /**
     * @Route("/odt", name="astillero-odt")
     */
    public function displayAstilleroODT(Request $request)
    {
        return $this->render('astillero-odt.twig');
    }

    /**
     * Crea una nueva cotizacion de astillero
     *
     * @Route("/nueva", name="astillero_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
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

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
        $sistema =$query->getArrayResult();
        $dolar = $sistema[0]['dolar'];
        $iva = $sistema[0]['iva'];
        $astilleroCotizacion->setDolar($dolar);
        $form = $this->createForm('AppBundle\Form\AstilleroCotizacionType', $astilleroCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $granSubtotal = 0;
            $granIva = 0;
            $granTotal = 0;

            $llegada = $astilleroCotizacion->getFechaLlegada();
            $salida = $astilleroCotizacion->getFechaSalida();
            $diferenciaDias = date_diff($llegada, $salida);
            $cantidadDias = ($diferenciaDias->days);

            // Uso de grua
            $servicio = $this->getDoctrine()
                            ->getRepository(AstilleroServicioBasico::class)
                            ->find(1);
            $cantidad = $astilleroGrua->getCantidad();
            $precio = $astilleroGrua->getPrecio();
            if($precio==null){
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;


            $astilleroGrua
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null)
                ->setAstilleroserviciobasico($servicio);
            $astilleroGrua->setIva($ivaTot);
            $astilleroGrua->setSubtotal($subTotal);
            $astilleroGrua->setTotal($total);
            $astilleroGrua->setEstatus(true);
            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granTotal+=$total;

            // Uso de suelo
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(2);
            $cantidad = $cantidadDias;
            $precio = $astilleroSuelo->getPrecio();
            if($precio==null){
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;

            $astilleroSuelo
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null);

            $astilleroSuelo->setEstatus(1);
            $astilleroSuelo->setCantidad($cantidad);
            $astilleroSuelo->setSubtotal($subTotal);
            $astilleroSuelo->setIva($ivaTot);
            $astilleroSuelo->setTotal($total);

            $granSubtotal+=$subTotal;
            $granIva+=$ivaTot;
            $granTotal+=$total;

            // Uso de rampa
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(3);
            $cantidad = 1;
            $precio = $astilleroRampa->getPrecio();
            if($precio==null){
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;

            $astilleroRampa
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null);
            $astilleroSuelo->setCantidad($cantidad);
            $astilleroSuelo->setSubtotal($subTotal);
            $astilleroSuelo->setIva($ivaTot);
            $astilleroSuelo->setTotal($total);
            ;
            if($astilleroRampa->getEstatus()){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granTotal+=$total;
            }

            // Uso de karcher
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(4);
            $cantidad =1;
            $precio = $astilleroKarcher->getPrecio();
            if($precio==null){
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;

            $astilleroKarcher
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null);
            $astilleroSuelo->setCantidad($cantidad);
            $astilleroSuelo->setSubtotal($subTotal);
            $astilleroSuelo->setIva($ivaTot);
            $astilleroSuelo->setTotal($total);

            if($astilleroKarcher->getEstatus()){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granTotal+=$total;
            }

            // sacar varada y botadura
            $servicio = $this->getDoctrine()
                ->getRepository(AstilleroServicioBasico::class)
                ->find(5);
            $cantidad = $astilleroVarada->getCantidad();
            $precio = $astilleroVarada->getPrecio();
            if($precio==null){
                $precio = 0;
            }
            $subTotal = $cantidad * $precio;
            $ivaTot = ($subTotal * $iva)/100;
            $total = $subTotal + $ivaTot;

            $astilleroVarada
                ->setAstilleroserviciobasico($servicio)
                ->setServicio(null)
                ->setProducto(null)
                ->setOtroservicio(null);
            $astilleroSuelo->setSubtotal($subTotal);
            $astilleroSuelo->setIva($ivaTot);
            $astilleroSuelo->setTotal($total);
            ;
            if($astilleroVarada->getEstatus()){
                $granSubtotal+=$subTotal;
                $granIva+=$ivaTot;
                $granTotal+=$total;
            }

            foreach ($astilleroCotizacion->getAcservicios() as $servAst){
              if($servAst->getAstilleroserviciobasico() ==null){
                  $cantidad = $servAst->getCantidad();
                  $precio = $servAst->getPrecio();
                  $subTotal = $cantidad * $precio;
                  $ivaTot = ($subTotal * $iva)/100;
                  $total = $subTotal + $ivaTot;
                  $servAst->setSubtotal($subTotal);
                  $servAst->setIva($ivaTot);
                  $servAst->setTotal($total);
                  $servAst->setEstatus(true);

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
                ->setFecharegistro($fechaHoraActual)
                ->setDolar($astilleroCotizacion->getDolar())
                ->setEstatus(true);
            $astilleroCotizacion->setValidanovo(0);
            $astilleroCotizacion->setValidacliente(0);
            $em->persist($astilleroCotizacion);
            $em->flush();

            return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
        }

        return $this->render('astillero/cotizacion/new.html.twig', [
            'title' => 'Nueva cotización',
            'astilleroCotizacion' => $astilleroCotizacion,
            'valdolar' => $dolar,
            'valiva' => $iva,
            'form' => $form->createView()
        ]);
    }

    /**
     * Muestra una cotizacion de astillero
     *
     * @Route("/{id}", name="astillero_show")
     * @Method("GET")
     */
    public function showAction(AstilleroCotizacion $astilleroCotizacion)
    {
        $deleteForm = $this->createDeleteForm($astilleroCotizacion);

        return $this->render('astillero/cotizacion/show.html.twig', [
            'title' => 'Cotización',
            'astilleroCotizacion' => $astilleroCotizacion,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Editar una cotizacion
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

            return $this->redirectToRoute('astillero_edit', ['id' => $astilleroCotizacion->getId()]);
        }

        return $this->render('astillero/cotizacion/edit.html.twig', [
            'title' => 'Editar cotizacion',
            'astilleroCotizacion' => $astilleroCotizacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }
    /**
     *
     * @Route("/{id}/validar", name="astillero_validar")
     * @Method({"GET", "POST"})
     **/
    public function validaAction(Request $request, AstilleroCotizacion $astilleroCotizacion,\Swift_Mailer $mailer)
    {
        if ($astilleroCotizacion->isEstatus() == 0 ||
            $astilleroCotizacion->getValidanovo() == 1 ||
            $astilleroCotizacion->getValidanovo() == 2
            //    $marinaHumedaCotizacion->getValidacliente() ==1 ||
            //    $marinaHumedaCotizacion->getValidacliente() ==2
        ) {
            throw new NotFoundHttpException();
        }
        $valorSistema = new ValorSistema();
        //$servicios = $marinaHumedaCotizacion->getMHCservicios();
        $editForm = $this->createForm( 'AppBundle\Form\AstilleroCotizacionValidarType', $astilleroCotizacion);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if($astilleroCotizacion->getValidanovo()==2){
                $tokenAcepta = $valorSistema->generaToken(100);
                $tokenRechaza = $valorSistema->generaToken(100);
//                $astilleroCotizacion
//                    ->setTokenacepta($tokenAcepta)
//                    ->setTokenrechaza($tokenRechaza)
//                    ->setNombrevalidanovo($this->getUser()->getNombre());


//                // creando pdf
//                $html = $this->renderView('marinahumeda/cotizacion/pdf/cotizacionpdf.html.twig', [
//                    'title' => 'Cotizacion-'.$astilleroCotizacion->getFolio().'.pdf',
//                    'marinaHumedaCotizacion' => $astilleroCotizacion
//                ]);
//                $header = $this->renderView('marinahumeda/cotizacion/pdf/pdfencabezado.twig', [
//                    'marinaHumedaCotizacion' => $astilleroCotizacion
//                ]);
//                $footer = $this->renderView('marinahumeda/cotizacion/pdf/pdfpie.twig', [
//                    'marinaHumedaCotizacion' => $astilleroCotizacion
//                ]);
//                $hojapdf = $this->get('knp_snappy.pdf');
//                $options = [
//                    'margin-top'    => 23,
//                    'margin-right'  => 0,
//                    'margin-bottom' => 33,
//                    'margin-left'   => 0,
//                    'header-html' => utf8_decode($header),
//                    'footer-html' => utf8_decode($footer)
//                ];
//                $pdfEnviar = new PdfResponse(
//                    $hojapdf->getOutputFromHtml($html,$options),
//                    'Cotizacion-'.$astilleroCotizacion
//                        ->getFolio().'-'.$astilleroCotizacion
//                        ->getFoliorecotiza().'.pdf', 'application/pdf', 'inline'
//                );
//                $attachment = new Swift_Attachment($pdfEnviar, 'Cotizacion-'.$marinaHumedaCotizacion->getFolio().'-'.$marinaHumedaCotizacion->getFoliorecotiza().'.pdf', 'application/pdf');
                // Enviar correo de confirmacion
                $message = (new \Swift_Message('¡Cotizacion de servicios!'))
                    ->setFrom('noresponder@novonautica.com')
                    ->setTo($astilleroCotizacion->getCliente()->getCorreo())
                    ->setBcc('admin@novonautica.com')
                    ->setBody(
                        'Cotizacion validada por novo'
//                        $this->renderView('marinahumeda/cotizacion/correo-clientevalida.twig', [
//                            'astilleroCotizacion' => $astilleroCotizacion,
//                            'tokenAcepta' => $tokenAcepta,
//                            'tokenRechaza' => $tokenRechaza
////                        ]
//            ),
//                        'text/html'
                    );
                    //->attach($attachment);

                $mailer->send($message);

//                if($astilleroCotizacion->getFoliorecotiza() == 0){
//                    $folio = $astilleroCotizacion->getFolio();
//                    $tipoCorreo = 1;
//                }else{
//                    $folio = $astilleroCotizacion->getFolio().'-'.$astilleroCotizacion->getFoliorecotiza();
//                    $tipoCorreo = 2;
//                }
//                $historialCorreo = new Correo();
//                $historialCorreo->setFecha(new \DateTime('now'))->setTipo($tipoCorreo)->setDescripcion('Envio de cotización con Folio: '.$folio);
//                $em->persist($historialCorreo);
            }
//            else{
//                if($astilleroCotizacion->getValidanovo()==1){
//                    $astilleroCotizacion->setNombrevalidanovo($this->getUser()->getNombre());
//                }
//            }
            //$this->getDoctrine()->getManager()->flush();
            $em->persist($astilleroCotizacion);
            $em->flush();
            return $this->redirectToRoute('astillero_show', ['id' => $astilleroCotizacion->getId()]);
        }

        return $this->render('astillero/cotizacion/validar.html.twig', [
            'title' => 'Validación',
            'astilleroCotizacion' => $astilleroCotizacion,
            'edit_form' => $editForm->createView()
        ]);
    }
    /**
     * Elimina una cotizacion
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
