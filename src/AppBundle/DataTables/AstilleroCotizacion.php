<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 21/02/2018
 * Time: 05:44 PM
 */

namespace AppBundle\DataTables;


use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class AstilleroCotizacion extends AbstractDataTableHandler
{
    const ID = 'astilleroCotizacion';
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
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $acRepo = $this->doctrine->getRepository('AppBundle:AstilleroCotizacion');
        $results = new DataTableResults();
        $qb = $acRepo->createQueryBuilder('ac');
        $results->recordsTotal = $qb->select('COUNT(ac.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $q = $qb
            ->select('ac', 'ba', 'cl')
            ->leftJoin('ac.barco', 'ba')
            ->leftJoin('ac.cliente', 'cl')
        ;

        if ($request->search->value) {
            $q->where('(LOWER(ba.nombre) LIKE :search OR ' .
                'LOWER(cl.nombre) LIKE :search OR '
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('ba.nombre', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('cl.nombre', $order->dir);
            }
        }
        $acotizaciones = $q->getQuery()->getResult();
        $results->recordsFiltered = count($acotizaciones);
        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var AstilleroCotizacion $acotizacion */
            $acotizacion = $acotizaciones[$index];
//            $barcos = $acotizacion->getBarcos()
//                ->map(function ($barco) { return [$barco->getId(), $barco->getNombre()]; })
//                ->toArray();

            $results->data[] = [
                $acotizacion->getCliente(),
                $acotizacion->getBarco(),
                $acotizacion->getId()
            ];
        }
        return $results;
    }
}