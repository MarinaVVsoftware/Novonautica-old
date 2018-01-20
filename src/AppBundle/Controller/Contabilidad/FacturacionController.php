<?php

namespace AppBundle\Controller\Contabilidad;

use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Extra\NumberToLetter;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Facturacion controller.
 *
 * @Route("contabilidad/facturacion")
 */
class FacturacionController extends Controller
{
    /**
     * Lists all facturacion entities.
     *
     * @Route("/", name="contabilidad_facturacion_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $facturacions = $em->getRepository('AppBundle:Contabilidad\Facturacion')->findAll();

        return $this->render('contabilidad/facturacion/index.html.twig', [
            'facturacions' => $facturacions,
        ]);
    }

    /**
     * Creates a new facturacion entity.
     *
     * @Route("/new", name="contabilidad_facturacion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $factura = new Facturacion();
        $valorSistema = $em->getRepository('AppBundle:ValorSistema')->find(1);
        $factura->setTipoCambio($valorSistema->getDolar());
        $factura->setFolioCotizacion($valorSistema->getFolioMarina() + 1);
        $valorSistema->setFolioMarina($factura->getFolioCotizacion());

        $form = $this->createForm('AppBundle\Form\Contabilidad\FacturacionType', $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // checar que el folio no se repita en la facturacion
            /*if ($em->getRepository('AppBundle:Contabilidad\Facturacion')->findOneBy(['folioCotizacion' => $factura->getFolioCotizacion()])) {
                $this->addFlash('danger', 'Este folio ya ha sido facturado.');
                return $this->redirectToRoute('contabilidad_facturacion_new');
            }*/

            $facturador = $this->container->get('multifacturas');
            $timbrado = $facturador->procesa($factura);

            // Verificar que la factura se haya timbrado correctamente
            if ($timbrado['codigo_mf_numero']) {
                $this->addFlash('danger', $timbrado['codigo_mf_texto']);
                return $this->redirectToRoute('contabilidad_facturacion_new');
            }

            // Si se eligio un pago de una cotizacion, entonces relacionarlo con la factura
            if ($factura->getPagos()) { $factura->getPagos()->setFactura($factura); }
            $factura->setXml(trim($timbrado['cfdi']));
            $factura->setPng(trim($timbrado['png']));
            $factura->setXmlArchivo($timbrado['archivo_xml']);
            $factura->setPngArchivo($timbrado['archivo_png']);
            $factura->setFolioFiscal($timbrado['uuid']);
            $factura->setCadenaOriginal($timbrado['representacion_impresa_cadena']);
            $factura->setSerieCertificadoCSD($timbrado['representacion_impresa_certificado_no']);
            $factura->setFechaTimbrado((string) $timbrado['representacion_impresa_fecha_timbrado']);
            $factura->setSelloCFDI((string) $timbrado['representacion_impresa_sello']);
            $factura->setSelloSAT((string) $timbrado['representacion_impresa_selloSAT']);
            $factura->setCertificadoSAT((string) $timbrado['representacion_impresa_certificadoSAT']);

            $attachment = new Swift_Attachment(
                $this->createFacturaPDF($factura),
                'factura_' . $factura->getFolioCotizacion() . '.pdf',
                'application/pdf'
            );

            // Enviar correo de confirmacion
            $message = (new \Swift_Message('Factura de su pago realizado en ' . $factura->getFecha()->format('d/m/Y')))
                ->setFrom('noresponder@novonautica.com')
                ->setTo(explode(',', $factura->getEmail()))
                ->setBcc(explode(',', $factura->getEmisor()->getEmails()))
                ->setBody(
                    $this->renderView('contabilidad/facturacion/email/factura-template.html.twig'),
                    'text/html'
                )
                ->attach($attachment);

            $mailer->send($message);

            $em->persist($factura);
            $em->flush();

