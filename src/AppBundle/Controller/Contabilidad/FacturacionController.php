<?php

namespace AppBundle\Controller\Contabilidad;

use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Extra\NumberToLetter;
use AppBundle\Serializer\CotizacionNameConverter;
use AppBundle\Serializer\NotNullObjectNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Facturacion controller.
 *
 * @Route("contabilidad/facturacion")
 */
class FacturacionController extends Controller
{
    private $formaPago = [
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

    private $metodoPago = [
        'PUE' => 'Pago en una sola exhibición',
        'PIP' => 'Pago inicial y parcialidades',
        'PPD' => 'Pago en parcialidades o diferido',
    ];

    private $regimenFiscal = [
        '601' => 'General de Ley Personas Morales',
        '603' => 'Personas Morales con Fines no Lucrativos',
        '605' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios',
        '606' => 'Arrendamiento',
        '608' => 'Demás ingresos',
        '609' => 'Consolidación',
        '610' => 'Residentes en el Extranjero sin Establecimiento Permanente en México',
        '611' => 'Ingresos por Dividendos (socios y accionistas)',
        '612' => 'Personas Físicas con Actividades Empresariales y Profesionales',
        '614' => 'Ingresos por intereses',
        '616' => 'Sin obligaciones fiscales',
        '620' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos',
        '621' => 'Incorporación Fiscal',
        '622' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
        '623' => 'Opcional para Grupos de Sociedades',
        '624' => 'Coordinados',
        '628' => 'Hidrocarburos',
        '607' => 'Régimen de Enajenación o Adquisición de Bienes',
        '629' => 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales',
        '630' => 'Enajenación de acciones en bolsa de valores',
        '615' => 'Régimen de los ingresos por obtención de premios',
    ];

    private $tipoComprobante = [
        'I' => 'Ingreso',
        'E' => 'Egreso',
        'T' => 'Traslado',
        'N' => 'Nómina',
        'P' => 'Pago',
    ];

    private $cfdi = [
        'G01' => 'Adquisición de mercancias',
        'G02' => 'Devoluciones, descuentos o bonificaciones',
        'G03' => 'Gastos en general',
        'I01' => 'Construcciones',
        'I02' => 'Mobilario y equipo de oficina por inversiones',
        'I03' => 'Equipo de transporte',
        'I04' => 'Equipo de computo y accesorios',
        'I05' => 'Dados, troqueles, moldes, matrices y herramental',
        'I06' => 'Comunicaciones telefónicas',
        'I07' => 'Comunicaciones satelitales',
        'I08' => 'Otra maquinaria y equipo',
        'D01' => 'Honorarios médicos, dentales y gastos hospitalarios.',
        'D02' => 'Gastos médicos por incapacidad o discapacidad',
        'D03' => 'Gastos funerales.',
        'D04' => 'Donativos.',
        'D05' => 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación).',
        'D06' => 'Aportaciones voluntarias al SAR.',
        'D07' => 'Primas por seguros de gastos médicos.',
        'D08' => 'Gastos de transportación escolar obligatoria.',
        'D09' => 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.',
        'D10' => 'Pagos por servicios educativos (colegiaturas)',
        'P01' => 'Por definir',
    ];

    private $moneda = [
        'USD' => 'Dolar Americano',
        'MXN' => 'Peso Mexicano'
    ];

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
        $valorSistema->setFolioMarina($factura->getFolioCotizacion());

        $form = $this->createForm('AppBundle\Form\Contabilidad\FacturacionType', $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facturador = $this->container->get('multifacturas');
            $timbrado = $facturador->procesa($factura);

            // Verificar que la factura se haya timbrado correctamente
            if ($timbrado['codigo_mf_numero']) {
                $this->addFlash('danger', $timbrado['codigo_mf_texto']);
                return $this->redirectToRoute('contabilidad_facturacion_new');
            }

            // Si se eligio un pago de una cotizacion, entonces relacionarlo con la factura
            if ($factura->getPagos()) {
                $factura->getPagos()->setFactura($factura);
            }
            $factura->setXml(trim($timbrado['cfdi']));
            $factura->setPng(trim($timbrado['png']));
            $factura->setXmlArchivo($timbrado['archivo_xml']);
            $factura->setPngArchivo($timbrado['archivo_png']);
            $factura->setFolioFiscal($timbrado['uuid']);
            $factura->setCadenaOriginal($timbrado['representacion_impresa_cadena']);
            $factura->setSerieCertificadoCSD($timbrado['representacion_impresa_certificado_no']);
            $factura->setFechaTimbrado((string)$timbrado['representacion_impresa_fecha_timbrado']);
            $factura->setSelloCFDI((string)$timbrado['representacion_impresa_sello']);
            $factura->setSelloSAT((string)$timbrado['representacion_impresa_selloSAT']);
            $factura->setCertificadoSAT((string)$timbrado['representacion_impresa_certificadoSAT']);

            $attachment = new Swift_Attachment(
                $this->createFacturaPDF($factura),
                'factura_' . $factura->getFolioCotizacion() . '.pdf',
                'application/pdf'
            );

            $em->persist($factura);
            $em->flush();

            $message = (new \Swift_Message('Factura de su pago realizado en ' . $factura->getFecha()->format('d/m/Y')))
                ->setFrom('noresponder@novonautica.com')
                ->setTo(explode(',', $factura->getEmail()))
                ->setBcc(explode(',', $factura->getEmisor()->getEmails()))
                ->setBody(
                    $this->renderView('contabilidad/facturacion/email/factura-template.html.twig'),
                    'text/html'
                )
                ->attach($attachment);

//            $mailer->send($message);
            return $this->redirectToRoute('contabilidad_facturacion_index');
        }

