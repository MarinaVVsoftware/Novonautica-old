<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/22/18
 * Time: 1:28 PM
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\ModificacionInventario;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ModificacionInventarioDataTable extends AbstractDataTableHandler
{
    const ID = 'modificacion_inventario';
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
        $repository = $this->doctrine->getRepository(ModificacionInventario::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('m')->select('COUNT(m.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('m')
        ->leftJoin('m.empresa', 'e');

        if ($request->search->value) {
            $query->where('(LOWER(m.empresa) LIKE :search OR ' .
                'LOWER(m.comentario) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('m.empresa', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('m.comentario', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('m.createdAt', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(m.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var ModificacionInventario[] $modificacionInventarios */
        $modificacionInventarios = $query->getQuery()->getResult();

        foreach ($modificacionInventarios as $modificacionInventario) {
            $results->data[] = [
                $modificacionInventario->getEmpresa()->getNombre(),
                $modificacionInventario->getComentario(),
                $modificacionInventario->getCreatedAt()->format('d/m/y'),
                $modificacionInventario->getId(),
            ];
        }

        return $results;
    }
}
