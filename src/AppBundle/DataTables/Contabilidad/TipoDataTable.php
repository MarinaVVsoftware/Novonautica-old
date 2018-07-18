<?php
/**
 * User: inrumi
 * Date: 7/18/18
 * Time: 12:03
 */

namespace AppBundle\DataTables\Contabilidad;


use AppBundle\Entity\Contabilidad\Egreso\Tipo;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class TipoDataTable extends AbstractDataTableHandler
{
    const ID = 'egreso/tipo';
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
        $repository = $this->doctrine->getRepository(Tipo::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('t')->select('COUNT(t.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('t');

        if ($request->search->value) {
            $query->where('(LOWER(t.descripcion) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('t.descripcion', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('t.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(t.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Tipo[] $tipos */
        $tipos = $query->getQuery()->getResult();

        foreach ($tipos as $tipo) {
            $results->data[] = [
                $tipo->getDescripcion(),
                $tipo->getId(),
            ];
        }

        return $results;
    }
}
