<?php

namespace AppBundle\Repository\JRFMarine;

/**
 * ProductoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductoRepository extends \Doctrine\ORM\EntityRepository
{
    public function getProductoSelect2($query)
    {
        $builder = $this->createQueryBuilder('producto');

        return $builder
            ->select('producto.id, producto.nombre AS text, producto.existencia AS quantity')
            ->where('LOWER(producto.nombre) LIKE :query')
            ->setParameter('query', strtolower("%{$query}%"))
            ->setMaxResults(5)
            ->getQuery()
            ->getArrayResult();
    }
}