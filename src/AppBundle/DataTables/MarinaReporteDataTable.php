<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/11/18
 * Time: 13:41
 */

namespace AppBundle\DataTables;


use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class MarinaReporteDataTable extends AbstractDataTableHandler
{
    const ID = 'marinaReporte';
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $repository = $this->doctrine->getRepository('AppBundle:MarinaHumedaCotizacion');
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('mc')
            ->select('COUNT(mc.cliente)')
            ->where('mc.validacliente = 2 AND mc.estatuspago IS NULL OR mc.estatuspago <> 2');

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $pagoRepository = $this->doctrine->getRepository('AppBundle:Pago');

        $adeudoSubquery = $pagoRepository->createQueryBuilder('pa')
            ->select('SUM(pa.cantidad)')
            ->where('pa.acotizacion = mc.id')
            ->groupBy('pa.acotizacion');

        $fechaSubquery = $pagoRepository->createQueryBuilder('pf')
            ->select('MAX(pf.fecharealpago)')
            ->where('pf.acotizacion = mc.id');

        $query = $repository->createQueryBuilder('mc')
            ->select(
                'CASE WHEN  mc.foliorecotiza = 0 THEN mc.folio '.
                'ELSE CONCAT(mc.folio, \'-\', mc.foliorecotiza) END AS folio',
                'c.id AS id_cliente', 'mc.id AS id_cotizacion',
                'c.nombre', 'mc.fechaLlegada', 'mc.fechaSalida', 'mc.total', 'b.nombre AS barco',
                "({$adeudoSubquery->getDQL()}) AS pagado",
                'COALESCE(p.fecharealpago, \'No se han realizado pagos\') AS lastPago'
            )
            ->leftJoin('mc.cliente', 'c')
            ->leftJoin('mc.pagos', 'p')
            ->leftJoin('mc.barco', 'b')
            ->where("p.fecharealpago = ({$fechaSubquery->getDQL()})".
                ' AND mc.estatuspago = 1 OR mc.estatuspago IS NULL AND mc.validacliente = 2'
            );

        if ($request->search->value) {
            $query->where('(LOWER(c.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        if ($request->customData) {
            $query->andWhere('mc.fechaLlegada BETWEEN :start AND :end');
            $query->setParameter('start', $request->customData['dates']['start']);
            $query->setParameter('end', $request->customData['dates']['end']);
        }

        if ($request->columns[1]->search->value) {
            $query->andWhere('(LOWER(c.id) = :id)');
            $query->setParameter('id', $request->columns[1]->search->value);
        }

        if ($request->columns[2]->search->value) {
            $query->andWhere('(LOWER(b.id) = :id)');
            $query->setParameter('id', $request->columns[2]->search->value);
        }

        if ($request->order[0]->column == 0) {
            $query->addOrderBy('mc.folio', $request->order[0]->dir);
        }


        $queryCount = clone $query;
        $queryCount->select('COUNT(mc.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        if ($request->length >= 0) {
            $query->setMaxResults($request->length);
            $query->setFirstResult($request->start);
        }

        $reportes = $query->getQuery()->getResult();

        foreach ($reportes as $reporte) {
            $results->data[] = [
                $reporte['folio'],
                $reporte['nombre'],
                $reporte['barco'],
                $reporte['fechaLlegada']->format('Y-m-d H:i:s'),
                $reporte['fechaSalida']->format('Y-m-d H:i:s'),
                $reporte['lastPago'],
                '$'.number_format(($reporte['total'] / 100), 2),
                '$'.number_format(($reporte['pagado'] / 100), 2),
                [
                    'cliente' => $reporte['id_cliente'],
                    'cotizacion' => $reporte['id_cotizacion'],
                ],
            ];
        }

        return $results;
    }
}
