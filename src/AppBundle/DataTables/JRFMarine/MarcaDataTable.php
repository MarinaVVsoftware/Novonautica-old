<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2018-12-09
 * Time: 00:12
 */

namespace AppBundle\DataTables\JRFMarine;


use AppBundle\Entity\JRFMarine\Marca;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class MarcaDataTable extends AbstractDataTableHandler
{
    const ID = 'jrfmarine/marcas';
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return DataTableResults
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $repository = $this->doctrine->getRepository(Marca::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('m')->select('COUNT(m.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('m');

        if ($request->search->value) {
            $query->where('(LOWER(m.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('m.nombre', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(m.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Marca[] $marcas */
        $marcas = $query->getQuery()->getResult();

        foreach ($marcas as $marca) {
            $results->data[] = [
                $marca->getNombre(),
                $marca->getImagen(),
                $marca->getId(),
            ];
        }

        return $results;
    }
}
