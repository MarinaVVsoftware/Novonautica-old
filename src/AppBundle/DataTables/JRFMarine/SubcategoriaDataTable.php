<?php
/**
 * Created by PhpStors.
 * User: inrumi
 * Date: 2018-12-09
 * Time: 00:31
 */

namespace AppBundle\DataTables\JRFMarine;


use AppBundle\Entity\JRFMarine\Categoria\Subcategoria;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class SubcategoriaDataTable extends AbstractDataTableHandler
{
    const ID = 'jrfmarine/subcategorias';
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
        $repository = $this->doctrine->getRepository(Subcategoria::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('s')->select('COUNT(s.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('s');

        if (isset($request->customData['categoria'])) {
            $query->andWhere('IDENTITY(s.categoria) = :categoria');
            $query->setParameter('categoria', $request->customData['categoria']);
        }

        if ($request->search->value) {
            $query->where('(LOWER(s.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('s.nombre', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(s.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Subcategoria[] $subcategorias */
        $subcategorias = $query->getQuery()->getResult();

        foreach ($subcategorias as $subcategoria) {
            $results->data[] = [
                $subcategoria->getNombre(),
                $subcategoria->getCategoria()->getNombre(),
                $subcategoria->getImagen(),
                $subcategoria->getId(),
            ];
        }

        return $results;
    }
}
