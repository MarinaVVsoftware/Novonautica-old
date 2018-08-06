<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/11/18
 * Time: 15:12
 */

namespace AppBundle\DataTables;


use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class AstilleroReporteDataTable extends AbstractDataTableHandler
{
    const ID = 'astilleroReporte';
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
        $repository = $this->doctrine->getRepository('AppBundle:AstilleroCotizacion');
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('ac')
            ->select('COUNT(ac.cliente)')
            ->where('ac.validacliente = 2 AND ac.estatuspago IS NULL OR ac.estatuspago <> 2');

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $pagoRepository = $this->doctrine->getRepository('AppBundle:Pago');

        $adeudoSubquery = $pagoRepository->createQueryBuilder('pa')
            ->select('SUM(pa.cantidad)')
            ->where('pa.acotizacion = ac.id')
            ->groupBy('pa.acotizacion');

        $fechaSubquery = $pagoRepository->createQueryBuilder('pf')
            ->select('MAX(pf.fecharealpago)')
            ->where('pf.acotizacion = ac.id');

        $query = $repository->createQueryBuilder('ac')
            ->select('CASE WHEN  ac.foliorecotiza = 0 THEN ac.folio ' .
                'ELSE CONCAT(ac.folio, \'-\', ac.foliorecotiza) END AS folio')
            ->addSelect('c.id AS id_cliente', 'ac.id AS id_cotizacion')
            ->addSelect('c.nombre', 'ac.fechaLlegada', 'ac.fechaSalida', 'ac.total', 'b.nombre AS barco')
            ->addSelect("({$adeudoSubquery->getDQL()}) AS pagado")
            ->addSelect('COALESCE(p.fecharealpago, \'No se han realizado pagos\') AS lastPago')
            ->leftJoin('ac.cliente', 'c')
            ->leftJoin('ac.pagos', 'p')
            ->leftJoin('ac.barco', 'b')
            ->where("p.fecharealpago = ({$fechaSubquery->getDQL()}) " .
                "AND ac.estatuspago = 1 OR ac.estatuspago IS NULL AND ac.validacliente = 2");

        if ($request->search->value) {
            $query->where('(LOWER(c.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        if ($request->customData) {
            $query->andWhere('ac.fechaLlegada BETWEEN :start AND :end');
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
            $query->addOrderBy('ac.folio', $request->order[0]->dir);
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(ac.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        $reportes = $query->getQuery()->getResult();

        foreach ($reportes as $reporte) {
            $results->data[] = [
                $reporte['folio'],
                $reporte['nombre'],
                $reporte['barco'],
                $reporte['fechaLlegada']->format('Y-m-d H:i:s'),
                $reporte['fechaSalida']->format('Y-m-d H:i:s'),
                $reporte['lastPago'],
                '$' . number_format(($reporte['total'] / 100), 2),
                '$' . number_format(($reporte['pagado'] / 100), 2),
                [
                    'cliente' => $reporte['id_cliente'],
                    'cotizacion' => $reporte['id_cotizacion']
                ],
            ];
        }

        return $results;
    }
}
