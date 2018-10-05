<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 11/10/2018
 * Time: 05:58 PM
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Gasto;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class GastoDataTable extends AbstractDataTableHandler
{

    const ID = 'gasto';
    private $doctrine;

    /**
     * @var Security
     */
    private $security;

    public function __construct(ManagerRegistry $doctrine, Security $security)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
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
        $repository = $this->doctrine->getRepository(Gasto::class);
        $results = new DataTableResults();

        $qb = $repository->createQueryBuilder('g');
        $views = [];
        $admin = false;
        foreach ($this->security->getUser()->getRoles() as $role){
            if(strpos($role, 'ROLE_ADMIN')===0){
                $admin = true;
            }
            if (strpos($role, 'VIEW_GASTO') === 0) { //busca si la cadena tiene VIEW_GASTO en la posicion cero
                $views[] = explode('_', $role)[3]; //extrae el id del emisor(empresa) de la cadena
            }
        }

        $results->recordsTotal = $qb->select('COUNT(g.id)');
        if(!$admin){
            if(count($views) > 0) {
                $results->recordsTotal = $results->recordsTotal->where($qb->expr()->in('g.empresa', $views));
            }else{
                $results->recordsTotal = $results->recordsTotal->where($qb->expr()->eq('g.id',0)); // condicion solo para que no regrese resultados
            }
        }
        $results->recordsTotal = $results->recordsTotal->getQuery()->getSingleScalarResult();

        $q = $qb
            ->select('g','contabilidadFacturacionEmisor')
            ->leftJoin('g.empresa','contabilidadFacturacionEmisor');

        if(!$admin){
            if(count($views) > 0){
                $q = $q->where($qb->expr()->in('contabilidadFacturacionEmisor.id',$views));
            }else{
                $q = $q->where($qb->expr()->eq('g.id',0)); // condicion solo para que no regrese resultados
            }
        }

        if($request->search->value){
            $q->andWhere(
                '(LOWER(g.fecha) LIKE :search OR '.
                'LOWER(contabilidadFacturacionEmisor.nombre) LIKE :search '.
                ')')
                ->setParameter('search',strtolower("%{$request->search->value}%"));
        }

        foreach ($request->columns as $column) {
            if($column->search->value){
                $value = $column->search->value === 'null' ? null : strtolower($column->search->value);
                if($column->data == 1){
                    $q->andWhere('LOWER(contabilidadFacturacionEmisor.nombre) LIKE :empresa')
                        ->setParameter('empresa',"%{$value}%");
                } else if($column->data == 2){
                    $q->andWhere('LOWER(g.total) LIKE :total')
                        ->setParameter('total',"%{$value}%");
                } else if($column->data == 3){
                    $q->andWhere('LOWER(g.fecha) LIKE :fecha')
                        ->setParameter('fecha',"%{$value}%");
                }
            }
        }

        foreach ($request->order as $order){
            if ($order->column === 0) {
                $q->addOrderBy('contabilidadFacturacionEmisor.nombre', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('g.total', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('g.fecha', $order->dir);
            }
        }

        $gastos = $q->getQuery()->getResult();
        $results->recordsFiltered = count($gastos);
        for($i = 0; $i < $request->length || $request->length === -1; $i++){
            $index = $i + $request->start;
            if($index >= $results->recordsFiltered){
                break;
            }

            /** @var Gasto $gasto */
            $gasto = $gastos[$index];
            $results->data[] = [
                $gasto->getEmpresa()->getNombre().' - '.$results->recordsTotal,
                '$ '.number_format($gasto->getTotal()/100,2).' <small>MXN</small>',
                $gasto->getFecha()->format('d/m/Y') ?? '',
                $gasto->getId()
            ];
        }
        return $results;
    }
}