        return $this->render('contabilidad/facturacion/new.html.twig', [
            'facturacion' => $factura,
            'form' => $form->createView(),
        ]);
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
     * @Route("/{id}/factura", name="contabilidad_factura_pdf")
     * @Method("GET")
     */
    public function showFacturaPDFAction(Request $request, Facturacion $factura)
    {
        return $this->createFacturaPDF($factura);
    }

    /**
     * @Route("/cotizaciones.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return string
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function getAllCotizacionesAction(Request $request)
    {
        $em = $this->getDoctrine();
        $folio = $request->query->get('folio');
        $cotizaciones = $em->getRepository('AppBundle:Contabilidad\Facturacion')->getCotizaciones($folio);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $nameConverter = new CotizacionNameConverter();
        $normalizer = new NotNullObjectNormalizer($classMetadataFactory, $nameConverter);

        $returnNombres = function ($servicio) {
            if (null !== $servicio) {
                return $servicio->getNombre();
            }
        };

        $normalizer->setCallbacks([
            'tipo' => function ($tipo) {
                if ($tipo === 1) {
                    return 'Días de estancia';
                } else if ($tipo === 2) {
                    return 'Conexión a electricidad';
                } else {
                    return 'Abastecimiento de combustible';
                }
            },
            'astilleroserviciobasico' => $returnNombres,
            'servicio' => $returnNombres,
            'producto' => $returnNombres,
        ]);

        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $response = $serializer->serialize($cotizaciones, $request->getRequestFormat(), ['groups' => ['facturacion']]);
        return new Response($response);
    }

    /**
     * @Route("/clave_unidad.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return string
     */
    public function getAllClavesUnidad(Request $request)
    {
        $query = $request->query->get('q');
        $repo = $this->getDoctrine()
            ->getRepository('AppBundle:Contabilidad\Facturacion\Concepto\ClaveUnidad');
        $cus = $repo->findAllLike($query);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        return new Response($serializer->serialize($cus, $request->getRequestFormat()));
    }

    /**
     * @Route("/claveprodserv.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     * @return Response
     */
    public function getAllClaveProdServ(Request $request)
    {
        $query = $request->query->get('q');
        $repo = $this->getDoctrine()
            ->getRepository('AppBundle:Contabilidad\Facturacion\Concepto\ClaveProdServ');
        $cps = $repo->findAllLike($query);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        return new Response($serializer->serialize($cps, $request->getRequestFormat()));
    }

    private function createFacturaPDF(Facturacion $factura)
    {
        $folio = $factura->getFolioCotizacion() ?? $factura->getFolioFiscal();
        $numToLetters = new NumberToLetter();
        $html = $this->renderView(':contabilidad/facturacion/pdf:factura.html.twig', [
            'title' => 'factura_' . $folio . '.pdf',
            'factura' => $factura,
            'regimenFiscal' => $this->regimenFiscal[$factura->getEmisor()->getRegimenFiscal()],
            'tipoComprobante' => $this->tipoComprobante[$factura->getTipoComprobante()],
            'numLetras' => $numToLetters->toWord(($factura->getTotal() / 100), $factura->getMoneda()),
            'usoCFDI' => $this->cfdi[$factura->getUsoCFDI()],
            'formaPago' => $this->formaPago[$factura->getFormaPago()],
            'metodoPago' => $this->metodoPago[$factura->getMetodoPago()],
            'moneda' => $this->moneda[$factura->getMoneda()]
        ]);

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            'factura_' . $folio . '.pdf', 'application/pdf', 'inline'
        );
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
