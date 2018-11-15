<?php
/**
 * User: inrumi
 * Date: 7/3/18
 * Time: 12:15
 */

namespace AppBundle\Entity\Tienda;


use AppBundle\Entity\Tienda\Inventario\Registro;
use AppBundle\Entity\Tienda\Venta\Concepto;
use Doctrine\ORM\Event\LifecycleEventArgs;

class VentaListener
{
    /**
     * @param Venta $venta
     * @param LifecycleEventArgs $eventArgs
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postPersist(Venta $venta, LifecycleEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();

        /** @var Concepto $concepto */
        foreach ($venta->getConceptos() as $concepto) {
            $producto = $concepto->getProducto();
            $existenciaInicial = $producto->getExistencia();
            $existenciaRemover = $concepto->getCantidad();

            $producto->setExistencia($existenciaInicial - $existenciaRemover);
            $entityManager->persist($producto);
        }

        $entityManager->flush();
    }
}
