<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 21/03/2018
 * Time: 03:15 PM
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\OrdenDeTrabajo;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class OrdenDeTrabajoDataTable extends AbstractDataTableHandler
{
    CONST ID = 'ODT';
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
        $odtRepo = $this->doctrine->getRepository('AppBundle:OrdenDeTrabajo');
        $results = new DataTableResults();

        $qb = $odtRepo->createQueryBuilder('odt');
        $results->recordsTotal = $qb->select('COUNT(odt.id)')->getQuery()->getSingleScalarResult();
        $q = $qb->select('odt');


//        foreach ($request->order as $order) {
//            if ($order->column === 0) {
//                $q->addOrderBy('odt.astilleroCotizacion.folio', $order->dir);
//            } elseif ($order->column === 1) {
//                $q->addOrderBy('odt.astilleroCotizacion.barco', $order->dir);
//            } elseif ($order->column === 2) {
//                $q->addOrderBy('odt.astilleroCotizacion.cliente', $order->dir);
//            }
//        }

        $odts = $q->getQuery()->getResult();
        $results->recordsFiltered = count($odts);
        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var OrdenDeTrabajo $odt */
            $odt = $odts[$index];

            $results->data[] = [
                $odt->getAstilleroCotizacion()->getFolio(),
                $odt->getAstilleroCotizacion()->getBarco()->getNombre(),
                $odt->getAstilleroCotizacion()->getCliente()->getNombre(),
                $odt->getPrecioTotal(),
                $odt->getMaterialesTotal(),
                $odt->getPagosTotal(),
                $odt->getSaldoTotal(),
                $odt->getPreciovvTotal(),
                $odt->getUtilidadvvTotal(),
                $odt->getIvaTotal(),
                $odt->getGranTotal(),
                $odt->getId()
            ];
        }

        return $results;

    }
}