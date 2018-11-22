<?php

namespace AppBundle\DataTables;

use AppBundle\Entity\Solicitud;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class CompraDataTable extends AbstractDataTableHandler
{

    const ID = 'compra';
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
        $repository = $this->doctrine->getRepository(Solicitud::class);
        $results = new DataTableResults();

        $qb = $repository->createQueryBuilder('c');

        $results->recordsTotal = $qb->select('COUNT(c.id)');
        $results->recordsTotal = $results->recordsTotal->getQuery()->getSingleScalarResult();

        $q = $qb
            ->select('c','contabilidadFacturacionEmisor')
            ->leftJoin('c.empresa','contabilidadFacturacionEmisor');

        if($request->search->value){
            $q->andWhere(
                '(c.fecha LIKE :search '.
                ' OR c.folio LIKE :search '.
                ' OR LOWER(contabilidadFacturacionEmisor.nombre) LIKE :search '.
                ' OR c.fechaValidadoCompra LIKE :search '.
                ' OR c.total LIKE :search '.
                ')')
                ->setParameter('search',strtolower("%{$request->search->value}%"));
        }

        foreach ($request->columns as $column) {
            if($column->search->value){
                $value = $column->search->value === 'null' ? null : strtolower($column->search->value);
                if($column->data == 1){
                    $q->andWhere('c.fecha LIKE :fecha')
                        ->setParameter('fecha',"%{$value}%");
                } else if($column->data == 2){
                    $q->andWhere('c.folio LIKE :folio')
                        ->setParameter('folio',"%{$value}%");
                } else if($column->data == 3){
                    $q->andWhere('LOWER(contabilidadFacturacionEmisor.nombre) LIKE :emrpesa')
                        ->setParameter('proveedor',"%{$value}%");
                } else if($column->data == 4){
                    $q->andWhere('c.subtotal LIKE :subtotal')
                        ->setParameter('subtotal',"%{$value}%");
                } else if($column->data == 5){
                    $q->andWhere('c.ivatotal LIKE :ivatotal')
                        ->setParameter('ivatotal',"%{$value}%");
                } else if($column->data == 6){
                    $q->andWhere('c.total LIKE :total')
                        ->setParameter('total',"%{$value}%");
                } else if($column->data == 7){
                    $q->andWhere('c.fechaValidadoCompra LIKE :fechavalidadocompra')
                        ->setParameter('fechavalidadocompra',"%{$value}%");
                }
            }
        }

        foreach ($request->order as $order){
            if ($order->column === 0) {
                $q->addOrderBy('c.fecha', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('c.folio', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('contabilidadFacturacionEmisor.nombre', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('c.subtotal', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('c.ivatotal', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('c.total', $order->dir);
            } elseif ($order->column === 6) {
                $q->addOrderBy('c.fechaValidadoCompra', $order->dir);
            }
        }

        $solicitudes = $q->getQuery()->getResult();
        $results->recordsFiltered = count($solicitudes);
        for($i = 0; $i < $request->length || $request->length === -1; $i++){
            $index = $i + $request->start;
            if($index >= $results->recordsFiltered){
                break;
            }

            /** @var Solicitud $solicitud */
            $solicitud = $solicitudes[$index];
            $estatus = '';
            if($solicitud->getValidadoCompra()){
                $estatus = $solicitud->getFechaValidoCompra()->format('d/m/Y');
            }elseif($solicitud->getValidadoCompra() === false){
                $estatus = 'Rechazado';
            }else{
                $estatus = 'Pendiente';
            }

            $results->data[] = [
                $solicitud->getFecha()->format('d/m/Y') ?? '',
                $solicitud->getFolio(),
                $solicitud->getEmpresa()->getNombre(),
                '$ '.number_format($solicitud->getSubtotal()/100,2).' <small>MXN</small>',
                '$ '.number_format($solicitud->getIvatotal()/100,2).' <small>MXN</small>',
                '$ '.number_format($solicitud->getTotal()/100,2).' <small>MXN</small>',
                $estatus,
                [$solicitud->getId(),$solicitud->getValidadoCompra()]
            ];
        }
        return $results;
    }
}