<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/12/18
 * Time: 15:27
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Cliente\Notificacion;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ClienteNotificacionDataTable extends AbstractDataTableHandler
{
    const ID = 'clienteNotificacion';
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
        $repository = $this->doctrine->getRepository('AppBundle:Cliente\Notificacion');
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->leftJoin('n.cliente', 'c')
            ->groupBy('c.id, n.tipo');

        $results->recordsTotal = count($query->getQuery()->getScalarResult());

        $query = $repository->createQueryBuilder('n')
            ->select('c.nombre AS cliente', 'u.nombre AS usuario', 'n.tipo', 'n.fecha')
            ->addSelect('COUNT(n.tipo) AS enviadas')
            ->leftJoin('n.cliente', 'c')
            ->leftJoin('n.usuario', 'u')
            ->addGroupBy('c.id, n.tipo')
        ;

        if ($request->search->value) {
            $query->where(
                '(LOWER(c.nombre) LIKE :search OR ' .
                'LOWER(u.nombre) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('c.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('u.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('n.tipo', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('n.fecha', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->addSelect('COUNT(n.id)');
        $results->recordsFiltered = count($queryCount->getQuery()->getScalarResult());

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Notificacion[] $notificaciones */
        $notificaciones = $query->getQuery()->getResult();

        foreach ($notificaciones as $notificacion) {
            $results->data[] = [
                $notificacion['cliente'],
                $notificacion['usuario'],
                Notificacion::findNamedTipo($notificacion['tipo']),
                $notificacion['enviadas'],
                $notificacion['fecha']->format('d F Y'),
            ];
        }

        return $results;
    }
}