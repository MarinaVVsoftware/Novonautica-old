<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 12/20/17
 * Time: 15:29
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Producto;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ProductoDataTable extends AbstractDataTableHandler
{
    const ID = 'producto';
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
     * @return DataTableResults
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $productoRepo = $this->doctrine->getRepository('AppBundle:Producto');
        $results = new DataTableResults();

        $qb = $productoRepo->createQueryBuilder('pro');
        $results->recordsTotal = $qb->select('COUNT(pro.id)')->getQuery()->getSingleScalarResult();

        $query = $qb
            ->select('pro', 'mar', 'cat', 'sub')
            ->leftJoin('pro.marca', 'mar')
            ->leftJoin('pro.categoria', 'cat')
            ->leftJoin('pro.subcategoria', 'sub');

        if ($request->search->value) {
            $query->where('(LOWER(pro.nombre) LIKE :search OR ' .
                'LOWER(pro.precio) LIKE :search OR ' .
                'LOWER(pro.modelo) LIKE :search OR ' .
                'LOWER(mar.nombre) LIKE :search OR ' .
                'LOWER(cat.nombre) LIKE :search OR ' .
                'LOWER(sub.nombre) LIKE :search OR ' .
                'LOWER(pro.unidad) LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->columns as $column) {
            if ($column->search->value) {
                $value = strtolower($column->search->value);
                if ($column->data == 3) {
                    $query->andWhere('LOWER(mar.nombre) = :marca');
                    $query->setParameter('marca', strtolower("{$value}"));
                } elseif ($column->data == 4) {
                    $query->andWhere('LOWER(cat.nombre) = :categoria');
                    $query->setParameter('categoria', strtolower("{$value}"));
                } elseif ($column->data == 5) {
                    $query->andWhere('LOWER(sub.nombre) = :subcategoria');
                    $query->setParameter('subcategoria', strtolower("{$value}"));
                }
            }
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('pro.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('pro.precio', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('pro.modelo', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('mar.nombre', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('cat.nombre', $order->dir);
            } elseif ($order->column == 5) {
                $query->addOrderBy('sub.nombre', $order->dir);
            } elseif ($order->column == 6) {
                $query->addOrderBy('pro.unidad', $order->dir);
            }
        }

        /** @var Producto[] $productos */
        $productos = $query->getQuery()->getResult();

        $results->recordsFiltered = count($productos);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            $producto = $productos[$index];

            $results->data[] = [
                $producto->getNombre(),
                $producto->getPrecio(),
                $producto->getModelo(),
                $producto->getMarca() ? $producto->getMarca()->getNombre() : '',
                $producto->getCategoria() ? $producto->getCategoria()->getNombre() : '',
                $producto->getSubcategoria() ? $producto->getSubcategoria()->getNombre() : '',
                $producto->getUnidad(),
                $producto->getImagen(),
                $producto->getId()
            ];
        }

        return $results;
    }
}