<?php

namespace AppBundle\DataTables;


use AppBundle\Entity\Solicitud;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class SolicitudDataTable extends AbstractDataTableHandler
{

    const ID = 'solicitud';
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
        $repository = $this->doctrine->getRepository(Solicitud::class);
        $results = new DataTableResults();

        $qb = $repository->createQueryBuilder('s');
        $views = [];
        $admin = false;
        foreach ($this->security->getUser()->getRoles() as $role){
            if(strpos($role, 'ROLE_ADMIN')===0){
                $admin = true;
            }
            if (strpos($role, 'VIEW_SOLICITUD') === 0) { //busca si la cadena tiene VIEW_SOLICITUD en la posicion cero
                $views[] = explode('_', $role)[3]; //extrae el id del emisor(empresa) de la cadena
            }
        }

        $results->recordsTotal = $qb->select('COUNT(s.id)');
        if(!$admin){
            if(count($views) > 0) {
                $results->recordsTotal = $results->recordsTotal->where($qb->expr()->in('s.empresa', $views));
            }else{
                $results->recordsTotal = $results->recordsTotal->where($qb->expr()->eq('s.id',0)); // condicion solo para que no regrese resultados
            }
        }
        $results->recordsTotal = $results->recordsTotal->getQuery()->getSingleScalarResult();

        $q = $qb
            ->select('s','contabilidadFacturacionEmisor')
            ->leftJoin('s.empresa','contabilidadFacturacionEmisor');

        if(!$admin){
            if(count($views) > 0){
                $q = $q->where($qb->expr()->in('contabilidadFacturacionEmisor.id',$views));
            }else{
                $q = $q->where($qb->expr()->eq('s.id',0)); // condicion solo para que no regrese resultados
            }
        }

        if($request->search->value){
            $q->andWhere(
                '(LOWER(s.fecha) LIKE :search '.
                ' OR s.folio LIKE :search '.
                ' OR LOWER(contabilidadFacturacionEmisor.nombre) LIKE :search '.
                ' OR s.validadoCompra LIKE :search '.
                ')')
                ->setParameter('search',strtolower("%{$request->search->value}%"));
        }

        foreach ($request->columns as $column) {
            if($column->search->value){
                $value = $column->search->value === 'null' ? null : strtolower($column->search->value);
                if($column->data == 1){
                    $q->andWhere('s.folio LIKE :folio')
                        ->setParameter('folio',"%{$value}%");
                } else if($column->data == 2){
                    $q->andWhere('LOWER(contabilidadFacturacionEmisor.nombre) LIKE :empresa')
                        ->setParameter('empresa',"%{$value}%");
                } else if($column->data == 3){
                    $q->andWhere('s.fecha LIKE :fecha')
                        ->setParameter('fecha',"%{$value}%");
                } else if($column->data == 4){
                    $q->andWhere('s.validadoCompra LIKE :validadoCompra')
                        ->setParameter('validado',"%{$value}%");
                }
            }
        }

        foreach ($request->order as $order){
            if ($order->column === 0) {
                $q->addOrderBy('s.folio', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('contabilidadFacturacionEmisor.nombre', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('s.fecha', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('s.validadoCompra', $order->dir);
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
            $results->data[] = [
                $solicitud->getFolio(),
                $solicitud->getEmpresa()->getNombre(),
                $solicitud->getFecha()->format('d/m/Y') ?? '',
                $solicitud->getValidadoCompra()?'Validado':'No validado',
                [$solicitud->getId(),$solicitud->getValidadoCompra()]
            ];
        }
        return $results;
    }
}