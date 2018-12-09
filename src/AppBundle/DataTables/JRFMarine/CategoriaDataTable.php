<?php
/**
 * Created by PhpStorc.
 * User: inrumi
 * Date: 2018-12-09
 * Time: 00:31
 */

namespace AppBundle\DataTables\JRFMarine;


use AppBundle\Entity\JRFMarine\Categoria;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class CategoriaDataTable extends AbstractDataTableHandler
{
    const ID = 'jrfmarine/categorias';
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
        $repository = $this->doctrine->getRepository(Categoria::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('c')->select('COUNT(c.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('c');

        if ($request->search->value) {
            $query->where('(LOWER(c.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('c.nombre', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(c.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Subcategoria[] $categorias */
        $categorias = $query->getQuery()->getResult();

        foreach ($categorias as $categoria) {
            $results->data[] = [
                $categoria->getNombre(),
                $categoria->getImagen(),
                $categoria->getId(),
            ];
        }

        return $results;
    }
}