            return $this->redirectToRoute('contabilidad_facturacion_index');
        }

        return $this->render('contabilidad/facturacion/new.html.twig', [
            'facturacion' => $factura,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/factura", name="contabilidad_factura_pdf")
     * @Method("GET")
     */
    public function getFactura(Request $request)
    {
        $factura = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Contabilidad\Facturacion')
            ->find($request->query->get('id'));

        return $this->createFacturaPDF($factura);

        /*$numToLetters = new NumberToLetter();
        $numLetras = $numToLetters->to_word(($factura->getTotal() / 10), 'USD');
        $formaPago = [
            '01' => 'Efectivo',
            '02' => 'Cheque nominativo',
            '03' => 'Transferencia electrónica de fondos',
            '04' => 'Tarjeta de crédito',
            '05' => 'Monedero electrónico',
            '06' => 'Dinero electrónico',
            '08' => 'Vales de despensa',
            '12' => 'Dación en pago',
            '13' => 'Pago por subrogación',
            '14' => 'Pago por consignación',
            '15' => 'Condonación',
            '17' => 'Compensación',
            '23' => 'Novación',
            '24' => 'Confusión',
            '25' => 'Remisión de deuda',
            '26' => 'Prescripción o caducidad',
            '27' => 'A satisfacción del acreedor',
            '28' => 'Tarjeta de débito',
            '29' => 'Tarjeta de servicios',
            '99' => 'Por definir',
        ];

        $metodoPago = [
            'PUE' => 'Pago en una sola exhibición',
            'PIP' => 'Pago inicial y parcialidades',
            'PPD' => 'Pago en parcialidades o diferido',
        ];

        $html = $this->renderView(':contabilidad/facturacion/pdf:factura.html.twig', [
            'title' => 'factura_' . $factura->getFolioCotizacion() . '.pdf',
            'factura' => $factura,
            'numLetras' => $numLetras,
            'formaPago' => $formaPago[$factura->getFormaPago()],
            'metodoPago' => $metodoPago[$factura->getMetodoPago()],
        ]);

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            'factura_' . $factura->getFolioCotizacion() . '.pdf', 'application/pdf', 'inline'
        );*/
    }

    private function createFacturaPDF(Facturacion $factura)
    {
        $numToLetters = new NumberToLetter();
        $numLetras = $numToLetters->to_word(($factura->getTotal() / 10), 'USD');
        $formaPago = [
            '01' => 'Efectivo',
            '02' => 'Cheque nominativo',
            '03' => 'Transferencia electrónica de fondos',
            '04' => 'Tarjeta de crédito',
            '05' => 'Monedero electrónico',
            '06' => 'Dinero electrónico',
            '08' => 'Vales de despensa',
            '12' => 'Dación en pago',
            '13' => 'Pago por subrogación',
            '14' => 'Pago por consignación',
            '15' => 'Condonación',
            '17' => 'Compensación',
            '23' => 'Novación',
            '24' => 'Confusión',
            '25' => 'Remisión de deuda',
            '26' => 'Prescripción o caducidad',
            '27' => 'A satisfacción del acreedor',
            '28' => 'Tarjeta de débito',
            '29' => 'Tarjeta de servicios',
            '99' => 'Por definir',
        ];

        $metodoPago = [
            'PUE' => 'Pago en una sola exhibición',
            'PIP' => 'Pago inicial y parcialidades',
            'PPD' => 'Pago en parcialidades o diferido',
        ];

        $html = $this->renderView(':contabilidad/facturacion/pdf:factura.html.twig', [
            'title' => 'factura_' . $factura->getFolioCotizacion() . '.pdf',
            'factura' => $factura,
            'numLetras' => $numLetras,
            'formaPago' => $formaPago[$factura->getFormaPago()],
            'metodoPago' => $metodoPago[$factura->getMetodoPago()],
        ]);

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            'factura_' . $factura->getFolioCotizacion() . '.pdf', 'application/pdf', 'inline'
        );
    }

    /**
     * @Route("/cotizaciones.{_format}", defaults={"_format" = "html"})
     *
     * @param Request $request
     *
     * @return string
     */
    public function getAllCotizacionesAction(Request $request)
    {
        $em = $this->getDoctrine();
        $folio = $request->query->get('folio');
        $marinaCotizaciones = $em->getRepository('AppBundle:Contabilidad\Facturacion')->getAllCotizacionesxFacturar($folio);
        $marinaCotizaciones = $this->clearData($marinaCotizaciones);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        return new Response($serializer->serialize($marinaCotizaciones, $request->getRequestFormat()));
    }

    /**
     * Displays a form to edit an existing facturacion entity.
     *
     * @Route("/{id}/edit", name="contabilidad_facturacion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Facturacion $facturacion)
    {
        $deleteForm = $this->createDeleteForm($facturacion);
        $editForm = $this->createForm('AppBundle\Form\Contabilidad\FacturacionType', $facturacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contabilidad_facturacion_edit', array('id' => $facturacion->getId()));
        }

        return $this->render('contabilidad/facturacion/edit.html.twig', array(
            'facturacion' => $facturacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a facturacion entity.
     *
     * @Route("/{id}", name="contabilidad_facturacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Facturacion $facturacion)
    {
        $form = $this->createDeleteForm($facturacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($facturacion);
            $em->flush();
        }

        return $this->redirectToRoute('contabilidad_facturacion_index');
    }

    /**
     * En caso de que se requiera limpiar las cotizaciones
     *
     * @param $cotizaciones
     *
     * @return array
     */
    private function clearData($cotizaciones): array
    {
        $clearedCotizaciones = [];

        foreach ($cotizaciones as $i => $cotizacion) {
            $clearedCotizaciones[$i] = [
                'id' => $cotizacion['id'],
                'folio' => $cotizacion['foliorecotiza'] ? $cotizacion['folio'] . '-' . $cotizacion['foliorecotiza'] : $cotizacion['folio'],
                'tipocambio' => $cotizacion['dolar'],
                'descuento' => (int)$cotizacion['descuentototal'],
                'iva' => (int)$cotizacion['ivatotal'],
                'subtotal' => (int)$cotizacion['subtotal'],
                'total' => (int)$cotizacion['total'],
            ];

            // Aqui se deberia checar si es marina o astillero
            foreach ($cotizacion['pagos'] as $pago) {
                $clearedCotizaciones[$i]['pagos'][] = [
                    'id' => $pago['id'],
                    'cantidad' => (int)$pago['cantidad'],
                    'dolar' => (int)$pago['dolar'],
                ];
            }

            /*foreach ($cotizacion['mhcservicios'] as $servicio) {

                $cu = false; // Kilovoltio - amperio
                $cps = false; //	Suministro de electricidad monofásica

                if ($servicio['tipo'] === 1) {
                    $descripcion = 'Días Estadía';
                } else if ($servicio['tipo'] === 2) {
                    $descripcion = 'Conexión a electricidad';
                    $cu = 'KVA'; // Kilovoltio - amperio
                    $cps = '83101801'; //	Suministro de electricidad monofásica
                } else {
                    $descripcion = 'Abastecimiento de gasolina';
                }

                $clearedCotizaciones[$i]['conceptos'][] = [
                    'id' => $servicio['id'],
                    'cantidad' => (int)$servicio['cantidad'],
                    'unidad' => $servicio['unidad'] ?? 'NA',
                    'cps' => $cps ?: null,
                    'cu' => $cu ?: null,
                    'descripcion' => $descripcion,
                    'precio' => ($servicio['precio'] * $cotizacion['barco']['eslora']), // Especificamente Marina
                    'descuento' => (int)$servicio['descuento'],
                    'iva' => (int)$servicio['iva'],
                    'subtotal' => (int)$servicio['subtotal'],
                    'total' => (int)$servicio['total'],
                ];
            }*/
        }

        return $clearedCotizaciones;
    }

    /**
     * Creates a form to delete a facturacion entity.
     *
     * @param Facturacion $facturacion The facturacion entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Facturacion $facturacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contabilidad_facturacion_delete', ['id' => $facturacion->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
