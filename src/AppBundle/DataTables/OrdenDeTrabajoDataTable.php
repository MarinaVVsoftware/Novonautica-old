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
        $q = $qb
            ->select('odt','astilleroCotizacion','barco','cliente')
            ->leftJoin('odt.astilleroCotizacion','astilleroCotizacion')
            ->leftJoin('astilleroCotizacion.barco','barco')
            ->leftJoin('astilleroCotizacion.cliente','cliente')
        ;

        if ($request->search->value) {
            $q->andWhere('(LOWER(astilleroCotizacion.folio) LIKE :search OR ' .
                'LOWER(cliente.nombre) LIKE :search OR ' .
                'LOWER(barco.nombre) LIKE :search OR '.
                'LOWER(astilleroCotizacion.fechaLlegada) LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('astilleroCotizacion.folio', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('astilleroCotizacion.barco', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('astilleroCotizacion.cliente', $order->dir);
            }
        }

        $odts = $q->getQuery()->getResult();
        $results->recordsFiltered = count($odts);
        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var OrdenDeTrabajo $odt */
            $odt = $odts[$index];

            $listaRazonSocial='';
            if($odt->getAstilleroCotizacion()->getRequerirFactura()){
                foreach ($odt->getAstilleroCotizacion()->getCliente()->getRazonesSociales() as $razonSocial){
                    $listaRazonSocial.='<li>'.$razonSocial->getRazonSocial().'</li>';
                }
            }else{
                $listaRazonSocial = 'Público en general';
            }

            $results->data[] = [
                $odt->getAstilleroCotizacion()->getFoliorecotiza() == 0 ? $odt->getAstilleroCotizacion()->getFolio() : $odt->getAstilleroCotizacion()->getFolio().'-'.$odt->getAstilleroCotizacion()->getFoliorecotiza(),
                $odt->getAstilleroCotizacion()->getBarco()->getNombre(),
                $odt->getAstilleroCotizacion()->getCliente()->getNombre(),
                $listaRazonSocial,
                $odt->getAstilleroCotizacion()->getFechaLlegada() ? $odt->getAstilleroCotizacion()->getFechaLlegada()->format('d/m/Y'):'',
                $odt->getAstilleroCotizacion()->getFechaSalida() ? $odt->getAstilleroCotizacion()->getFechaSalida()->format('d/m/Y'):'',
                '$' . number_format($odt->getPrecioTotal() / 100, 2).' MXN',
                '$' . number_format($odt->getUtilidadvvTotal() / 100, 2).' MXN',
                '$' . number_format($odt->getPreciovvTotal() / 100, 2).' MXN',
                '$' . number_format($odt->getIvaTotal() / 100, 2).' MXN',
                '$' . number_format($odt->getGranTotal() / 100, 2).' MXN',
                $odt->getId(),
                $odt->getAstilleroCotizacion()->getRequerirFactura()
            ];
        }

        return $results;

    }
}