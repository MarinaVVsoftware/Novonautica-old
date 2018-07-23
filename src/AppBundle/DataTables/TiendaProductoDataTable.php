<?php

namespace AppBundle\DataTables;


use AppBundle\Entity\Tienda\Producto;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class TiendaProductoDataTable extends AbstractDataTableHandler
{
    const ID = 'tienda_producto';
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
        $repository = $this->doctrine->getRepository('AppBundle:Tienda\Producto');

        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('p')->select('COUNT(p.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('p');

        if ($request->search->value) {
            $query->where('(LOWER(p.nombre) LIKE :search OR '.
                'LOWER(p.preciocolaborador) LIKE :search OR '.
                'LOWER(p.precio) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('p.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('p.precio', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('p.preciocolaborador', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('p.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(p.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Producto[] $productos */
        $productos = $query->getQuery()->getResult();

        foreach ($productos as $producto) {
            $results->data[] = [
                $producto->getNombre(),
                "$".number_format($producto->getPrecio() / 100, 2)." MXN",
                "$".number_format($producto->getPreciocolaborador() / 100, 2)." MXN",
                $producto->getImagen(),
                [
                    'id' => $producto->getId(),
                    'estatus' => $producto->isActive(),
                ],
            ];
        }

        return $results;
    }
}
