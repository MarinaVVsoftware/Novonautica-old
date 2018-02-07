<?php

namespace AppBundle\Controller;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Correo controller.
 *
 * @Route("historial-correo")
 */
class CorreoController extends Controller
{
    /**
     * Lists all correo entities.
     *
     * @Route("/", name="historial-correo_index")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $datatables = $this->get('datatables');
                $results = $datatables->handle($request, 'correo');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }

        return $this->render('correo/index.html.twig', [
            'title' => 'Historial Correos'
        ]);
    }


    /**
     * Muestra el pdf enviado en base a una cotizacion
     *
     * @Route("/cotizacion-pdf", name="correo_pdf")
     *
     * @param Request $request
     *
     * @return PdfResponse
     */
    public function showCorreoPDFAction(Request $request)
    {
        $folio = $request->query->get('folio');
        $tipo = $request->query->get('tipo');

        $mhc = $this->getDoctrine()
                ->getRepository('AppBundle:MarinaHumedaCotizacion')
                ->getCotizacionByFolio($folio);

        if ($mhc) {
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
        else {
            $ac = $this->getDoctrine()
                ->getRepository('AppBundle:AstilleroCotizacion')
                ->getCotizacionByFolio($folio);

            if ($tipo == 1) { //dolares
                $html = $this->renderView('astillero/cotizacion/pdf/cotizacionpdf.html.twig', [
                    'title' => 'Cotizacion-0.pdf',
                    'astilleroCotizacion' => $ac
                ]);
            } else { //pesos
                $html = $this->renderView('astillero/cotizacion/pdf/cotizacion-pesospdf.html.twig', [
                    'title' => 'Cotizacion-0.pdf',
                    'astilleroCotizacion' => $ac
                ]);
            }

            $header = $this->renderView('astillero/cotizacion/pdf/pdfencabezado.twig', [
                'astilleroCotizacion' => $ac
            ]);
            $footer = $this->renderView('astillero/cotizacion/pdf/pdfpie.twig', [
                'astilleroCotizacion' => $ac
            ]);
            $hojapdf = $this->get('knp_snappy.pdf');
            $options = [
                'margin-top' => 30,
                'margin-right' => 0,
                'margin-bottom' => 10,
                'margin-left' => 0,
                'header-html' => utf8_decode($header),
                'footer-html' => utf8_decode($footer)
            ];
            return new PdfResponse(
                $hojapdf->getOutputFromHtml($html, $options),
                'Cotizacion-' . $ac->getFolio() . '-' . $ac->getFoliorecotiza() . '.pdf', 'application/pdf', 'inline'
            );
        }
    }
}
