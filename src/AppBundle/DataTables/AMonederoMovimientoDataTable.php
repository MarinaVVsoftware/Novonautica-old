<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 27/08/2018
 * Time: 05:58 PM
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\MonederoMovimiento;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class AMonederoMovimientoDataTable extends AbstractDataTableHandler
{
    CONST ID = 'AMonederoMovimiento';
    private $doctrine;

    public function __construct(ManagerRegistry $registry)
    {
        $this->doctrine = $registry;
    }

    /**
     * Handles specified DataTable request.
     * {@inheritdoc}
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $monederoMovimientoRepo = $this->doctrine->getRepository('AppBundle:MonederoMovimiento');
        $results = new DataTableResults();

        $qb = $monederoMovimientoRepo->createQueryBuilder('mm');
        $results->recordsTotal = $qb
            ->select('COUNT(mm.id)')
            ->where('mm.cliente = '.$request->customData['idcliente'])
            ->andWhere('mm.tipo = 2')
            ->getQuery()->getSingleScalarResult();
        $q = $qb->select('mm','cliente')
            ->join('mm.cliente', 'cliente')
            ->where('cliente.id = '.$request->customData['idcliente'])
            ->andWhere('mm.tipo = 2');

        if ($request->search->value) {
            $q->where('(LOWER(mm.descripcion) LIKE :search OR ' . 'mm.fecha LIKE :search OR mm.monto LIKE :search OR mm.resultante LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $q->addOrderBy('mm.id', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('mm.fecha', $order->dir);
            }elseif ($order->column === 2) {
                $q->addOrderBy('mm.monto', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('mm.operacion', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('mm.resultante', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('mm.descripcion', $order->dir);
            }
        }

        $monederoMovimientos = $q->getQuery()->getResult();

        $results->recordsFiltered = count($monederoMovimientos);

        for($i = 0; $i < $request->length || $request->length === -1; $i++){
            $index = $i + $request->start;

            if($index >= $results->recordsFiltered){
                break;
            }

            /** @var MonederoMovimiento $monederoMovimiento */
            $monederoMovimiento = $monederoMovimientos[$index];

            $results->data[] = [
                $monederoMovimiento->getId(),
                $monederoMovimiento->getFecha() ? $monederoMovimiento->getFecha()->format('d/m/Y h:i a') : '',
                '$'.number_format($monederoMovimiento->getMonto()/100,2).' MXN',
                $monederoMovimiento->getOperacion() == 1 ? 'SUMA': 'RESTA',
                '$'.number_format($monederoMovimiento->getResultante()/100,2).' MXN',
                $monederoMovimiento->getDescripcion()
            ];
        }
        return $results;
    }
}