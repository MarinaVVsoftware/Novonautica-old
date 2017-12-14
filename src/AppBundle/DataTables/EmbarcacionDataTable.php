<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 12/8/17
 * Time: 16:36
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Embarcacion;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableHandlerInterface;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class EmbarcacionDataTable extends AbstractDataTableHandler
{
    const ID = 'embarcaciones';
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
        $embarcacionRepo = $this->doctrine->getRepository('AppBundle:Embarcacion');
        $results = new DataTableResults();

        $query = $embarcacionRepo->createQueryBuilder('em');
        $results->recordsTotal = $query->select('COUNT(em.id)')->getQuery()->getSingleScalarResult();

        $query = $embarcacionRepo->createQueryBuilder('em')
            ->select('em', 'ma', 'mo', 'pa')
            ->leftJoin('em.marca', 'ma')
            ->leftJoin('em.modelo', 'mo')
            ->leftJoin('em.pais', 'pa');

        if ($request->search->value) {
            $query
                ->orWhere(
                    $query->expr()->like('LOWER(em.nombre)', ':search'),
                    $query->expr()->like('LOWER(mo.nombre)', ':search'),
                    $query->expr()->like('LOWER(ma.nombre)', ':search'),
                    $query->expr()->like('LOWER(pa.name)', ':search'),
                    $query->expr()->like('LOWER(em.longitud)', ':search'),
                    $query->expr()->like('LOWER(em.precio)', ':search'),
                    $query->expr()->like('LOWER(em.ano)', ':search')
                )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->columns as $column) {
            if ($column->data == 0 && $column->search->value) {
                $query
                    ->andWhere($query->expr()->like('LOWER(em.nombre)', '?0'))
                    ->setParameter(0, strtolower("%{$column->search->value}%"));
            } elseif ($column->data == 1 && $column->search->value) {
                $query
                    ->andWhere($query->expr()->like('LOWER(pa.name)', '?1'))
                    ->setParameter(1, strtolower("%{$column->search->value}%"));
            } elseif ($column->data == 2 && $column->search->value) {
                $query
                    ->andWhere($query->expr()->like('LOWER(mo.nombre)', '?2'))
                    ->setParameter(2, strtolower("%{$column->search->value}%"));
            } elseif ($column->data == 3 && $column->search->value) {
                $query
                    ->andWhere($query->expr()->like('LOWER(ma.nombre)', '?3'))
                    ->setParameter(3, strtolower("%{$column->search->value}%"));
            } elseif ($column->data == 4 && $column->search->value) {
                $query
                    ->andWhere($query->expr()->like('LOWER(em.longitud)', '?4'))
                    ->setParameter(4, strtolower("%{$column->search->value}%"));
            } elseif ($column->data == 5 && $column->search->value) {
                $query
                    ->andWhere($query->expr()->like('LOWER(em.precio)', '?5'))
                    ->setParameter(5, strtolower("%{$column->search->value}%"));
            } elseif ($column->data == 6 && $column->search->value) {
                $query
                    ->andWhere($query->expr()->like('LOWER(em.ano)', '?6'))
                    ->setParameter(6, strtolower("%{$column->search->value}%"));
            }
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $query->addOrderBy('em.nombre', $order->dir);
            } elseif ($order->column === 1) {
                $query->addOrderBy('em.pais', $order->dir);
            } elseif ($order->column === 2) {
                $query->addOrderBy('em.modelo', $order->dir);
            } elseif ($order->column === 3) {
                $query->addOrderBy('em.marca', $order->dir);
            } elseif ($order->column === 4) {
                $query->addOrderBy('em.longitud', $order->dir);
            } elseif ($order->column === 5) {
                $query->addOrderBy('em.precio', $order->dir);
            } elseif ($order->column === 6) {
                $query->addOrderBy('em.ano', $order->dir);
            }
        }

        /** @var Embarcacion[] $embarcaciones */
        $embarcaciones = $query->getQuery()->getResult();

        $results->recordsFiltered = count($embarcaciones);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            $embarcacion = $embarcaciones[$index];

            $results->data[] = [
                $embarcacion->getNombre(),
                $embarcacion->getPais() ? $embarcacion->getPais()->getName() : '',
                $embarcacion->getModelo() ? $embarcacion->getModelo()->getNombre() : 'Custom',
                $embarcacion->getMarca() ? $embarcacion->getMarca()->getNombre() : 'Custom',
                $embarcacion->getLongitud(),
                '$ ' . number_format($embarcacion->getPrecio() / 100, 2) . ' USD',
                $embarcacion->getAno(),
                $embarcacion->getId(),
            ];
        }

        return $results;
    }
}