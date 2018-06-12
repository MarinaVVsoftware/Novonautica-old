<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/23/18
 * Time: 11:04
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\SlipMovimiento;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class SlipMovimientoDataTable extends AbstractDataTableHandler
{
    CONST ID = 'SlipMovimiento';
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $smRepo = $this->doctrine->getRepository('AppBundle:SlipMovimiento');
        $results = new DataTableResults();

        $query = $smRepo->createQueryBuilder('sm');
        $results->recordsTotal = $query->select('COUNT(sm.id)')->getQuery()->getSingleScalarResult();

        $query = $smRepo->createQueryBuilder('sm')
            ->leftJoin('sm.marinahumedacotizacion', 'mhc')
            ->leftJoin('mhc.barco', 'barco')
            ->leftJoin('sm.slip', 'slip');

        if ($request->search->value) {
            $query
                ->orWhere(
                    $query->expr()->like('LOWER(barco.nombre)', ':search')
                )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $query->addOrderBy('barco.nombre', $order->dir);
            } elseif ($order->column === 1) {
                $query->addOrderBy('slip.id', $order->dir);
            } elseif ($order->column === 2) {
                $query->addOrderBy('mhc.folio', $order->dir);
            } elseif ($order->column === 3) {
                $query->addOrderBy('sm.fechaLlegada', $order->dir);
            } elseif ($order->column === 4) {
                $query->addOrderBy('sm.fechaSalida', $order->dir);
            } elseif ($order->column === 6) {
                $query->addOrderBy('sm.createdAt', $order->dir);
            } elseif ($order->column === 7) {
                $query->addOrderBy('sm.id', $order->dir);
            }
        }

        $movimientos = $query->getQuery()->getResult();

        $results->recordsFiltered = count($movimientos);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;
            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var SlipMovimiento $movimiento */
            $movimiento = $movimientos[$index];
            $cotizacion = $movimiento->getMarinahumedacotizacion();

            if ($cotizacion) {
                $folio = !$cotizacion->getFoliorecotiza() ? $cotizacion->getFolio() : $cotizacion->getFolio() . '-' . $cotizacion->getFoliorecotiza();
            }

            $results->data[] = [
                null === $cotizacion ? $movimiento->getNota() : $cotizacion->getBarco()->getNombre(),
                $movimiento->getSlip()->getId(),
                $folio,
                $movimiento->getFechaLlegada()->format('Y-m-d'),
                $movimiento->getFechaSalida()->format('Y-m-d'),
                $movimiento->getCreatedAt()->format('Y-m-d H:s'),
                $movimiento->getId(),
            ];
        }

        return $results;
    }
}
