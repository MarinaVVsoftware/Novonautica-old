<?php
/**
 * User: inrumi
 * Date: 7/5/18
 * Time: 15:56
 */

namespace AppBundle\DataTables\Contabilidad\Entrada;


use AppBundle\Entity\Contabilidad\Egreso\Entrada\Concepto;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ConceptoDataTable extends AbstractDataTableHandler
{
    const ID = 'entrada/concepto';
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
        $repository = $this->doctrine->getRepository(Concepto::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('c')->select('COUNT(c.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('c')
            ->leftJoin('c.empresa', 'e');

        if ($request->search->value) {
            $query->where(
                '(LOWER(c.descripcion) LIKE :search OR ' .
                'LOWER(e.nombre) LIKE :search'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('c.descripcion', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('e.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('c.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(c.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Concepto[] $conceptos */
        $conceptos = $query->getQuery()->getResult();

        foreach ($conceptos as $concepto) {
            $results->data[] = [
                $concepto->getDescripcion(),
                $concepto->getEmpresa()->getNombre(),
                $concepto->getId(),
            ];
        }

        return $results;
    }
}
