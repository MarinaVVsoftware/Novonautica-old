<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 30/11/2018
 * Time: 01:11 PM
 */

namespace AppBundle\DataTables\Proveedor;

use AppBundle\Entity\Astillero\Proveedor\Trabajo;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class TrabajoDataTable extends AbstractDataTableHandler
{
    const ID = 'ProveedorTrabajo';
    private $doctrine;

    public function __construct(ManagerRegistry $registry)
    {
        $this->doctrine = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $proveedorTrabajoRepo = $this->doctrine->getRepository('AppBundle:Astillero\Proveedor\Trabajo');
        $results = new DataTableResults();

        $qb = $proveedorTrabajoRepo->createQueryBuilder('t');
        $results->recordsTotal = $qb->select('COUNT(t.id)')->getQuery()->getSingleScalarResult();

        $q = $qb->select('t');

        if($request->search->value){
            $q->where('(LOWER(t.nombre) LIKE :search)')
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order){
            if($order->column === 0){
                $q->addOrderBy('t.nombre',$order->dir);
            }
        }

        $trabajos = $q->getQuery()->getResult();
        $results->recordsFiltered = count($trabajos);
        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;
            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var Trabajo $trabajo */
            $trabajo = $trabajos[$index];

            $results->data[] = [
                $trabajo->getNombre(),
                $trabajo->getId()
            ];
        }

        return $results;
    }
}