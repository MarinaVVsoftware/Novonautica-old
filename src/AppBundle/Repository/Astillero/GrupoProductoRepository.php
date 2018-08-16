<?php
/**
 * User: inrumi
 * Date: 8/16/18
 * Time: 11:57
 */

namespace AppBundle\Repository\Astillero;


use Doctrine\ORM\EntityRepository;

class GrupoProductoRepository extends EntityRepository
{
    public function getGrupoFromServicio($servicioId)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT grupo, producto '.
                'FROM AppBundle:Astillero\GrupoProducto grupo '.
                'LEFT JOIN grupo.producto producto '.
                'LEFT JOIN grupo.servicio servicio '.
                'WHERE servicio.id = :id'
            )
            ->setParameter('id', $servicioId);

        return $query->getArrayResult();
    }
}
