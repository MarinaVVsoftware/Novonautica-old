<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 30/10/2018
 * Time: 05:55 PM
 */

namespace AppBundle\DataTables\Marina;


use AppBundle\Entity\MarinaHumedaServicio;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ServicioDataTable extends AbstractDataTableHandler
{
    CONST ID = 'MarinaServicioAdicional';
    private $doctrine;

    public function __construct(ManagerRegistry $registry)
    {
        $this->doctrine = $registry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $astilleroProductoRepo = $this->doctrine->getRepository('AppBundle:MarinaHumedaServicio');
        $results = new DataTableResults();

        $qb = $astilleroProductoRepo->createQueryBuilder('s');
        $results->recordsTotal = $qb->select('COUNT(s.id)')->getQuery()->getSingleScalarResult();

        $q = $qb->select('s','ClaveProdServ','ClaveUnidad')
            ->leftJoin('s.claveProdServ','ClaveProdServ')
            ->leftJoin('s.claveUnidad','ClaveUnidad');

        if ($request->search->value) {
            $q->where('(LOWER(s.nombre) LIKE :search '.
                ' OR LOWER(s.unidad) LIKE :search '.
                ' OR LOWER(s.precio) LIKE :search '.
                ' OR ClaveProdServ.claveProdServ LIKE :search' .
                ' OR ClaveUnidad.claveUnidad LIKE :search' .
                ')'
            );
            $q->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('s.nombre', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('s.unidad', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('s.precio', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('ap.existencia', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('ClaveProdServ.claveProdServ', $order->dir);
            } elseif ($order->column === 5) {
                $q->addOrderBy('ClaveUnidad.claveUnidad', $order->dir);
            }
        }

        $productos = $q->getQuery()->getResult();

        $results->recordsFiltered = count($productos);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var MarinaHumedaServicio $producto */
            $producto = $productos[$index];

            $results->data[] = [
                $producto->getNombre(),
                $producto->getUnidad(),
                '$'.number_format($producto->getPrecio() / 100, 2).' MXN',
                $producto->getExistencia()?$producto->getExistencia():'0',
                $producto->getClaveProdServ()?$producto->getClaveProdServ()->getClaveProdServ():'',
                $producto->getClaveUnidad()?$producto->getClaveUnidad()->getClaveUnidad():'',
                $producto->getId(),
            ];
        }

        return $results;
    }
}