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
        $repository = $this->doctrine->getRepository(Entrada::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('e')->select('COUNT(e.id)');
        $results->recordsTotal = $query->getQuery()->getScalarResult();

        $query = $repository->createQueryBuilder('e')
            ->select(
                'p.nombre AS producto',
                'COALESCE(SUM(CASE WHEN r.tipo = 1 THEN e.cantidad ELSE -e.cantidad END), 0) AS quantity'
            )
            ->leftJoin(Registro::class, 'r', 'WITH', 'r.id = e.registro')
            ->leftJoin(Producto::class, 'p', 'WITH', 'p.id = e.producto')
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

        if ($request->length > 0) {
            $query->setMaxResults($request->length);
            $query->setFirstResult($request->start);
        }

        /** @var Producto[] $productos */
        $productos = $query->getQuery()->getResult();

        $results->recordsFiltered = count($productos);

        foreach ($productos as $producto) {
            $results->data[] = [
                $producto['producto'],
                $producto['quantity'],
            ];
        }

        return $results;
    }
}
