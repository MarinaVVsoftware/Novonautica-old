<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/28/18
 * Time: 17:13
 */

namespace AppBundle\DataTables;


use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

class ReporteDataTable extends AbstractDataTableHandler
{
    const ID = 'reporte';
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
     * @return DataTableResults
     *
     * @throws DataTableException
     * @throws NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $repository = $this->doctrine->getRepository('AppBundle:Cliente\Reporte');
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.cliente)');

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('r')
            ->select('c.nombre', 'COALESCE(MAX(r.createdAt), \'No hay registro\') AS lastPago', 'SUM(r.adeudo) AS adeudo', 'SUM(r.abono) AS abono', 'c.id')
            ->addSelect('(SUM(r.adeudo) - SUM(r.abono)) AS total')
            ->leftJoin('r.cliente', 'c')
            ->addGroupBy('c.id');

        if ($request->search->value) {
            $query->where('(LOWER(c.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('c.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('lastPago', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('adeudo', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('abono', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('total', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->addSelect('COUNT(DISTINCT r.cliente)');
        $results->recordsFiltered = count($queryCount->getQuery()->getResult());

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        $reportes = $query->getQuery()->getResult();

        foreach ($reportes as $reporte) {
            $results->data[] = [
                $reporte['nombre'],
                $reporte['lastPago'],
                '$' . number_format(($reporte['adeudo'] / 100), 2) . ' USD',
                '$' . number_format(($reporte['abono'] / 100), 2) . ' USD',
                '$' . number_format(($reporte['total'] / 100), 2) . ' USD',
                $reporte['id'],
            ];
        }

        return $results;
    }
}