<?php

namespace AppBundle\Repository;

/**
 * MarinaHumedaCotizaServiciosRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MarinaHumedaCotizaServiciosRepository extends \Doctrine\ORM\EntityRepository
{
    public function getOneWithCatalogo($id)
    {
        $manager = $this->getEntityManager();

        $conceptos = $manager->createQuery(
            'SELECT '.
            'concepto.id AS productoId, '.
            'concepto.cantidad AS conceptoCantidad, ' .
            'concepto.total AS conceptoImporte, '.
            'cotizacion.dolar AS conceptoDolar, '.
            '(CASE '.
            'WHEN concepto.tipo = 1 THEN \'Estadia\' '.
            'WHEN concepto.tipo = 2 THEN \'Electricidad\' '.
            'ELSE \'Sin descripción\' '.
            'END) AS conceptoDescripcion '.
            'FROM AppBundle:MarinaHumedaCotizaServicios concepto '.
            'LEFT JOIN concepto.marinahumedacotizacion cotizacion '.
            'WHERE IDENTITY(concepto.marinahumedacotizacion) = :id ')
            ->setParameter('id', $id)
            ->getArrayResult();

        return $conceptos;
    }
}
