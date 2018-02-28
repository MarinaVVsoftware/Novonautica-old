<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/16/18
 * Time: 11:47
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\MarinaHumedaCotizacion;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class MHCGasolinaDataTable extends AbstractDataTableHandler
{
    const ID = 'cotizacionGasolina';
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
            ->where($qb->expr()->eq('servicios.tipo',3))
            ->getQuery()
            ->getSingleScalarResult();

        $q = $qb
            ->select('mhce', 'barco', 'cliente', 'slip', 'movimiento')
            ->leftJoin('mhce.barco', 'barco')
            ->leftJoin('mhce.cliente', 'cliente')
            ->leftJoin('mhce.slipmovimiento', 'movimiento')
            ->leftJoin('mhce.slip', 'slip')
        ;

        if ($request->search->value) {
            $q->andWhere('(LOWER(mhce.folio) LIKE :search OR ' .
                'LOWER(cliente.nombre) LIKE :search OR ' .
                'LOWER(barco.nombre) LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->columns as $column) {
            if ($column->search->value) {
                $value = $column->search->value === 'null' ? null : strtolower($column->search->value);

                if ($column->data == 1) {
                    $q->andWhere('LOWER(cliente.nombre) LIKE :cliente')
                        ->setParameter('cliente', "%{$value}%");
                } else if ($column->data == 2) {
                    $q->andWhere('LOWER(barco.nombre) LIKE :barco')
                        ->setParameter('barco', "%{$value}%");
                } else if ($column->data == 8) {
                    if ($value) {
                        $q->andWhere('mhce.validanovo = :validacion')
                            ->setParameter('validacion', $value);
                    } else {
                        $q->andWhere('mhce.validanovo = 0');
                    }
                } else if ($column->data == 9) {
                    if ($value) {
                        $q->andWhere('mhce.validacliente = :aceptacion')
                            ->setParameter('aceptacion', $value);
                    } else {
                        $q->andWhere('mhce.validacliente = 0');
                    }
                } else if ($column->data == 10) {
                    if ($value) {
                        $q->andWhere('mhce.estatuspago = :pago')->setParameter('pago', $value);
                    } else {
                        $q->andWhere('mhce.estatuspago IS NULL');
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
                $q->addOrderBy('barco.nombre', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('mhce.descuento', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('mhce.subtotal', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('mhce.ivatotal', $order->dir);
            } elseif ($order->column === 6) {
                $q->addOrderBy('mhce.descuentototal', $order->dir);
            } elseif ($order->column === 7) {
                $q->addOrderBy('mhce.total', $order->dir);
            } elseif ($order->column === 8) {
                $q->addOrderBy('mhce.validanovo', $order->dir);
            } elseif ($order->column === 9) {
                $q->addOrderBy('mhce.validacliente', $order->dir);
            } elseif ($order->column === 10) {
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

            $results->data[] = [
                !$cotizacion->getFoliorecotiza() ? $cotizacion->getFolio() : $cotizacion->getFolio() . '-' . $cotizacion->getFoliorecotiza(),
                $cotizacion->getCliente()->getNombre(),
                $cotizacion->getBarco()->getNombre(),
                ($cotizacion->getDescuento() ?? 0 ) . '%',
                '$' . number_format($cotizacion->getSubtotal() / 100, 2),
                '$' . number_format($cotizacion->getIvatotal() / 100, 2),
                '$' . number_format($cotizacion->getDescuentototal() / 100, 2),
                '$' . number_format($cotizacion->getTotal() / 100, 2),
                $cotizacion->getValidanovo(),
                $cotizacion->getValidacliente(),
                $cotizacion->getEstatuspago(),
                ['id' => $cotizacion->getId(), 'estatus' => $cotizacion->getEstatus()]
            ];
        }

        return $results;
    }
}