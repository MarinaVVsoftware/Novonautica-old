<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2018-12-09
 * Time: 02:07
 */

namespace AppBundle\DataTables\JRFMarine;


use AppBundle\Entity\JRFMarine\Producto;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ProductoDataTable extends AbstractDataTableHandler
{
    const ID = 'jrfmarine/productos';
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
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $repository = $this->doctrine->getRepository(Producto::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('p')->select('COUNT(p.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'categoria')
            ->leftJoin('p.subcategoria', 'subcategoria')
            ->leftJoin('p.marca', 'marca');

        if ($request->search->value) {
            $query->where(
                '(LOWER(p.nombre) LIKE :search OR ' .
                'LOWER(categoria.nombre) LIKE :search OR ' .
                'LOWER(subcategoria.nombre) LIKE :search OR ' .
                'LOWER(marca.nombre) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('p.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('p.precio', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('p.categoria', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('p.marca', $order->dir);
            } elseif ($order->column == 5) {
                $query->addOrderBy('p.existencia', $order->dir);
            }
        }

        $productos = $query->getQuery()->getResult();

        $results->recordsFiltered = count($productos);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var Producto $producto */
            $producto = $productos[$index];
            $categoria = $producto->getCategoria();
            $subcategoria = $producto->getSubcategoria();
            $marca = $producto->getMarca();

            $results->data[] = [
                $producto->getNombre(),
                '$'.number_format(($producto->getPrecio() / 100), 2).' <small>'.($producto->getDivisaNombre()??'').'</small>',
                $categoria->getNombre().' / '.$subcategoria->getNombre(),
                $marca->getNombre(),
                $producto->getImagen(),
                $producto->getExistencia(),
                $producto->getId(),
            ];
        }

        return $results;
    }
}
