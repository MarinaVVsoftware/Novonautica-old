<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/29/18
 * Time: 12:17
 */

namespace AppBundle\EventListener;


use AppBundle\Entity\Cliente\Reporte;
use AppBundle\Entity\Tienda\Solicitud;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ReporteSolicitudListener implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [Events::postPersist];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Solicitud) {
            return;
        }

        $em = $args->getObjectManager();
        $folio = $entity->getFoliorecotiza() ? "{$entity->getFolio()}-{$entity->getFoliorecotiza()}" : $entity->getFolio();

        $reporte = new Reporte();
        $reporte->setAdeudo($entity->getTotalusd());
        $reporte->setCliente($entity->getNombrebarco()->getCliente());
        $reporte->setConcepto("Solicitud Tienda #{$folio}");
        $reporte->setReferencia($folio);

        $em->persist($reporte);
        $em->flush();
    }
}