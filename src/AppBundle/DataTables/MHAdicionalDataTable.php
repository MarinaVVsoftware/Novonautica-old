<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 29/08/2018
 * Time: 01:34 PM
 */

namespace AppBundle\DataTables;

use AppBundle\Entity\MarinaHumedaCotizacionAdicional;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class MHAdicionalDataTable extends AbstractDataTableHandler
{
    const ID = 'cotizacionMarinaAdicional';
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
     *
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $mcaRepo = $this->doctrine->getRepository('AppBundle:MarinaHumedaCotizacionAdicional');
        $results = new DataTableResults();

        $qb = $mcaRepo->createQueryBuilder('mca');
        $results->recordsTotal = $qb->select('COUNT(mca.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $q = $qb
            ->select('mca','barco','cliente')
            ->leftJoin('mca.barco','barco')
            ->leftJoin('mca.cliente','cliente');

        if($request->search->value){
            $q->where('(LOWER(mca.id) LIKE :search OR ' .
                'LOWER(cliente.nombre) LIKE :search OR ' .
                'LOWER(barco.nombre) LIKE :search OR ' .
                'LOWER(mca.subtotal) LIKE :search OR ' .
                'LOWER(mca.ivatotal) LIKE :search OR ' .
                'LOWER(mca.total) LIKE :search)'
            )->setParameter('search', strtolower("%{$request->search->value}%"));
        }
        foreach ($request->columns as $column) {
            if($column->search->value){
                $value = $column->search->value === 'null' ? null : strtolower($column->search->value);
                if ($column->data == 1) {
                    $q->andWhere('LOWER(mca.id) LIKE :id')
                        ->setParameter('cliente', "%{$value}%");
                } else if ($column->data == 2) {
                    $q->andWhere('LOWER(cliente.nombre) LIKE :cliente')
                        ->setParameter('cliente', "%{$value}%");
                } else if ($column->data == 3) {
                    $q->andWhere('LOWER(barco.nombre) LIKE :barco')
                        ->setParameter('barco', "%{$value}%");
                }
            }
        }
        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('mca.id', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('cliente.nombre', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('barco.nombre', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('mca.subtotal', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('mca.ivatotal', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('mca.total', $order->dir);
            }
        }
        $adicionales = $q->getQuery()->getResult();
        $results->recordsFiltered = count($adicionales);
        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;
            if ($index >= $results->recordsFiltered) { break; }

            /** @var MarinaHumedaCotizacionAdicional $adicional */
            $adicional = $adicionales[$index];
            $results->data[] = [
                $adicional->getId(),
                $adicional->getCliente()->getNombre(),
                $adicional->getBarco()->getNombre(),
                '$ '.number_format($adicional->getSubtotal()/100,2).' USD',
                '$ '.number_format($adicional->getIvatotal()/100,2).' USD',
                '$ '.number_format($adicional->getTotal()/100,2).' USD',
                $adicional->getId()
            ];
        }
        return $results;
    }
}