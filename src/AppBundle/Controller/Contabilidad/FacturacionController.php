<?php

namespace AppBundle\Controller\Contabilidad;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Extra\NumberToLetter;
use AppBundle\Form\Contabilidad\FacturacionType;
use Doctrine\ORM\NonUniqueResultException;
use Hyperion\MultifacturasBundle\src\Multifacturas;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function indexAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $datatables = $this->get('datatables');
                $results = $datatables->handle($request, 'facturas');

                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getCode());
            }
        }

        return $this->render('contabilidad/facturacion/index.html.twig', ['title' => 'Listado de facturas']);
    }

    /**
     * Creates a new facturacion entity.
     *
     * @Route("/new", name="contabilidad_facturacion_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $factura = new Facturacion();
        $this->denyAccessUnlessGranted('CONTABILIDAD_CREATE', $factura);

        $em = $this->getDoctrine()->getManager();

        $factura->setFolio($em->getRepository(Facturacion::class)->generateFolio());

        $form = $this->createForm(FacturacionType::class, $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // TODO Agregar campo de usoCFDI a los RFCS de los clientes

            /*
            $attachment = new Swift_Attachment(
                $this->getFacturaPDF($factura),
                'factura_'.$factura->getFolioCotizacion().'.pdf',
                'application/pdf'
            );

            $em->persist($factura);
            $em->flush();

            $message = (new \Swift_Message('Factura de su pago realizado en '.$factura->getFecha()->format('d/m/Y')))
                ->setFrom('noresponder@novonautica.com')
                ->setTo(explode(',', $factura->getEmail()))
                ->setBcc(explode(',', $factura->getEmisor()->getEmails()))
                ->setBody(
                    $this->renderView('contabilidad/facturacion/email/factura-template.html.twig', [
                        'cuerpo' => $factura->getCuerpoCorreo(),
                    ]),
                    'text/html'
                )
                ->attach($attachment);

            $mailer->send($message);

            return $this->redirectToRoute('contabilidad_facturacion_index');
            */
        }

        return $this->render('contabilidad/facturacion/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/reenviar", name="contabilidad_facturacion_reenvio")
     * @Method("GET")
     *
     * @param Facturacion $factura
     * @param \Swift_Mailer $mailer
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reenviarAction(Facturacion $factura, \Swift_Mailer $mailer)
    {
        $attachment = new Swift_Attachment(
            $this->getFacturaPDF($factura),
            'factura_'.$factura->getFolioCotizacion().'.pdf',
            'application/pdf'
        );

        $message = (new \Swift_Message('Factura de su pago realizado en '.$factura->getFecha()->format('d/m/Y')))
            ->setFrom('noresponder@novonautica.com')
            ->setTo(explode(',', $factura->getEmail()))
            ->setBcc(explode(',', $factura->getEmisor()->getEmails()))
            ->setBody(
                $this->renderView('contabilidad/facturacion/email/factura-template.html.twig'),
                'text/html'
            )
            ->attach($attachment);

        $mailer->send($message);

        return $this->redirectToRoute('contabilidad_facturacion_index');
    }

    /**
     * @Route("/{id}/pdf", name="contabilidad_factura_pdf")
     * @Method("GET")
     *
     * @param Facturacion $factura
     *
     * @return PdfResponse
     */
    public function getFacturaPDF(Facturacion $factura)
    {
        $folio = $factura->getFolioCotizacion() ?? $factura->getFolioFiscal();
        $numToLetters = new NumberToLetter();
        $html = $this->renderView(':contabilidad/facturacion/pdf:factura.html.twig', [
            'title' => 'factura_'.$folio.'.pdf',
            'factura' => $factura,
            'regimenFiscal' => $this->regimenFiscal[$factura->getEmisor()->getRegimenFiscal()],
            'tipoComprobante' => $this->tipoComprobante[$factura->getTipoComprobante()],
            'numLetras' => $numToLetters->toWord(($factura->getTotal() / 100), $factura->getMoneda()),
            'usoCFDI' => $this->cfdi[$factura->getUsoCFDI()],
            'formaPago' => $this->formaPago[$factura->getFormaPago()],
            'metodoPago' => $this->metodoPago[$factura->getMetodoPago()],
            'moneda' => $this->moneda[$factura->getMoneda()],
        ]);

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            'factura_'.$folio.'.pdf', 'application/pdf', 'inline'
        );
    }

    /**
     * @Route("/{id}/cancelar", name="contabilidad_facturacion_cancel")
     * @Method({"GET"})
     *
     * @param Facturacion $factura
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelAction(Facturacion $factura)
    {
        $this->denyAccessUnlessGranted('CONTABILIDAD_CANCEL', $factura);

        $facturador = $this->container->get('multifacturas');
        $timbrado = $facturador->cancela($factura);

        if ($timbrado['codigo_mf_numero']) {
            $this->addFlash('danger', $timbrado['codigo_mf_texto']);
        } else {
            $this->addFlash('danger', $timbrado['codigo_mf_texto']);
            $factura->setEstatus(0);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('contabilidad_facturacion_index');
    }

    /**
     * @Route("/clientes")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getClientesLikeAction(Request $request)
    {
        $q = $request->query->get('q');
        $clientes = $this->getDoctrine()->getRepository(Cliente::class)->getAllWhereNombreLike($q);

        return new JsonResponse(
            [
                'results' => $clientes,
            ],
            JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/clientes/rfc")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getClienteRFCsAction(Request $request)
    {
        $q = $request->query->get('q');
        $rfcs = $this->getDoctrine()->getRepository(Cliente\RazonSocial::class)->getRFCsFromClient($q);

        return new JsonResponse(
            [
                'results' => $rfcs,
            ],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/claves-unidad")
     *
     * @param Request $request
     *
     * @return string
     */
    public function getAllClavesUnidad(Request $request)
    {
        $q = $request->query->get('q');
        $clavesUnidad = $this->getDoctrine()
            ->getRepository(Facturacion\Concepto\ClaveUnidad::class)
            ->findAllLikeSelect2($q);

        return new JsonResponse(
            [
                'results' => $clavesUnidad
            ]
        );
    }

    /**
     * @Route("/clavesprodserv")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getAllClaveProdServ(Request $request)
    {
        $query = $request->query->get('q');
        $cps = $this->getDoctrine()
            ->getRepository(Facturacion\Concepto\ClaveProdServ::class)
            ->findAllLikeSelect2($query);

        return new JsonResponse(
            [
                'results' => $cps
            ]
        );
    }

    /**
     * @Route("/{id}")
     * @param int $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $facturacionRepository = $this->getDoctrine()->getRepository(Facturacion::class);

        try {
            $factura = $facturacionRepository->getFactura($id);
        } catch (NonUniqueResultException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->render(
            'contabilidad/facturacion/show.html.twig',
            [
                'factura' => $factura,
            ]
        );
    }
}
