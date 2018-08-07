<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 16/03/2018
 * Time: 03:44 PM
 */

namespace AppBundle\DataTables;

use AppBundle\Entity\Astillero\Producto;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class AstilleroProductoDataTable extends AbstractDataTableHandler
{
    CONST ID = 'AstilleroProducto';
    private $doctrine;

    public function __construct(ManagerRegistry $registry)
    {
        $this->doctrine = $registry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $astilleroProductoRepo = $this->doctrine->getRepository('AppBundle:Astillero\Producto');
        $results = new DataTableResults();

        $qb = $astilleroProductoRepo->createQueryBuilder('ap');
        $results->recordsTotal = $qb->select('COUNT(ap.id)')->getQuery()->getSingleScalarResult();

        $q = $qb->select('ap');

        if ($request->search->value) {
            $q->where('(LOWER(ap.nombre) LIKE :search OR ' .
                'ap.identificador LIKE :search OR ap.precio LIKE :search OR ap.unidad LIKE :search OR ap.proveedor LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('ap.identificador', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('ap.proveedor', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('ap.nombre', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('ap.precio', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('ap.unidad', $order->dir);
            }
        }

        $productos = $q->getQuery()->getResult();

        $results->recordsFiltered = count($productos);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var Producto $producto */
            $producto = $productos[$index];

            $results->data[] = [
                $producto->getIdentificador(),
                $producto->getProveedor() ? $producto->getProveedor()->getNombre() : '',
                $producto->getNombre(),
                '$' . number_format($producto->getPrecio() / 100, 2).' MXN',
                $producto->getUnidad(),
                $producto->getId()
            ];
        }

        return $results;
    }
}