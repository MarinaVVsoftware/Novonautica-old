<?php
/**
 * User: inrumi
 * Date: 6/26/18
 * Time: 17:02
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Tienda\Inventario\Registro;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class RegistrosDataTable extends AbstractDataTableHandler
{
    const ID = 'registros';
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
        $repository = $this->doctrine->getRepository(Registro::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.tipo = ?1')
            ->setParameter(1, $request->customData['tipo'] === 'entrada');

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('r')
            ->where('r.tipo = ?1')
            ->setParameter(1, $request->customData['tipo'] === 'entrada');

        if ($request->search->value) {
            $query->where('(LOWER(r.fecha) LIKE :search OR ' .
                'LOWER(r.referencia) LIKE :search OR ' .
                'LOWER(re.total) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('r.fecha', $order->dir);
                $query->addOrderBy('r.id', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('r.referencia', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('r.total', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(r.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Registro[] $registros */
        $registros = $query->getQuery()->getResult();

        foreach ($registros as $registro) {
            $results->data[] = [
                $registro->getFecha()->format('d/m/Y'),
                $registro->getReferencia() ?: 'No existe referencia',
                '$' . number_format(($registro->getTotal() / 100), 2),
                $registro->getId(),
            ];
        }

        return $results;
    }
}
