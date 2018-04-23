<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 16/03/2018
 * Time: 04:02 PM
 */

namespace AppBundle\DataTables;
use AppBundle\Entity\Astillero\Servicio;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;


class AstilleroServicioDataTable extends AbstractDataTableHandler
{
    CONST ID = 'AstilleroServicio';
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
        $astilleroServicioRepo = $this->doctrine->getRepository('AppBundle:Astillero\Servicio');
        $results = new DataTableResults();

        $qb = $astilleroServicioRepo->createQueryBuilder('ats');
        $results->recordsTotal = $qb->select('COUNT(ats.id)')->getQuery()->getSingleScalarResult();

        $q = $qb->select('ats');

        if ($request->search->value) {
            $q->where('(LOWER(ats.nombre) LIKE :search OR ' .
                'ats.precio LIKE :search  OR ats.unidad LIKE :search OR ats.descripcion LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('ats.nombre', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('ats.precio', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('ats.unidad', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('ats.descripcion', $order->dir);
            }
        }

        $servicios = $q->getQuery()->getResult();

        $results->recordsFiltered = count($servicios);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var Servicio $servicio */
            $servicio = $servicios[$index];

            $results->data[] = [
                $servicio->getNombre(),
                '$' . number_format($servicio->getPrecio() / 100, 2).' MXN',
                $servicio->getUnidad(),
                $servicio->getDescripcion(),
                $servicio->getId()
            ];
        }

        return $results;
    }
}