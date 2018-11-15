<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/15/18
 * Time: 12:24 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Event\LifecycleEventArgs;

class CombustibleListener
{
    public function postPersist(Combustible $combustible, LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();

        $producto = $combustible->getTipo();
        $cantidadInicial = $producto->getExistencia();
        $cantidadRemover = $combustible->getCantidad();

        $producto->setExistencia($cantidadInicial - $cantidadRemover);

        $em->persist($producto);
        $em->flush();
    }

}
