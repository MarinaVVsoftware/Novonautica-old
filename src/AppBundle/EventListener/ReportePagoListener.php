<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/28/18
 * Time: 15:58
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Cliente\Reporte;
use AppBundle\Entity\Pago;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;


class ReportePagoListener implements EventSubscriber
{

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::preRemove,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $pago = $args->getObject();

        if (!$pago instanceof Pago) { return; }

        $em = $args->getObjectManager();

        if ($pago->getMhcotizacion()) {
            $cotizacion = $pago->getMhcotizacion();
        } else if ($pago->getAcotizacion()) {
            $cotizacion = $pago->getAcotizacion();
        } else {
            $cotizacion = $pago->getTiendasolicitud();
        }

        if (null === $cotizacion) { return; }

        $folio = $cotizacion->getFoliorecotiza() ? "{$cotizacion->getFolio()}-{$cotizacion->getFoliorecotiza()}" : $cotizacion->getFolio();

        $reporte = new Reporte();
        $reporte->setAbono($pago->getCantidad());
        $reporte->setCliente($cotizacion->getCliente());
        $reporte->setConcepto("Abono de Cotización #{$folio}");
        $reporte->setReferencia($folio);

        $em->persist($reporte);
        $em->flush();
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $pago = $args->getObject();

        if (!$pago instanceof Pago) { return; }

        $em = $args->getObjectManager();
        $cotizacion = $pago->getMhcotizacion() ?: $pago->getAcotizacion();

        if (!$cotizacion) { return; }

        $folio = $cotizacion->getFoliorecotiza() ? "{$cotizacion->getFolio()}-{$cotizacion->getFoliorecotiza()}" : $cotizacion->getFolio();

        $reporte = new Reporte();
        $reporte->setAdeudo($pago->getCantidad());
        $reporte->setCliente($cotizacion->getCliente());
        $reporte->setConcepto("Devolución de abono #{$folio}");
        $reporte->setReferencia($folio);

        $em->persist($reporte);
        $em->flush();
    }
}