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
        $registro = new Registro();

        $registro->setReferencia('PUNTO DE VENTA');
        $registro->setTipo(Registro::TIPO_SALIDA);
        $registro->setFecha($venta->getCreatedAt());
        $registro->setTotal($venta->getTotal());

        /** @var Concepto $concepto */
        foreach ($venta->getConceptos() as $concepto) {
            $entrada = new Registro\Entrada();

            $entrada->setProducto($concepto->getProducto());
            $entrada->setCantidad($concepto->getCantidad());
            $entrada->setImporte($concepto->getTotal());
            $entrada->setRegistro($registro);

            $registro->addEntrada($entrada);
        }

        $entityManager = $eventArgs->getEntityManager();
        $entityManager->persist($registro);
        $entityManager->flush();
    }
}
