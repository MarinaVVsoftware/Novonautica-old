<?php
/**
 * User: inrumi
 * Date: 10/2/18
 * Time: 13:20
 */

namespace AppBundle\DataTables\Reporte\Ingreso;


use AppBundle\Entity\MarinaHumedaCotizacion;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class MarinaDataTable extends AbstractDataTableHandler
{
    const ID = 'marina/reporte/ingreso';
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Handles specified DataTable request.
     *
     * @param DataTableQuery $request
     *
     * @throws DataTableException
     *
     * @return DataTableResults
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $repository = $this->doctrine->getRepository(MarinaHumedaCotizacion::class);
        $results = new DataTableResults();

        $inicio = \DateTime::createFromFormat('Y-m-d', $request->customData['dates']['month'])->setTime(0, 0,0);
        $fin = (clone $inicio)->modify('last day of this month');

        $query = $repository
            ->createQueryBuilder('mc')
            ->select('COUNT(mc.id)')
            ->where(
                'mc.validacliente = 2 AND '.
                '(:inicio BETWEEN mc.fechaLlegada AND mc.fechaSalida OR '.
                ':fin BETWEEN mc.fechaLlegada AND mc.fechaSalida)')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin);

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $cantidadQuery =
            '(CASE '.
                'WHEN :inicio < mc.fechaLlegada '.
                'THEN (CASE '.
                    'WHEN DATE_DIFF(:fin, :inicio) <> 30 '.
                        'THEN DATE_DIFF(:fin, mc.fechaLlegada) '.
                        'ELSE (DATE_DIFF(:fin, mc.fechaLlegada) - 1) ' .
                    'END) '.
                'WHEN :fin > mc.fechaSalida '.
                'THEN (CASE '.
                    'WHEN DATE_DIFF(:fin, :inicio) <> 30 '.
                        'THEN DATE_DIFF(:fin, mc.fechaSalida) '.
                        'ELSE (DATE_DIFF(:fin, mc.fechaSalida) - 1) '.
                    'END) '.
                'ELSE 30 ' .
            'END) AS dias_cantidad';

        $query = $repository
            ->createQueryBuilder('mc')
            ->innerJoin('mc.mhcservicios', 's')
            ->leftJoin('mc.barco', 'b')
            ->leftJoin('mc.pagos', 'p')
            ->select(
                '(CASE WHEN mc.foliorecotiza <= 0 THEN mc.folio ELSE CONCAT(mc.folio, \'-\', mc.foliorecotiza) END) AS folio',
                'IDENTITY(mc.slip) AS slip ',
                'b.nombre AS embarcacion ',
                'MAX(CASE WHEN s.tipo = 1 THEN s.total ELSE 0 END) AS amarre_usd',
                'MAX(CASE WHEN s.tipo = 2 THEN s.total ELSE 0 END) AS servicio_usd',
                '(p.cantidad / 1.16)                                  AS pago_subtotal',
                '((p.cantidad / 1.16) * 0.16)                         AS pago_iva',
                'p.cantidad                                           AS pago',
                $cantidadQuery,
                'mc.id'
            )
            ->groupBy('mc.id')
            ->where(
                'mc.validacliente = 2 AND '.
                '(:inicio BETWEEN mc.fechaLlegada AND mc.fechaSalida OR '.
                ':fin BETWEEN mc.fechaLlegada AND mc.fechaSalida)')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin);

        if ($request->search->value) {
            $query->where(
                '(LOWER(mc.folio) LIKE :search OR '.
                'LOWER(b.nombre) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('mc.slip', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('mc.barco', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('s.cantidad', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('s.cantidad', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('mc.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $results->recordsFiltered = COUNT($queryCount->select('COUNT(mc.id)')->getQuery()->getResult());

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var MarinaHumedaCotizacion[] $cotizacions */
        $cotizacions = $query->getQuery()->getResult();

        foreach ($cotizacions as $cotizacion) {
            $results->data[] = [
                $cotizacion['folio'],
                $cotizacion['slip'] ?? 'NA',
                $cotizacion['embarcacion'],
                '$ '.number_format(($cotizacion['amarre_usd'] / 100), 2),
                '$ '.number_format(($cotizacion['servicio_usd'] / 100), 2),
                '$ '.number_format(($cotizacion['pago_subtotal'] / 100), 2),
                '$ '.number_format(($cotizacion['pago_iva'] / 100), 2),
                '$ '.number_format(($cotizacion['pago'] / 100), 2),
                $cotizacion['dias_cantidad'],
            ];
        }

        return $results;
    }
}
