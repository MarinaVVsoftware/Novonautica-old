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
            ->select('COUNT(DISTINCT mc.cliente)');

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('mc')
            ->select('c.nombre', 'SUM(mc.total) AS adeudo', 'SUM(mc.pagado) AS abono')
            ->addSelect('(SUM(mc.total) - COALESCE(SUM(mc.pagado), 0)) AS total')
            ->addSelect('COALESCE(MAX(p.fecharealpago), \'No hay registro\') AS lastPago')
            ->leftJoin('mc.pagos', 'p')
            ->leftJoin('mc.cliente', 'c')
            ->andWhere('mc.validacliente = 2')
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
                $query->addOrderBy('abono', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('total', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('total', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->addSelect('COUNT(DISTINCT mc.cliente)');
        $results->recordsFiltered = count($queryCount->getQuery()->getResult());

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        $reportes = $query->getQuery()->getResult();

        dump($reportes);

        foreach ($reportes as $reporte) {
            $results->data[] = [
                $reporte['nombre'],
                $reporte['lastPago'],
                '$' . number_format(($reporte['adeudo'] / 100), 2) . ' USD',
                '$' . number_format(($reporte['abono'] / 100), 2) . ' USD',
                '$' . number_format(($reporte['total'] / 100), 2) . ' USD',
            ];
        }

        return $results;
    }
}