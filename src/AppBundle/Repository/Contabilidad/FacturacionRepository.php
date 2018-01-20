<?php

namespace AppBundle\Repository\Contabilidad;

use Doctrine\ORM\Query;

/**
 * FacturacionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FacturacionRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllCotizacionesxFacturar($folio)
    {
        $em = $this->getEntityManager();
        // Consume mas recursos ??
        $dql = '
        SELECT 
        cotizacion,
        pagos
        FROM AppBundle:MarinaHumedaCotizacion AS cotizacion
        LEFT JOIN cotizacion.pagos AS pagos
        WHERE cotizacion.validanovo = 2
        AND pagos.id IS NOT NULL
        AND pagos.factura IS NULL
        ';
        /*$dql = '
        SELECT 
        cotizacion,
        servicio,
        barco
        FROM AppBundle:MarinaHumedaCotizacion AS cotizacion
        LEFT JOIN cotizacion.mhcservicios AS servicio
        LEFT JOIN cotizacion.slipmovimiento AS movimiento
        LEFT JOIN cotizacion.barco AS barco
        WHERE cotizacion.validanovo = 2
        AND cotizacion.factura IS null
        ';*/
        // o esta consume mas recursos?
        /*$dql = '
        SELECT 
        partial cotizacion.{id, folio, foliorecotiza, iva, subtotal, total},
        partial servicio.{id, tipo, cantidad, precio, iva, descuento, subtotal, total}
        FROM AppBundle:MarinaHumedaCotizacion AS cotizacion
        LEFT JOIN cotizacion.mhcservicios AS servicio
        WHERE cotizacion.validanovo = 2
        ';*/

        if ($folio) {
            $dql .= 'AND cotizacion.folio LIKE :folio';
            $query = $em->createQuery($dql)
                ->setParameter(':folio', "%{$folio}%");
        } else {
            $query = $em->createQuery($dql);
        }

        return $query->getResult(Query::HYDRATE_ARRAY);
    }

    public function getPagosByFolioCotizacion($folio, $folioRecotizado = null)
    {
        $em = $this->getEntityManager();
        $dql = '
        SELECT 
        pagos, mhc, movimiento
        FROM AppBundle:Pago AS pagos
        LEFT JOIN pagos.mhcotizacion AS mhc
        LEFT JOIN mhc.slipmovimiento AS movimiento
        WHERE mhc.folio = :folio
        ';

        if ($folioRecotizado) {
            $dql .= 'AND mhc.foliorecotiza = :foliorecotizado';
            $query = $em->createQuery($dql)
                ->setParameter(':folio', $folio)
                ->setParameter(':foliorecotizado', $folioRecotizado);
        } else {
            $query = $em->createQuery($dql)
                ->setParameter(':folio', $folio);
        }

        return $query->getResult();
    }
}
