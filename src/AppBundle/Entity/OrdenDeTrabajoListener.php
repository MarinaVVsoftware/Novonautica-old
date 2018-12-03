<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 15/11/2018
 * Time: 05:14 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;

class OrdenDeTrabajoListener
{
    public function postPersist(OrdenDeTrabajo $ordenDeTrabajo, LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();

        foreach ($ordenDeTrabajo->getContratistas() as $contratista) {

            if ($contratista->getProducto()) {
                $productosRepositorio = $em->getRepository(Astillero\Producto::class);
                $producto = $productosRepositorio->find($contratista->getProducto());
                $nuevaExistencia = $producto->getExistencia() - $contratista->getCantidad();
                $producto->setExistencia($nuevaExistencia);

                $em->persist($producto);
            }

        }

        $em->flush();
    }
}
