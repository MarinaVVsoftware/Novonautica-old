<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2019-02-07
 * Time: 09:58
 */

namespace AppBundle\DataTables\Combustble;


use AppBundle\Entity\Combustible\TipoPago;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class TipoPagoDataTable extends AbstractDataTableHandler
{
    const ID = 'combustible/tipopago';
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
        $repository = $this->doctrine->getRepository(TipoPago::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('tp')->select('COUNT(tp.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('tp');

        if ($request->search->value) {
            $query->where('(LOWER(tp.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('tp.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('tp.porcentaje', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('tp.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(tp.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var TipoPago[] $tipoPagos */
        $tipoPagos = $query->getQuery()->getResult();

        foreach ($tipoPagos as $tipoPago) {
            $results->data[] = [
                $tipoPago->getNombre(),
                $tipoPago->getPorcentaje(),
                $tipoPago->getId(),
            ];
        }

        return $results;
    }
}
