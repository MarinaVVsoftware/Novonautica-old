<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 07/02/2019
 * Time: 03:40 PM
 */

namespace AppBundle\DataTables\Marina;


use AppBundle\Entity\MarinaHumedaTarifa;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class TarifaDataTable extends AbstractDataTableHandler
{
    const ID = 'marinaTarifa';
    private $doctrine;

    public function __construct(ManagerRegistry $registry)
    {
        $this->doctrine = $registry;
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
        $repository = $this->doctrine->getRepository(MarinaHumedaTarifa::class);
        $results = new DataTableResults();

        $qb = $repository->createQueryBuilder('mht');

        $results->recordsTotal = $qb->select('COUNT(mht.id)');
        $results->recordsTotal = $results->recordsTotal->getQuery()->getSingleScalarResult();

        $q = $qb->select('mht');

        if ($request->search->value) {
            $q->andWhere(
                '( mht.tipo LIKE :search ' .
                ' OR mht.condicion LIKE :search ' .
                ' OR mht.piesA LIKE :search ' .
                ' OR mht.piesB LIKE :search ' .
                ' OR mht.costo LIKE :search ' .
                ' OR mht.descripcion LIKE :search ' .
                ')')
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->columns as $column) {
            if ($column->search->value) {
                $value = $column->search->value === 'null' ? null : strtolower($column->search->value);
                if ($column->data == 1) {
                    $q->andWhere('mht.tipo LIKE :tipo')
                        ->setParameter('tipo', "%{$value}%");
                } else if ($column->data == 2) {
                    $q->andWhere('mht.condicion LIKE :condicion')
                        ->setParameter('condicion', "%{$value}%");
                } else if ($column->data == 3) {
                    $q->andWhere('mht.piesA LIKE :piesA')
                        ->setParameter('piesA', "%{$value}%");
                } else if ($column->data == 4) {
                    $q->andWhere('mht.piesB LIKE :piesB')
                        ->setParameter('piesB', "%{$value}%");
                } else if ($column->data == 5) {
                    $q->andWhere('mht.costo LIKE :costo')
                        ->setParameter('costo', "%{$value}%");
                } else if ($column->data == 6) {
                    $q->andWhere('mht.descripcion LIKE :descripcion')
                        ->setParameter('descripcion', "%{$value}%");
                }
            }
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('mht.tipo', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('mht.condicion', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('mht.piesA', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('mht.piesB', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('mht.costo', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('mht.descripcion', $order->dir);
            }
        }

        $tarifas = $q->getQuery()->getResult();

        $results->recordsFiltered = count($tarifas);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var MarinaHumedaTarifa $tarifa */
            $tarifa = $tarifas[$index];

            $results->data[] = [
                $tarifa->getTipo() === 1 ? 'Amarre' : 'Electricidad',
                $tarifa->getCondicionCompleta(),
                '$ ' . number_format($tarifa->getCosto() / 100, 2) . ' <small>USD</small>',
                $tarifa->getDescripcion(),
                $tarifa->getId()
            ];
        }

        return $results;
    }
}