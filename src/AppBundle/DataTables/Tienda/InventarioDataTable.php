<?php
/**
 * User: inrumi
 * Date: 6/27/18
 * Time: 11:24
 */

namespace AppBundle\DataTables\Tienda;


use AppBundle\Entity\Tienda\Inventario\Registro;
use AppBundle\Entity\Tienda\Inventario\Registro\Entrada;
use AppBundle\Entity\Tienda\Producto;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class InventarioDataTable extends AbstractDataTableHandler
{
    const ID = 'inventario';
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
        $repository = $this->doctrine->getRepository(Producto::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('p')->select('COUNT(p.id)');

        $results->recordsTotal = $query->getQuery()->getScalarResult();

        $query = $repository->createQueryBuilder('p')
            ->select(
                'p.nombre',
                'COALESCE(SUM(CASE WHEN r.tipo = 1 THEN e.cantidad ELSE -e.cantidad END), 0) AS quantity'
            )
            ->leftJoin(Entrada::class, 'e', 'WITH', 'p.id = e.producto')
            ->leftJoin(Registro::class, 'r', 'WITH', 'r.id = e.registro')
            ->groupBy('p.id');

        if ($request->search->value) {
            $query->where('(LOWER(p.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('p.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('quantity', $order->dir);
            }
        }

        $queryCount = clone $query;
        $results->recordsFiltered = count($queryCount->getQuery()->getScalarResult());

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Producto[] $productos */
        $productos = $query->getQuery()->getResult();

        foreach ($productos as $producto) {
            $results->data[] = [
                $producto['nombre'],
                $producto['quantity'],
            ];
        }

        return $results;
    }
}
