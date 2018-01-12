<?php

namespace AppBundle\Repository;

/**
 * ClienteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClienteRepository extends \Doctrine\ORM\EntityRepository
{
    public function findEmpresas()
    {
        $qb = $this->createQueryBuilder('cl');
        return $qb
            ->select('cl.empresa as nombre')
            ->getQuery()
            ->getResult();
    }

    public function findLike($key, $value)
    {
        $key = 'cl.' . $key;
        $qb = $this->createQueryBuilder('cl');

        return $qb
            ->where('LOWER('. $key .') LIKE :value')
            ->setParameter(':value', strtolower("%{$value}%"))
            ->getQuery()
            ->getResult();
    }
}
