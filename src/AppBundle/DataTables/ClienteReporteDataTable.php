<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/29/18
 * Time: 15:52
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Cliente\Reporte;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ClienteReporteDataTable extends AbstractDataTableHandler
{
    const ID = 'clienteReporte';
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
        $repository = $this->doctrine->getRepository('AppBundle:Cliente\Reporte');
        $results = new DataTableResults();

        $clienteId = $request->customData['cliente'];

        $query = $repository->createQueryBuilder('r')->select('COUNT(r.id)')
            ->leftJoin('r.cliente', 'cliente')
            ->where("cliente.id = ${clienteId}");

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('r')
            ->leftJoin('r.cliente', 'cliente')
            ->andWhere("cliente.id = ${clienteId}");

        if ($request->search->value) {
            $query->where('(LOWER(r.createdAt) LIKE :search OR ' .
                'LOWER(r.concepto) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('r.createdAt', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('r.adeudo', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('r.abono', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(r.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Reporte[] $reportes */
        $reportes = $query->getQuery()->getResult();

        foreach ($reportes as $reporte) {
            $results->data[] = [
                $reporte->getCreatedAt()->format('Y-m-d H:i:s'),
                '$' . number_format(($reporte->getAdeudo() / 100), 2),
                '$' . number_format(($reporte->getAbono() / 100), 2),
                $reporte->getConcepto()
            ];
        }

        return $results;
    }
}