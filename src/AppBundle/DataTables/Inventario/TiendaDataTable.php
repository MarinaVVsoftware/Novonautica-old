<?php

namespace AppBundle\DataTables\Inventario;

use AppBundle\Entity\Tienda\Producto;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class TiendaDataTable extends AbstractDataTableHandler
{
    const ID = 'inventario_tienda';
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


        // Hasta ahora es mas rapido hidratar con un query a cada fila
        // En vez de hidratar el query con un solo join
        $query = $repository->createQueryBuilder('p')
            ->select('p', 'c','cps','cu')
            ->leftJoin('p.categoria', 'c')
            ->leftJoin('p.claveProdServ','cps')
            ->leftJoin('p.claveUnidad', 'cu');

        if ($request->search->value) {
            $query->where('(LOWER(p.nombre) LIKE :search '.
                ' OR LOWER(c.nombre) LIKE :search '.
                ' OR LOWER(p.preciocolaborador) LIKE :search '.
                ' OR LOWER(p.precio) LIKE :search '.
                ' OR cps.claveProdServ LIKE :search' .
                ' OR cu.claveUnidad LIKE :search' .
                ')'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('c.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('p.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('cps.claveProdServ', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('cu.claveUnidad', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('cu.nombre', $order->dir);
            } elseif ($order->column == 5) {
                $query->addOrderBy('p.precio', $order->dir);
            } elseif ($order->column == 6) {
                $query->addOrderBy('p.preciocolaborador', $order->dir);
            } elseif ($order->column == 7) {
                $query->addOrderBy('p.existencia', $order->dir);
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
                $producto->getCategoria() ? $producto->getCategoria()->getNombre() : 'Sin categoría',
                $producto->getNombre(),
                $producto->getClaveProdServ()?$producto->getClaveProdServ()->getClaveProdServ():'',
                $producto->getClaveUnidad()?$producto->getClaveUnidad()->getClaveUnidad():'',
                $producto->getClaveUnidad()?$producto->getClaveUnidad()->getNombre():'',
                "$".number_format($producto->getPrecio() / 100, 2)." MXN",
                "$".number_format($producto->getPreciocolaborador() / 100, 2)." MXN",
                $producto->getExistencia()?$producto->getExistencia():'0',
            ];
        }

        return $results;
    }
}
