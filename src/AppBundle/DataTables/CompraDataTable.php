<?php

namespace AppBundle\DataTables;

use AppBundle\Entity\Compra;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class CompraDataTable extends AbstractDataTableHandler
{

    const ID = 'compra';
    private $doctrine;
//
//    /**
//     * @var Security
//     */
//    private $security;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
//        $this->security = $security;
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
        $repository = $this->doctrine->getRepository(Compra::class);
        $results = new DataTableResults();

        $qb = $repository->createQueryBuilder('c');
//        $views = [];
//        $admin = false;
//        foreach ($this->security->getUser()->getRoles() as $role){
//            if(strpos($role, 'ROLE_ADMIN')===0){
//                $admin = true;
//            }
//            if (strpos($role, 'VIEW_GASTO') === 0) { //busca si la cadena tiene VIEW_GASTO en la posicion cero
//                $views[] = explode('_', $role)[3]; //extrae el id del emisor(empresa) de la cadena
//            }
//        }

        $results->recordsTotal = $qb->select('COUNT(c.id)');
//        if(!$admin){
//            if(count($views) > 0) {
//                $results->recordsTotal = $results->recordsTotal->where($qb->expr()->in('g.empresa', $views));
//            }else{
//                $results->recordsTotal = $results->recordsTotal->where($qb->expr()->eq('g.id',0)); // condicion solo para que no regrese resultados
//            }
//        }
        $results->recordsTotal = $results->recordsTotal->getQuery()->getSingleScalarResult();

        $q = $qb
            ->select('c','solicitud','proveedor')
            ->leftJoin('c.solicitud','solicitud')
            ->leftJoin('c.proveedor','proveedor');

//        if(!$admin){
//            if(count($views) > 0){
//                $q = $q->where($qb->expr()->in('contabilidadFacturacionEmisor.id',$views));
//            }else{
//                $q = $q->where($qb->expr()->eq('g.id',0)); // condicion solo para que no regrese resultados
//            }
//        }

        if($request->search->value){
            $q->andWhere(
                '(c.fecha LIKE :search '.
                ' OR c.folio LIKE :search '.
                ' OR LOWER(proveedor.nombre) LIKE :search '.
                ' OR c.validado LIKE :search '.
                ' OR c.estatus LIKE :search '.
                ' OR c.total LIKE :search '.
                ' OR solicitud.folio LIKE :search '.
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
                    $q->andWhere('LOWER(proveedor.nombre) LIKE :proveedor')
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
                    $q->andWhere('c.validado LIKE :validado')
                        ->setParameter('validado',"%{$value}%");
                } else if($column->data == 8){
                    $q->andWhere('c.estatus LIKE :estatus')
                        ->setParameter('estatus',"%{$value}%");
                }
            }
        }

        foreach ($request->order as $order){
            if ($order->column === 0) {
                $q->addOrderBy('c.fecha', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('c.folio', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('proveedor.nombre', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('c.subtotal', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('c.ivatotal', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('c.total', $order->dir);
            } elseif ($order->column === 6) {
                $q->addOrderBy('c.validado', $order->dir);
            } elseif ($order->column === 7) {
                $q->addOrderBy('c.estatus', $order->dir);
            }
        }

        $compras = $q->getQuery()->getResult();
        $results->recordsFiltered = count($compras);
        for($i = 0; $i < $request->length || $request->length === -1; $i++){
            $index = $i + $request->start;
            if($index >= $results->recordsFiltered){
                break;
            }

            /** @var Compra $compra*/
            $compra = $compras[$index];
            $results->data[] = [
                $compra->getFecha()->format('d/m/Y') ?? '',
                $compra->getFolio(),
                $compra->getProveedor()->getNombre(),
                '$ '.number_format($compra->getSubtotal()/100,2).' <small>MXN</small>',
                '$ '.number_format($compra->getIvatotal()/100,2).' <small>MXN</small>',
                '$ '.number_format($compra->getTotal()/100,2).' <small>MXN</small>',
                $compra->getValidado()?'Validado':'No validado',
                $compra->getEstatus()?'Vigente':'Ya utilizado',
                [$compra->getId(),$compra->getValidado()]
            ];
        }
        return $results;
    }
}