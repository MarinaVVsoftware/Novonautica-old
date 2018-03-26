<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/26/18
 * Time: 11:26
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Tienda\Solicitud;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class TiendaDataTable extends AbstractDataTableHandler
{
    const ID = 'tienda';
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
        $repository = $this->doctrine->getRepository('AppBundle:Tienda\Solicitud');
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('t')->select('COUNT(t.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('t')
            ->leftJoin('t.nombrebarco', 'b');

        if ($request->search->value) {
            $query->where('(LOWER(b.nombre) LIKE :search OR ' .
                'LOWER(t.solicitudEspecial) LIKE :search OR ' .
                'LOWER(u.correo) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('t.fecha', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('b.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('t.solicitudEspecial', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('t.total', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('t.totalusd', $order->dir);
            } elseif ($order->column == 5) {
                $query->addOrderBy('t.pagado', $order->dir);
            } elseif ($order->column == 6) {
                $query->addOrderBy('t.entregado', $order->dir);
            } elseif ($order->column == 7) {
                $query->addOrderBy('t.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(t.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Solicitud[] $solicitudes */
        $solicitudes = $query->getQuery()->getResult();

        foreach ($solicitudes as $solicitud) {
            $results->data[] = [
                $solicitud->getFecha()->format('d/m/Y'),
                $solicitud->getNombrebarco()->getNombre(),
                $solicitud->getSolicitudEspecial() ?: 'N/A',
                '$' . number_format(($solicitud->getTotal() / 100), 2),
                '$' . number_format(($solicitud->getTotalusd() / 100), 2),
                $solicitud->getPagado(),
                $solicitud->getEntregado(),
                $solicitud->getId()
            ];
        }

        return $results;
    }
}