<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use AppBundle\Entity\Pago;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Pago controller.
 *
 * @Route("pago")
 */
class PagoController extends Controller
{
    /**
     * @Route("/{id}/{kind}/pago-recibo")
     */
    public function paymentReceiptAction($id, $kind)
    {
        $em = $this->getDoctrine();
        $pago = $em->getRepository(Pago::class)->find($id);
        $emisorRepository = $em->getRepository(Emisor::class);

        switch ($kind) {
            case 'astillero':
                $cantidad = ($pago->getCantidad() / 100);
                $emisor = $emisorRepository->findOneBy(['alias' => 'V&V Astillero tt']);
                break;
            default:
                $cantidad = ($pago->getCantidad() / 100) * ($pago->getDolar() / 100);
                $emisor = $emisorRepository->findOneBy(['alias' => 'Servicios Marinos']);
                break;
        }

        $pagoHtml =  $this->renderView(
            'pago/recibo.html.twig',
            [
                'pago' => $pago,
                'cotizacion' => $pago->getUniqueCotizacion(),
                'emisor' => $emisor,
                'cantidad' => $cantidad,
            ]
        );

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($pagoHtml),
            'recibo-de-pago.pdf',
            'application/pdf',
            'inline'
        );
    }
}
