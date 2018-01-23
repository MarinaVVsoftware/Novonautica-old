<?php

namespace AppBundle\Repository\Contabilidad\Facturacion\Concepto;

/**
 * ClaveUnidadRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClaveUnidadRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllLike($query)
    {
        $qb = $this->createQueryBuilder('cu');

        return $qb
            ->where($qb->expr()->like('cu.nombre', ':query'))
            ->orWhere($qb->expr()->like('cu.claveUnidad', ':query'))
            ->setParameter('query', "%{$query}%")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}