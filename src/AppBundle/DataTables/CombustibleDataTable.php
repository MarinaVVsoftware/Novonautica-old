<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/16/18
 * Time: 11:47
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Combustible;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class CombustibleDataTable extends AbstractDataTableHandler
{
    const ID = 'combustible';
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

        $mhcRepo = $this->doctrine->getRepository('AppBundle:Combustible');
        $results = new DataTableResults();

        $qb = $mhcRepo->createQueryBuilder('c');
        $results->recordsTotal = $qb->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $q = $qb
            ->select('c','barco','cliente')
            ->leftJoin('c.barco','barco')
            ->leftJoin('c.cliente','cliente');

        if ($request->search->value) {
            $q->andWhere('(LOWER(c.folioCompleto) LIKE :search OR ' .
                'LOWER(barco.nombre) LIKE :search OR ' .
                'LOWER(cliente.nombre) LIKE :search OR ' .
                'LOWER(c.cuotaIesps) LIKE :search OR ' .
                'LOWER(c.cantidad) LIKE :search OR ' .
                'LOWER(c.precioVenta) LIKE :search OR ' .
                'LOWER(c.subtotal) LIKE :search OR ' .
                'LOWER(c.ivaTotal) LIKE :search OR ' .
                'LOWER(c.total) LIKE :search OR ' .
                'LOWER(c.fecha) LIKE :search)'
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
                } else if ($column->data == 7) {
                    if ($value) {
                        $q->andWhere('c.validanovo = :validacion')
                            ->setParameter('validacion', $value);
                    } else {
                        $q->andWhere('c.validanovo = 0');
                    }
                } else if ($column->data == 8) {
                    if ($value) {
                        $q->andWhere('c.validacliente = :aceptacion')
                            ->setParameter('aceptacion', $value);
                    } else {
                        $q->andWhere('c.validacliente = 0');
                    }
                } else if ($column->data == 9) {
                    if ($value) {
                        $q->andWhere('c.estatuspago = :pago')->setParameter('pago', $value);
                    } else {
                        $q->andWhere('c.estatuspago IS NULL');
                    }
                }
            }
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('c.folio', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('cliente.nombre', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('barco.nombre', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('c.cantidad', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('c.subtotal', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('c.ivatotal', $order->dir);
            } elseif ($order->column === 6) {
                $q->addOrderBy('c.total', $order->dir);
            } elseif ($order->column === 7) {
                $q->addOrderBy('c.validanovo', $order->dir);
            } elseif ($order->column === 8) {
                $q->addOrderBy('c.validacliente', $order->dir);
            } elseif ($order->column === 9) {
                $q->addOrderBy('c.estatuspago', $order->dir);
            }
        }

        $combustibles = $q->getQuery()->getResult();
        $results->recordsFiltered = count($combustibles);
        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var Combustible $combustible */
            $combustible = $combustibles[$index];
            $results->data[] = [
                $combustible->getFolioCompleto(),
                $combustible->getCliente()->getNombre(),
                $combustible->getBarco()->getNombre(),
                $combustible->getCantidad(),
                '$' . number_format($combustible->getSubtotal() / 100, 2),
                '$' . number_format($combustible->getIvatotal() / 100, 2),
                '$' . number_format($combustible->getTotal() / 100, 2),
                $combustible->getValidanovo(),
                $combustible->getValidacliente(),
                $combustible->getEstatuspago(),
                ['id' => $combustible->getId(), 'estatus' => $combustible->getEstatus()]
            ];
        }
        return $results;
    }
}