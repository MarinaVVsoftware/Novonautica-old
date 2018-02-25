<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 21/02/2018
 * Time: 05:44 PM
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\AstilleroCotizacion;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class AstilleroDataTable extends AbstractDataTableHandler
{
    const ID = 'cotizacionAstillero';
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
     * @throws DataTableException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $acRepo = $this->doctrine->getRepository('AppBundle:AstilleroCotizacion');
        $results = new DataTableResults();
        $qb = $acRepo->createQueryBuilder('ac');
        $results->recordsTotal = $qb->select('COUNT(ac.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $q = $qb
            ->select('ac', 'barco', 'cliente')
            ->leftJoin('ac.barco', 'barco')
            ->leftJoin('ac.cliente', 'cliente')
        ;

        if ($request->search->value) {
            $q->where('(LOWER(ac.folio) LIKE :search OR ' .
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
                        $q->andWhere('ac.validanovo = :validacion')
                            ->setParameter('validacion', $value);
                    } else {
                        $q->andWhere('ac.validanovo = 0');
                    }
                } else if ($column->data == 9) {
                    if ($value) {
                        $q->andWhere('ac.validacliente = :aceptacion')
                            ->setParameter('aceptacion', $value);
                    } else {
                        $q->andWhere('ac.validacliente = 0');
                    }
                } else if ($column->data == 10) {
                    if ($value) {
                        $q->andWhere('ac.estatuspago = :pago')->setParameter('pago', $value);
                    } else {
                        $q->andWhere('ac.estatuspago IS NULL');
                    }
                }
            }
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('ac.folio', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('cliente.nombre', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('barco.nombre', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('ac.fechaLlegada', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('ac.fechaSalida', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('ac.slip', $order->dir);
            } elseif ($order->column === 6) {
                $q->addOrderBy('ac.descuento', $order->dir);
            } elseif ($order->column === 7) {
                $q->addOrderBy('ac.subtotal', $order->dir);
            } elseif ($order->column === 8) {
                $q->addOrderBy('ac.ivatotal', $order->dir);
            } elseif ($order->column === 9) {
                $q->addOrderBy('ac.descuentototal', $order->dir);
            } elseif ($order->column === 10) {
                $q->addOrderBy('ac.total', $order->dir);
            } elseif ($order->column === 11) {
                $q->addOrderBy('ac.validanovo', $order->dir);
            } elseif ($order->column === 12) {
                $q->addOrderBy('ac.validacliente', $order->dir);
            } elseif ($order->column === 13) {
                $q->addOrderBy('ac.estatuspago', $order->dir);
            }
        }

        $acotizaciones = $q->getQuery()->getResult();
        $results->recordsFiltered = count($acotizaciones);
        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var AstilleroCotizacion $cotizacion */
            $cotizacion = $acotizaciones[$index];
            $folio = $cotizacion->getFoliorecotiza()
                ? $cotizacion->getFolio() . '-' . $cotizacion->getFoliorecotiza()
                : $cotizacion->getFolio();

            $results->data[] = [
                $folio,
                $cotizacion->getCliente()->getNombre(),
                $cotizacion->getBarco()->getNombre(),
                $cotizacion->getFechaLlegada()->format('d/m/Y') ?? '',
                $cotizacion->getFechaSalida()->format('d/m/Y') ?? '',
                '$' . number_format($cotizacion->getSubtotal(), 2),
                '$' . number_format($cotizacion->getIvatotal(), 2),
                '$' . number_format($cotizacion->getTotal(), 2),
                $cotizacion->getValidanovo(),
                $cotizacion->getValidacliente(),
                $cotizacion->getEstatuspago(),
                ['id' => $cotizacion->getId(), 'estatus' => $cotizacion->getEstatus()]
            ];
        }
        return $results;
    }
}