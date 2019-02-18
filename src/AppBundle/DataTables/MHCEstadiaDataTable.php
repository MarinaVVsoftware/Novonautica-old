<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/15/18
 * Time: 12:22
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\MarinaHumedaCotizacion;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class MHCEstadiaDataTable extends AbstractDataTableHandler
{
    const ID = 'cotizacionEstadia';
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $mhcRepo = $this->doctrine->getRepository('AppBundle:MarinaHumedaCotizacion');
        $results = new DataTableResults();

        $qb = $mhcRepo->createQueryBuilder('mhce');
        $results->recordsTotal = $qb->select('COUNT(mhce.id)')
            ->leftJoin('mhce.mhcservicios', 'servicios')
            ->where($qb->expr()->eq('servicios.tipo', 1))
            ->orWhere($qb->expr()->eq('servicios.tipo', 2))
            ->getQuery()
            ->getSingleScalarResult();

        $q = $qb
            ->select('mhce', 'servicios', 'barco', 'cliente', 'slip', 'movimiento')
            ->leftJoin('mhce.barco', 'barco')
            ->leftJoin('mhce.cliente', 'cliente')
            ->leftJoin('mhce.slipmovimiento', 'movimiento')
            ->leftJoin('mhce.slip', 'slip');

        if ($request->search->value) {
            $q->andWhere('(LOWER(mhce.folio) LIKE :search OR '.
                'LOWER(cliente.nombre) LIKE :search OR '.
                'LOWER(barco.nombre) LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->columns as $column) {
            if ($column->search->value) {
                $value = $column->search->value === 'null' ? null : strtolower($column->search->value);

                if ($value) {
                    if ($column->data == 1) {
                        $q->andWhere('LOWER(cliente.nombre) LIKE :cliente');
                        $q->setParameter('cliente', "%{$value}%");
                    }
                    elseif ($column->data == 5) {
                        $q->andWhere('mhce.validanovo = :validacion');
                        $q->setParameter('validacion', $value);
                    }
                    elseif ($column->data == 6) {
                        $q->andWhere('mhce.validacliente = :validacion');
                        $q->setParameter('validacion', $value);
                    }
                    elseif ($column->data == 7) {
                        $q->andWhere('mhce.estatuspago = :validacion');
                        $q->setParameter('validacion', $value);
                    }
                }
            }
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('mhce.folio', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('cliente.nombre', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('mhce.fechaLlegada', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('mhce.slip', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('mhce.total', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('mhce.validanovo', $order->dir);
            } elseif ($order->column === 6) {
                $q->addOrderBy('mhce.validacliente', $order->dir);
            } elseif ($order->column === 7) {
                $q->addOrderBy('mhce.estatuspago', $order->dir);
            }
        }

        $cotizaciones = $q->getQuery()->getResult();

        $results->recordsFiltered = count($cotizaciones);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var MarinaHumedaCotizacion $cotizacion */
            $cotizacion = $cotizaciones[$index];

            $servicioEstadia = $cotizacion->getMHCservicios()->filter(function ($servicio) {
                if ($servicio->getTipo() === 1) {
                    return $servicio;
                }
            });


            $results->data[] = [
                !$cotizacion->getFoliorecotiza() ? $cotizacion->getFolio() : $cotizacion->getFolio().'-'.$cotizacion->getFoliorecotiza(),
                [
                    'cliente' => $cotizacion->getCliente()->getNombre(),
                    'embarcacion' => $cotizacion->getBarco()->getNombre(),
                ],
                [
                    'llegada' => $cotizacion->getFechaLlegada() ? $cotizacion->getFechaLlegada()->format('d/m/Y') : '',
                    'salida' => $cotizacion->getFechaSalida() ? $cotizacion->getFechaSalida()->format('d/m/Y') : '',
                    'dias' => $servicioEstadia ? $servicioEstadia->first()->getCantidad() : 0,
                ],
                $cotizacion->getSlip() ? $cotizacion->getSlip()->__toString() : 'Sin asignar',
                [
                    'subtotal' => '$'.number_format($cotizacion->getSubtotal() / 100, 2).' USD',
                    'descuento' => '$'.number_format($cotizacion->getDescuentototal() / 100, 2).' USD',
                    'iva' => '$'.number_format($cotizacion->getIvatotal() / 100, 2).' USD',
                    'interesMoratorio' => '$'.number_format($cotizacion->getMoratoriaTotal() / 100, 2).' USD',
                    'total' => '$'.number_format($cotizacion->getTotal() / 100, 2).' USD',
                ],
                $cotizacion->getValidanovo(),
                $cotizacion->getValidacliente(),
                $cotizacion->getEstatuspago(),
                [
                    'id' => $cotizacion->getId(),
                    'estatus' => $cotizacion->getEstatus(),
                ],
            ];
        }

        return $results;
    }
}
