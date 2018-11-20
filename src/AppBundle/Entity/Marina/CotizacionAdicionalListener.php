<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 15/11/2018
 * Time: 11:56 AM
 */

namespace AppBundle\Entity\Marina;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\MarinaHumedaCotizacionAdicional;


class CotizacionAdicionalListener
{
    public function postPersist(MarinaHumedaCotizacionAdicional $cotizacionAdicional, LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $productosRepositorio = $em->getRepository('AppBundle:MarinaHumedaServicio');
        try{
            foreach ($cotizacionAdicional->getMhcservicios() as $servicio){
                $producto = $productosRepositorio->findOneBy(['id'=>$servicio->getMarinaHumedaServicio()->getId()]);
                $nuevaExistencia = $producto->getExistencia() - $servicio->getCantidad();
                $producto->setExistencia($nuevaExistencia);
                $em->persist($producto);
            }
            $em->flush();
        }catch (\Exception $e){
            $error = 'Error inesperado: '.$e;
        }

    }
}