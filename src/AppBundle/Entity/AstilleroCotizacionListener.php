<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 15/11/2018
 * Time: 05:14 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\AstilleroCotizacion;

class AstilleroCotizacionListener
{
    public function postUpdate(AstilleroCotizacion $astilleroCotizacion, LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        if($astilleroCotizacion->getValidacliente()){
            try{
                foreach ($astilleroCotizacion->getAcservicios() as $servicio){
                    if($servicio->getProducto()){
                        $productosRepositorio = $em->getRepository('AppBundle:Astillero\Producto');
                        $producto = $productosRepositorio->findOneBy(['id'=>$servicio->getProducto()->getId()]);
                        $nuevaExistencia = $producto->getExistencia() - $servicio->getCantidad();
                        $producto->setExistencia($nuevaExistencia);
                        $em->persist($producto);
                    }
                }
                $em->flush();
            }catch (\Exception $e){
                $error = 'Error inesperado: '.$e;
            }
        }
    }
}