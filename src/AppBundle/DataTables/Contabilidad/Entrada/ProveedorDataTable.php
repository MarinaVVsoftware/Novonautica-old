<?php
/**
 * User: inrumi
 * Date: 7/5/18
 * Time: 15:56
 */

namespace AppBundle\DataTables\Contabilidad\Entrada;


use AppBundle\Entity\Contabilidad\Egreso\Entrada\Proveedor;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ProveedorDataTable extends AbstractDataTableHandler
{
    const ID = 'entrada/proveedor';
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
        $repository = $this->doctrine->getRepository(Proveedor::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('p')->select('COUNT(p.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('p');

        if ($request->search->value) {
            $query->where('(LOWER(p.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('p.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('p.telefono', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('p.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(p.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Proveedor[] $proveedores */
        $proveedores = $query->getQuery()->getResult();

        foreach ($proveedores as $proveedor) {
            $results->data[] = [
                $proveedor->getNombre(),
                $proveedor->getTelefono(),
                $proveedor->getId(),
            ];
        }

        return $results;
    }
}
