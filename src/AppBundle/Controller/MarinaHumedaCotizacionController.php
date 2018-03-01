<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Correo;
use AppBundle\Entity\CotizacionNota;
use AppBundle\Entity\CuentaBancaria;
use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\ValorSistema;
use AppBundle\Form\CotizacionNotaType;
use AppBundle\Form\MarinaHumedaCotizacionAceptadaType;
use AppBundle\Form\MarinaHumedaCotizacionGasolinaType;
use AppBundle\Form\MarinaHumedaCotizacionNotaType;
use AppBundle\Form\MarinaHumedaCotizacionRechazadaType;
use AppBundle\Form\MarinaHumedaCotizacionType;
use Doctrine\Common\Collections\ArrayCollection;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * Marinahumedacotizacion controller.
 *
 * @Route("/marina/cotizacion")
 */
class MarinaHumedaCotizacionController extends Controller
{

    /**
     * @Route("/gracias", name="marina-humeda_gracias")
     * @Method("GET")
     */
    public function graciasAction()
    {
        return $this->render('marinahumeda/cotizacion/gracias.twig', [
        ]);
    }

    /**
     * Enlista todas las cotizaciones estadias
     *
     * @Route("/estadia/", name="marina-humeda_estadia_index")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return JsonResponse|Response
     */
    public function indexEstadiaAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $datatables = $this->get('datatables');
                $results = $datatables->handle($request, 'cotizacionEstadia');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }

        return $this->render('marinahumeda/cotizacion/estadia/index.html.twig', ['title' => 'Cotizaciones de Estadias']);
    }

    /**
     * Enlista todas las cotizaciones gasolina
     *
     * @Route("/gasolina/", name="marina-humeda_gasolina_index")
     * @Method("GET")
     */
    public function indexGasolinaAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $datatables = $this->get('datatables');
                $results = $datatables->handle($request, 'cotizacionGasolina');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }

        return $this->render('marinahumeda/cotizacion/gasolina/index.html.twig', ['title' => 'Cotizaciones de Gasolina']);
    }

    /**
     * Crea una nueva cotizacion
     *
     * @Route("/estadia/nuevo", name="marina-humeda_estadia_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $marinaDiasEstadia = new MarinaHumedaCotizaServicios();
        $marinaElectricidad = new MarinaHumedaCotizaServicios();

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
        $sistema = $query->getArrayResult();

        $dolarBase = $sistema[0]['dolar'];
        $iva = $sistema[0]['iva'];
        $mensaje = $sistema[0]['mensajeCorreoMarina'];

        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($marinaDiasEstadia)
            ->addMarinaHumedaCotizaServicios($marinaElectricidad)
            ->setMensaje($mensaje);


        $form = $this->createForm(MarinaHumedaCotizacionType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $granSubtotal = 0;
            $granIva = 0;
            $granDescuento = 0;
            $granTotal = 0;
            $descuento = $marinaHumedaCotizacion->getDescuento();
            $eslora = $marinaHumedaCotizacion->getBarco()->getEslora();
            $dolar = $marinaHumedaCotizacion->getDolar();

//            $llegada = $marinaHumedaCotizacion->getFechaLlegada();
//            $salida = $marinaHumedaCotizacion->getFechaSalida();
//            $diferenciaDias = date_diff($llegada, $salida);
//            $cantidadDias = ($diferenciaDias->days);
            $cantidadDias = $marinaHumedaCotizacion->getDiasEstadia();

            // Días Estadía
            $tiposervicio = 1;
            $precio = $marinaDiasEstadia->getPrecio()->getCosto();
            $subTotal = $cantidadDias * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva) / 100;
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

            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granDescuento += $descuentoTot;
            $granTotal += $total;

            // Conexión a electricidad
            $tiposervicio = 2;
            $precio = $marinaElectricidad->getPrecioAux()->getCosto();
            $subTotal = $cantidadDias * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva) / 100;
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

            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granDescuento += $descuentoTot;
            $granTotal += $total;

            //-------------------------------------------------

            $fechaHoraActual = new \DateTime('now');
            $foliobase = $sistema[0]['folioMarina'];
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
            $this->getDoctrine()
                ->getRepository(ValorSistema::class)
                ->find(1)
                ->setFolioMarina($folionuevo);

            $em->persist($marinaHumedaCotizacion);
            $em->flush();

            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }

        return $this->render('marinahumeda/cotizacion/estadia/new.html.twig', [
            'title' => 'Nueva cotización',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'valdolar' => $dolarBase,
            'valiva' => $iva,
            'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/gasolina/nuevo", name="marina-humeda_gasolina_new")
     * @Method({"GET", "POST"})
     */
    public function newGasolinaAction(Request $request)
    {
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $marinaGasolina = new MarinaHumedaCotizaServicios();
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
        $sistema = $query->getArrayResult();

        $dolarBase = $sistema[0]['dolar'];
        $iva = $sistema[0]['iva'];
        $mensaje = $sistema[0]['mensajeCorreoMarinaGasolina'];

        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($marinaGasolina)
            ->setMensaje($mensaje);
        $form = $this->createForm(MarinaHumedaCotizacionGasolinaType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dolar = $marinaHumedaCotizacion->getDolar();
            $cantidad = $marinaGasolina->getCantidad();
            $precioUSD = (round(((($marinaGasolina->getPrecio() / $dolar) * 100) / ($iva + 100)), 2)) * 100;
            $subtotalUSD = ($cantidad * $precioUSD);
            $ivaUSD = ($subtotalUSD * ($iva / 100));
            $totalUSD = ($subtotalUSD + $ivaUSD);

            $foliobase = $sistema[0]['folioMarina'];
            $folionuevo = $foliobase + 1;

            $marinaGasolina
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setPrecio($precioUSD) // Precio sin iva
                ->setSubtotal($subtotalUSD) // Total sin iva
                ->setIva($ivaUSD) // El iva del total
                ->setTotal($totalUSD); // Total con iva
            ;
            $marinaHumedaCotizacion
                ->setIva($iva)
                ->setSubtotal($subtotalUSD)
                ->setIvatotal($ivaUSD)
                ->setTotal($totalUSD)
                ->setValidanovo(0)
                ->setValidacliente(0)
                ->setEstatus(1)
                ->setFecharegistro(new \DateTime())
                ->setFolio($folionuevo)
                ->setFoliorecotiza(0);

            $this->getDoctrine()
                ->getRepository(ValorSistema::class)
                ->find(1)
                ->setFolioMarina($folionuevo);

            $em->persist($marinaGasolina);
            $em->persist($marinaHumedaCotizacion);
            $em->flush();

            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);

        }
        return $this->render('marinahumeda/cotizacion/gasolina/new.html.twig', [
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
        $html = $this->renderView('marinahumeda/cotizacion/pdf/cotizacionpdf.html.twig', [
            'title' => 'Cotizacion-' . $mhc->getFolio() . '.pdf',
            'marinaHumedaCotizacion' => $mhc
        ]);
        $header = $this->renderView('marinahumeda/cotizacion/pdf/pdfencabezado.twig', [
            'marinaHumedaCotizacion' => $mhc
        ]);
        $footer = $this->renderView('marinahumeda/cotizacion/pdf/pdfpie.twig', [
            'marinaHumedaCotizacion' => $mhc
        ]);
        $hojapdf = $this->get('knp_snappy.pdf');
        $options = [
            'margin-top' => 23,
            'margin-right' => 0,
            'margin-bottom' => 33,
            'margin-left' => 0,
            'header-html' => utf8_decode($header),
            'footer-html' => utf8_decode($footer)
        ];
        return new PdfResponse(
            $hojapdf->getOutputFromHtml($html, $options),
            'Cotizacion-' . $mhc
                ->getFolio() . '-' . $mhc
                ->getFoliorecotiza() . '.pdf', 'application/pdf', 'inline'
        );
    }

    /**
     * Confirma la respuesta de un cliente a una cotizacion
     *
     * @Route("/{token}/confirma", name="respuesta-cliente")
     * @Method({"GET", "POST"})
     *
     * @param $token
     *
     * @return Response
     */
    public function repuestaCliente(Request $request, $token)
    {

        $em = $this->getDoctrine()->getManager();
        $cotizacionAceptar = $em->getRepository(MarinaHumedaCotizacion::class)
            ->findOneBy(['tokenacepta' => $token]);

        if ($cotizacionAceptar) {
            $cuentaBancaria = $em->getRepository(CuentaBancaria::class)
                ->findAll();
            $qb = $em->createQueryBuilder();
            $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
            $sistema = $query->getArrayResult();

            $diasHabiles = $sistema[0]['diasHabilesMarinaCotizacion'];

            if ($cotizacionAceptar->getFoliorecotiza() == 0) {
                $folio = $cotizacionAceptar->getFolio();
            } else {
                $folio = $cotizacionAceptar->getFolio() . '-' . $cotizacionAceptar->getFoliorecotiza();
            }

            $valorSistema = new ValorSistema();
            $codigoSeguimiento = $folio . '-' . $valorSistema->generaToken(10);

            $cotizacionAceptar
                ->setValidacliente(2)
                ->setCodigoseguimiento($codigoSeguimiento);

            // Fecha en la que acepto el cliente
            $cotizacionAceptar->setRegistroValidaCliente(new \DateTimeImmutable());

            $em->persist($cotizacionAceptar);
            $em->flush();

            $mensaje1 = '¡Enhorabuena!';
            $mensaje2 = 'La cotización ' . $folio . ' ha sido aprobada.';
            $suformulario = 1;

            $editForm = $this->createForm(MarinaHumedaCotizacionAceptadaType::class, $cotizacionAceptar);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $cotizacionAceptar->setFecharespuesta(new \DateTime('now'));
                $em->persist($cotizacionAceptar);
                $em->flush();
                return $this->redirectToRoute('marina-humeda_gracias');
            }

            return $this->render('marinahumeda/cotizacion/respuesta-cliente.twig', [
                'mensaje1' => $mensaje1,
                'mensaje2' => $mensaje2,
                'suformulario' => $suformulario,
                'cuentaBancaria' => $cuentaBancaria,
                'diasHabiles' => $diasHabiles,
                'form' => $editForm->createView(),
                'marinaHumedaCotizacion' => $cotizacionAceptar
            ]);
        }
        else {
            $cotizacionRechazar = $em->getRepository(MarinaHumedaCotizacion::class)
                ->findOneBy(['tokenrechaza' => $token]);

            if ($cotizacionRechazar) {
                $cotizacionRechazar->setValidacliente(1);
                $cotizacionRechazar->setRegistroValidaCliente(new \DateTimeImmutable());
                $em->persist($cotizacionRechazar);
                $em->flush();

                if ($cotizacionRechazar->getFoliorecotiza() == 0) {
                    $folio = $cotizacionRechazar->getFolio();
                } else {
                    $folio = $cotizacionRechazar->getFolio() . '-' . $cotizacionRechazar->getFoliorecotiza();
                }

                $mensaje1 = '¡Oh-oh!';
                $mensaje2 = 'La cotización ' . $folio . ' no ha sido aprobada.';
                $mensaje3 = 'Nos gustaría saber su opinión o comentarios del motivo de su rechazo.';
                $suformulario = 2;

                $editForm = $this->createForm(MarinaHumedaCotizacionRechazadaType::class, $cotizacionRechazar);
                $editForm->handleRequest($request);

                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $em->flush();
                    return $this->redirectToRoute('marina-humeda_gracias');
                }

            }

            return $this->render('marinahumeda/cotizacion/respuesta-cliente.twig', [
                'mensaje1' => $mensaje1,
                'mensaje2' => $mensaje2,
                'mensaje3' => $mensaje3,
                'suformulario' => $suformulario,
                'form' => $editForm->createView()
            ]);
        }
    }

    /**
     * @Route("/{id}/pago", name="marina_cotizacion_pago_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacion $marinaHumedaCotizacion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editPagoAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        //$pago = new Pago();
        //$marinaHumedaCotizacion->addPago($pago);
        $totPagado = 0;
        $listaPagos = new ArrayCollection();

        foreach ($marinaHumedaCotizacion->getPagos() as $pago) {
            if($pago->getDivisa()=='MXN'){
                $pesos = ($pago->getCantidad()*$pago->getDolar())/100;
                $pago->setCantidad($pesos);
            }
            $listaPagos->add($pago);
        }

        $form = $this->createForm('AppBundle\Form\MarinaHumedaRegistraPagoType', $marinaHumedaCotizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $total = $marinaHumedaCotizacion->getTotal();
            $pagado = $marinaHumedaCotizacion->getPagado();

            $em = $this->getDoctrine()->getManager();

            foreach ($listaPagos as $pago) {
                if (false === $marinaHumedaCotizacion->getPagos()->contains($pago)) {
                    $pago->getMhcotizacion()->removePago($pago);
                    $em->persist($pago);
                    $em->remove($pago);
                }
            }
            $unpago = 0;
            foreach ($marinaHumedaCotizacion->getPagos() as $pago) {
                if($pago->getDivisa()=='MXN'){
                    $unpago = ($pago->getCantidad()/$pago->getDolar())*100;
                    $pago->setCantidad($unpago);
                }else{
                    $unpago = $pago->getCantidad();
                }
                $totPagado += $unpago;
            }

            if ($total < $totPagado) {
                $this->addFlash('notice', 'Error! Se ha intentado pagar más del total');
            } else {
                $faltante = $total - $totPagado;
                if ($faltante == 0) {
                    $marinaHumedaCotizacion->setRegistroPagoCompletado(new \DateTimeImmutable());
                    $marinaHumedaCotizacion->setEstatuspago(2);
                } else {
                    $marinaHumedaCotizacion->setEstatuspago(1);
                }
                $marinaHumedaCotizacion
                    ->setPagado($totPagado);
                $em->persist($marinaHumedaCotizacion);
                $em->flush();
                return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
            }
        }

        return $this->render('marinahumeda/cotizacion/pago/edit.html.twig', [
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @Route("/{id}/nota", name="marina-humeda_nota")
     * @Method({"GET", "POST"})
     */
    public function agregaNotaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        $em = $this->getDoctrine()->getManager();
        $cotizacionnota = new CotizacionNota();
        $marinaHumedaCotizacion->addCotizacionnota($cotizacionnota);
        $form = $this->createForm(CotizacionNotaType::class, $cotizacionnota);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fechaHoraActual = new \DateTimeImmutable('now');
            $cotizacionnota->setFechahoraregistro($fechaHoraActual);
            $em->persist($marinaHumedaCotizacion);
            $em->flush();
            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }
        return $this->render('marinahumeda/cotizacion/nota/new.html.twig', [
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @Route("/estadia/{id}/renovar", name="marina-humeda_estadia_renovar")
     * @Method({"GET", "POST"})
     */
    public function renuevaEstadiaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior)
    {
        if ($marinaHumedaCotizacionAnterior->getValidacliente() != 2) {
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('AppBundle:ValorSistema')->findOneBy(['id' => 1]);
        $dolar = $qb->getDolar();
        $iva = $qb->getIva();
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        $cliente = $marinaHumedaCotizacionAnterior->getCliente();
        $barco = $marinaHumedaCotizacionAnterior->getBarco();

        $marinaHumedaCotizacion
            ->setCliente($cliente)
            ->setBarco($barco)
            ->setFechaLlegada($marinaHumedaCotizacionAnterior->getFechaLlegada())
            ->setFechaSalida($marinaHumedaCotizacionAnterior->getFechaSalida())
            ->setSlip($marinaHumedaCotizacionAnterior->getSlip())
            ->setDescuento($marinaHumedaCotizacionAnterior->getDescuento())
            ->setDolar($dolar)
            ->setIva($iva)
            ->setSubtotal($marinaHumedaCotizacionAnterior->getSubtotal())
            ->setIvatotal($marinaHumedaCotizacionAnterior->getIvatotal())
            ->setDescuentototal($marinaHumedaCotizacionAnterior->getDescuentototal())
            ->setTotal($marinaHumedaCotizacionAnterior->getTotal())
            ->setValidanovo(0)
            ->setValidacliente(0)
            ->setMensaje($marinaHumedaCotizacionAnterior->getMensaje());
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
            ->setEstatus($servicios[0]->getEstatus());
        $marinaElectricidad = new MarinaHumedaCotizaServicios();
        $marinaElectricidad
            ->setTipo($servicios[1]->getTipo())
            ->setCantidad($servicios[1]->getCantidad())
            ->setPrecio($servicios[1]->getPrecio())
            ->setSubtotal($servicios[1]->getSubtotal())
            ->setIva($servicios[1]->getIva())
            ->setDescuento($servicios[1]->getDescuento())
            ->setTotal($servicios[1]->getTotal())
            ->setEstatus($servicios[1]->getEstatus());
        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($marinaDiasEstadia)
            ->addMarinaHumedaCotizaServicios($marinaElectricidad);
        $form = $this->createForm(MarinaHumedaCotizacionType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $granSubtotal = 0;
            $granIva = 0;
            $granDescuento = 0;
            $granTotal = 0;
            $descuento = $marinaHumedaCotizacion->getDescuento();
            $eslora = $marinaHumedaCotizacion->getBarco()->getEslora();

            $llegada = $marinaHumedaCotizacion->getFechaLlegada();
            $salida = $marinaHumedaCotizacion->getFechaSalida();

            $diferenciaDias = date_diff($llegada, $salida);

            $cantidad = ($diferenciaDias->days);
            // Días Estadía
            $precio = $marinaDiasEstadia->getPrecio()->getCosto();

            $subTotal = $cantidad * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaDiasEstadia
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setPrecio($precio)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granDescuento += $descuentoTot;
            $granTotal += $total;

            // Conexión a electricidad
            //$cantidad = $marinaElectricidad->getCantidad();
            $precio = $marinaElectricidad->getPrecioAux()->getCosto();

            $subTotal = $cantidad * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaElectricidad
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setPrecio($precio)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granDescuento += $descuentoTot;
            $granTotal += $total;

            //-------------------------------------------------
            $fechaHoraActual = new \DateTime('now');
            $foliobase = $qb->getFolioMarina();
            $folionuevo = $foliobase + 1;

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
                ->setFecharegistro($fechaHoraActual)
                ->setFolio($folionuevo)
                ->setFoliorecotiza(0);
            $folioactualiza = $this->getDoctrine()
                ->getRepository(ValorSistema::class)
                ->find(1)
                ->setFolioMarina($folionuevo);
            $em->persist($marinaHumedaCotizacion);
            $em->flush();

            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);

        }
        return $this->render('marinahumeda/cotizacion/estadia/recotizar.html.twig', [
            'title' => 'Renovación',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Muestra una cotizacion para recotizar
     *
     * @Route("/estadia/{id}/recotizar", name="marina-humeda_estadia_recotizar")
     * @Method({"GET", "POST"})
     */
    public function recotizaEstadiaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior)
    {
        if ($marinaHumedaCotizacionAnterior->getEstatus() == 0 ||
            $marinaHumedaCotizacionAnterior->getValidacliente() == 2 ||
            $marinaHumedaCotizacionAnterior->getValidanovo() == 0 ||
            ($marinaHumedaCotizacionAnterior->getValidanovo() == 2 && $marinaHumedaCotizacionAnterior->getValidacliente() == 0)
        ) {
            throw new NotFoundHttpException();
        }

        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();
        //       $marinaHumedaCotizacion = clone $marinaHumedaCotizacionAnterior;
        $foliorecotizado = $marinaHumedaCotizacionAnterior->getFoliorecotiza() + 1;
        $cliente = $marinaHumedaCotizacionAnterior->getCliente();
        $barco = $marinaHumedaCotizacionAnterior->getBarco();
//        $marinaHumedaCotizacion
//            ->setFoliorecotiza($foliorecotizado)
//            ->setNotasnovo(null)
//            ->setValidanovo(0)
//            ->setFecharegistro(null)
//            ->setNombrevalidanovo(null)
//        ;


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
            ->setMensaje($marinaHumedaCotizacionAnterior->getMensaje());


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
            ->setEstatus($servicios[0]->getEstatus());

        $marinaElectricidad = new MarinaHumedaCotizaServicios();
        $marinaElectricidad
            ->setTipo($servicios[1]->getTipo())
            ->setCantidad($servicios[1]->getCantidad())
            ->setPrecio($servicios[1]->getPrecio())
            ->setSubtotal($servicios[1]->getSubtotal())
            ->setIva($servicios[1]->getIva())
            ->setDescuento($servicios[1]->getDescuento())
            ->setTotal($servicios[1]->getTotal())
            ->setEstatus($servicios[1]->getEstatus());

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
            $granDescuento = 0;
            $granTotal = 0;
            $descuento = $marinaHumedaCotizacion->getDescuento();
            $eslora = $marinaHumedaCotizacion->getBarco()->getEslora();

            $llegada = $marinaHumedaCotizacion->getFechaLlegada();
            $salida = $marinaHumedaCotizacion->getFechaSalida();

            $diferenciaDias = date_diff($llegada, $salida);

            $cantidad = ($diferenciaDias->days);
            // Días Estadía
            $precio = $marinaDiasEstadia->getPrecio()->getCosto();

            $subTotal = $cantidad * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaDiasEstadia
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setPrecio($precio)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granDescuento += $descuentoTot;
            $granTotal += $total;

            // Conexión a electricidad
            //$cantidad = $marinaElectricidad->getCantidad();
            $precio = $marinaElectricidad->getPrecioAux()->getCosto();

            $subTotal = $cantidad * $precio * $eslora;
            $descuentoTot = ($subTotal * $descuento) / 100;
            $ivaTot = ($subTotal * $iva) / 100;
            $total = $subTotal - $descuentoTot + $ivaTot;

            $marinaElectricidad
                ->setEstatus(1)
                ->setCantidad($cantidad)
                ->setPrecio($precio)
                ->setSubtotal($subTotal)
                ->setDescuento($descuentoTot)
                ->setIva($ivaTot)
                ->setTotal($total);

            $granSubtotal += $subTotal;
            $granIva += $ivaTot;
            $granDescuento += $descuentoTot;
            $granTotal += $total;

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
        return $this->render('marinahumeda/cotizacion/estadia/recotizar.html.twig', [
            'title' => 'Recotización',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Muestra una cotizacion para recotizar
     *
     * @Route("/gasolina/{id}/recotizar", name="marina-humeda_gasolina_recotizar")
     * @Method({"GET", "POST"})
     */
    public function recotizaGasolinaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacionAnterior)
    {
        if ($marinaHumedaCotizacionAnterior->getEstatus() == 0 ||
            $marinaHumedaCotizacionAnterior->getValidacliente() == 2 ||
            $marinaHumedaCotizacionAnterior->getValidanovo() == 0 ||
            ($marinaHumedaCotizacionAnterior->getValidanovo() == 2 && $marinaHumedaCotizacionAnterior->getValidacliente() == 0)
        ) {
            throw new NotFoundHttpException();
        }
        $marinaHumedaCotizacion = new MarinaHumedaCotizacion();

        $cliente = $marinaHumedaCotizacionAnterior->getCliente();
        $barco = $marinaHumedaCotizacionAnterior->getBarco();
        $dolar = $marinaHumedaCotizacionAnterior->getDolar();
        $iva = $marinaHumedaCotizacionAnterior->getIva();
        $marinaHumedaCotizacion
            ->setCliente($cliente)
            ->setBarco($barco)
            ->setDescuento($marinaHumedaCotizacionAnterior->getDescuento())
            ->setDolar($dolar)
            ->setIva($marinaHumedaCotizacionAnterior->getIva())
            ->setSubtotal($marinaHumedaCotizacionAnterior->getSubtotal())
            ->setIvatotal($marinaHumedaCotizacionAnterior->getIvatotal())
            ->setDescuentototal($marinaHumedaCotizacionAnterior->getDescuentototal())
            ->setTotal($marinaHumedaCotizacionAnterior->getTotal())
            ->setValidanovo(0)
            ->setValidacliente(0)
            ->setFolio($marinaHumedaCotizacionAnterior->getFolio())
            ->setMensaje($marinaHumedaCotizacionAnterior->getMensaje());
        $servicios = $marinaHumedaCotizacionAnterior->getMHCservicios();
        $precioNoIncluyeIvaUSD = ($servicios[0]->getPrecio() * $dolar) / 100;
        $ivaDelPrecioGardardoUSD = ($precioNoIncluyeIvaUSD * $iva) / 100;
        $precioIncluyeIvaUSD = $precioNoIncluyeIvaUSD + $ivaDelPrecioGardardoUSD;
        $marinaGasolina = new MarinaHumedaCotizaServicios();
        $marinaGasolina
            ->setTipo($servicios[0]->getTipo())
            ->setCantidad($servicios[0]->getCantidad())
            ->setPrecio($precioIncluyeIvaUSD)
            ->setSubtotal($servicios[0]->getSubtotal())
            ->setIva($servicios[0]->getIva())
            ->setDescuento($servicios[0]->getDescuento())
            ->setTotal($servicios[0]->getTotal())
            ->setEstatus($servicios[0]->getEstatus());
        $marinaHumedaCotizacion
            ->addMarinaHumedaCotizaServicios($marinaGasolina);


        $form = $this->createForm(MarinaHumedaCotizacionGasolinaType::class, $marinaHumedaCotizacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $foliorecotizado = $marinaHumedaCotizacionAnterior->getFoliorecotiza() + 1;
            $descuento = $marinaHumedaCotizacion->getDescuento();
            $dolar = $marinaHumedaCotizacion->getDolar();
            $cantidad = $marinaGasolina->getCantidad();
            $precioConIvaMXN = $marinaGasolina->getPrecio();
            $precioConIvaUSD = $precioConIvaMXN / $dolar * 100;
            $totalConIvaUSD = $cantidad * $precioConIvaUSD;
            $ivaEquivalente = 100 + $iva; //116%
            $totalSinIvaUSD = (100 * $totalConIvaUSD) / $ivaEquivalente; //subtotal
            $ivaDelTotalUSD = $totalConIvaUSD - $totalSinIvaUSD;
            $precioSinIvaUSD = $totalSinIvaUSD / $cantidad;
            $fechaHoraActual = new \DateTime('now');
            $marinaGasolina
                ->setCantidad($cantidad)
                ->setPrecio($precioSinIvaUSD)
                ->setSubtotal($totalSinIvaUSD)
                ->setIva($ivaDelTotalUSD)
                ->setTotal($totalConIvaUSD);

            $marinaHumedaCotizacion
                ->setIva($iva)
                ->setSubtotal($totalSinIvaUSD)
                ->setIvatotal($ivaDelTotalUSD)
                ->setTotal($totalConIvaUSD)
                ->setValidanovo(0)
                ->setValidacliente(0)
                ->setEstatus(1)
                ->setFecharegistro($fechaHoraActual)
                ->setFoliorecotiza($foliorecotizado);
            $marinaHumedaCotizacionAnterior
                ->setEstatus(0);
            $em->persist($marinaGasolina);
            $em->persist($marinaHumedaCotizacion);
            $em->persist($marinaHumedaCotizacionAnterior);
            $em->flush();
            return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
        }

        return $this->render('marinahumeda/cotizacion/gasolina/recotizar.html.twig', [
            'title' => 'Recotización',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
            'form' => $form->createView(),
            'precioSinIvaUSD' => $precioNoIncluyeIvaUSD
        ]);

    }

    /**
     *
     * @Route("/{id}/validar", name="marina-humeda_validar")
     * @Method({"GET", "POST"})
     **/
    public function validaAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion, \Swift_Mailer $mailer)
    {
        if ($marinaHumedaCotizacion->getEstatus() == 0 ||
            $marinaHumedaCotizacion->getValidanovo() == 1 ||
            $marinaHumedaCotizacion->getValidacliente() == 1 ||
            $marinaHumedaCotizacion->getValidacliente() == 2
        ) {
            throw new NotFoundHttpException();
        }

        $valorSistema = new ValorSistema();
        $servicios = $marinaHumedaCotizacion->getMHCservicios();
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacion);

        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionType', $marinaHumedaCotizacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (is_null($marinaHumedaCotizacion->getTokenacepta())) {
                if ($marinaHumedaCotizacion->getValidanovo() == 2) {
                    $tokenAcepta = $valorSistema->generaToken(100);
                    $tokenRechaza = $valorSistema->generaToken(100);
                    $marinaHumedaCotizacion
                        ->setTokenacepta($tokenAcepta)
                        ->setTokenrechaza($tokenRechaza)
                        ->setNombrevalidanovo($this->getUser()->getNombre());

                    // Generacion de PDF
                    // Se envia un correo si se solicito notificar al cliente
                    if ($marinaHumedaCotizacion->isNotificarCliente()) {
                        $html = $this->renderView('marinahumeda/cotizacion/pdf/cotizacionpdf.html.twig', [
                            'title' => 'Cotizacion-' . $marinaHumedaCotizacion->getFolio() . '.pdf',
                            'marinaHumedaCotizacion' => $marinaHumedaCotizacion
                        ]);
                        $header = $this->renderView('marinahumeda/cotizacion/pdf/pdfencabezado.twig', [
                            'marinaHumedaCotizacion' => $marinaHumedaCotizacion
                        ]);
                        $footer = $this->renderView('marinahumeda/cotizacion/pdf/pdfpie.twig', [
                            'marinaHumedaCotizacion' => $marinaHumedaCotizacion
                        ]);
                        $hojapdf = $this->get('knp_snappy.pdf');
                        $options = [
                            'margin-top' => 23,
                            'margin-right' => 0,
                            'margin-bottom' => 33,
                            'margin-left' => 0,
                            'header-html' => utf8_decode($header),
                            'footer-html' => utf8_decode($footer)
                        ];
                        $pdfEnviar = new PdfResponse(
                            $hojapdf->getOutputFromHtml($html, $options),
                            'Cotizacion-' . $marinaHumedaCotizacion
                                ->getFolio() . '-' . $marinaHumedaCotizacion
                                ->getFoliorecotiza() . '.pdf', 'application/pdf', 'inline'
                        );
                        $attachment = new Swift_Attachment($pdfEnviar, 'Cotizacion-' . $marinaHumedaCotizacion->getFolio() . '-' . $marinaHumedaCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf');
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
                            )
                            ->attach($attachment);
                        if($marinaHumedaCotizacion->getBarco()->getCorreoCapitan()){
                            $message->addCc($marinaHumedaCotizacion->getBarco()->getCorreoCapitan());
                        }
                        if($marinaHumedaCotizacion->getBarco()->getCorreoResponsable()){
                            $message->addCc($marinaHumedaCotizacion->getBarco()->getCorreoResponsable());
                        }
                        $mailer->send($message);

                        if ($marinaHumedaCotizacion->getFoliorecotiza() == 0) {
                            $folio = $marinaHumedaCotizacion->getFolio();
                            $tipoCorreo = 'Cotización servicio Marina Humeda';
                        } else {
                            $folio = $marinaHumedaCotizacion->getFolio() . '-' . $marinaHumedaCotizacion->getFoliorecotiza();
                            $tipoCorreo = 'Recotización servicio Marina Humeda';
                        }

                        // Guardar correo en el log de correos
                        $historialCorreo = new Correo();
                        $historialCorreo
                            ->setFecha(new \DateTime('now'))
                            ->setTipo($tipoCorreo)
                            ->setDescripcion('Envio de cotización con folio: ' . $folio)
                            ->setFolioCotizacion($folio)
                            ->setMhcotizacion($marinaHumedaCotizacion)
                        ;

                        $em->persist($historialCorreo);
                    }



                } else {
                    if ($marinaHumedaCotizacion->getValidanovo() == 1) {
                        $marinaHumedaCotizacion->setNombrevalidanovo($this->getUser()->getNombre());
                    }
                }
            }

            // Guardar la fecha en la que se valido la cotizacion por novonautica
            $marinaHumedaCotizacion->setRegistroValidaNovo(new \DateTimeImmutable());

            $em->persist($marinaHumedaCotizacion);
            $em->flush();

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
     * @Route("/{id}/reenviar", name="marina-humeda_reenviar")
     * @Method({"GET", "POST"})
     **/
    public function reenviaCoreoAction(Request $request, MarinaHumedaCotizacion $marinaHumedaCotizacion, \Swift_Mailer $mailer)
    {

        $em = $this->getDoctrine()->getManager();

        $tokenAcepta = $marinaHumedaCotizacion->getTokenacepta();
        $tokenRechaza = $marinaHumedaCotizacion->getTokenrechaza();

        // creando pdf
        $html = $this->renderView('marinahumeda/cotizacion/pdf/cotizacionpdf.html.twig', [
            'title' => 'Cotizacion-' . $marinaHumedaCotizacion->getFolio() . '.pdf',
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion
        ]);
        $header = $this->renderView('marinahumeda/cotizacion/pdf/pdfencabezado.twig', [
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion
        ]);
        $footer = $this->renderView('marinahumeda/cotizacion/pdf/pdfpie.twig', [
            'marinaHumedaCotizacion' => $marinaHumedaCotizacion
        ]);
        $hojapdf = $this->get('knp_snappy.pdf');
        $options = [
            'margin-top' => 23,
            'margin-right' => 0,
            'margin-bottom' => 33,
            'margin-left' => 0,
            'header-html' => utf8_decode($header),
            'footer-html' => utf8_decode($footer)
        ];
        $pdfEnviar = new PdfResponse(
            $hojapdf->getOutputFromHtml($html, $options),
            'Cotizacion-' . $marinaHumedaCotizacion
                ->getFolio() . '-' . $marinaHumedaCotizacion
                ->getFoliorecotiza() . '.pdf', 'application/pdf', 'inline'
        );
        $attachment = new Swift_Attachment($pdfEnviar, 'Cotizacion-' . $marinaHumedaCotizacion->getFolio() . '-' . $marinaHumedaCotizacion->getFoliorecotiza() . '.pdf', 'application/pdf');



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
            )
            ->attach($attachment);

        if($marinaHumedaCotizacion->getBarco()->getCorreoCapitan()){
            $message->addCc($marinaHumedaCotizacion->getBarco()->getCorreoCapitan());
        }
        if($marinaHumedaCotizacion->getBarco()->getCorreoResponsable()){
            $message->addCc($marinaHumedaCotizacion->getBarco()->getCorreoResponsable());
        }
        $mailer->send($message);

        if ($marinaHumedaCotizacion->getFoliorecotiza() == 0) {
            $folio = $marinaHumedaCotizacion->getFolio();
            $tipoCorreo = 'Cotización servicio Marina Humeda';
        } else {
            $folio = $marinaHumedaCotizacion->getFolio() . '-' . $marinaHumedaCotizacion->getFoliorecotiza();
            $tipoCorreo = 'Recotización servicio Marina Humeda';
        }

        $historialCorreo = new Correo();
        $historialCorreo
            ->setFecha(new \DateTime('now'))
            ->setTipo($tipoCorreo)
            ->setDescripcion('Reenvio de cotización con Folio: ' . $folio)
            ->setFolioCotizacion($folio)
            ->setMhcotizacion($marinaHumedaCotizacion)
        ;

        $em->persist($historialCorreo);
        $em->persist($marinaHumedaCotizacion);
        $em->flush();

        //return $this->redirectToRoute('marina-humeda_show', ['id' => $marinaHumedaCotizacion->getId()]);
    }

    /**
     * @Route("/estadia/cliente.{_format}", defaults={"_format" = "json"})
     * @Route("/gasolina/cliente.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getClientesAction(Request $request)
    {
        $clientes = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion')->getAllClientes();

        return new Response($this->serializeEntities($clientes, $request->getRequestFormat()));
    }

    /**
     * @Route("/estadia/barco.{_format}", defaults={"_format" = "json"})
     * @Route("/gasolina/barco.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getBarcosAction(Request $request)
    {
        $barcos = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion')->getAllBarcos();

        return new Response($this->serializeEntities($barcos, $request->getRequestFormat()));
    }

    private function serializeEntities($entity, $format, $ignoredAttributes = []): string
    {
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $normalizer->setIgnoredAttributes($ignoredAttributes);

        return $serializer->serialize($entity, $format);
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
     * @return Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(MarinaHumedaCotizacion $marinaHumedaCotizacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda_delete', ['id' => $marinaHumedaCotizacion->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }


}
