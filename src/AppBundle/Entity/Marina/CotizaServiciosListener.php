<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 15/11/2018
 * Time: 01:48 PM
 */

namespace AppBundle\Entity\Marina;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\MarinaHumedaCotizaServicios;

class CotizaServiciosListener
{
    public function postRemove(MarinaHumedaCotizaServicios $cotizaServicios, LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $productosRepositorio = $em->getRepository('AppBundle:MarinaHumedaServicio');
        $producto = $productosRepositorio->findOneBy(['id'=>$cotizaServicios->getMarinahumedaservicio()->getId()]);
        $nuevaExistencia = $producto->getExistencia() + $cotizaServicios->getCantidad();
        $producto->setExistencia($nuevaExistencia);
        try{
            $em->persist($producto);
            $em->flush();
        }catch (\Exception $e){
            $error = 'Error inesperado: '.$e;
        }
    }
}