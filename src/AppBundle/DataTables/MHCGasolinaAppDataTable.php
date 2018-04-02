<?php

namespace AppBundle\DataTables;


use AppBundle\Entity\MarinaHumedaSolicitudGasolina;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class MHCGasolinaAppDataTable extends AbstractDataTableHandler
{
    const ID = 'appgasolina';
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
        $repository = $this->doctrine->getRepository('AppBundle:MarinaHumedaSolicitudGasolina');

        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('sg')->select('COUNT(sg.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('sg')
        ->select('sg', 'nbarco', 'ncliente')
        ->leftJoin('sg.cliente', 'ncliente')
        ->leftJoin('sg.idbarco', 'nbarco')
        ;

        if ($request->search->value) {
            $query->where('(LOWER(ncliente.nombre) LIKE :search OR ' .
                'LOWER(sg.tipo_combustible) LIKE :search OR ' .
                'LOWER(sg.id) LIKE :search OR ' .
                'LOWER(nbarco.nombre) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
                if ($order->column == 0) {
                $query->addOrderBy('ncliente.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('nbarco.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('sg.fechaPeticion', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('sg.cantidadCombustible', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('sg.tipo_combustible', $order->dir);
            }elseif ($order->column == 5) {
                $query->addOrderBy('sg.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(sg.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var MarinaHumedaSolicitudGasolina[] $solicitudes */
        $solicitudes = $query->getQuery()->getResult();

        foreach ($solicitudes as $solicitud) {
            $results->data[] = [
                $solicitud->getCliente()->getNombre(),
                $solicitud->getIdbarco()->getNombre(),
                $solicitud->getFechaPeticion()->format('d/m/Y H:i a'),
                $solicitud->getCantidadCombustible(),
                ($solicitud->getTipoCombustible() == 3) ? "Magna" : ($solicitud->getTipoCombustible() == 4 ? "Premium" : "Diesel"),
                $solicitud->getId()
            ];
        }

        return $results;
    }
}