<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/11/18
 * Time: 13:41
 */

namespace AppBundle\DataTables;


use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class MarinaReporteDataTable extends AbstractDataTableHandler
{
    const ID = 'marinaReporte';
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
        $repository = $this->doctrine->getRepository('AppBundle:MarinaHumedaCotizacion');
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('mc')
            ->select('COUNT(mc.cliente)')
            ->where('mc.validacliente = 2 AND mc.estatuspago IS NULL OR mc.estatuspago <> 2');

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $pagoRepository = $this->doctrine->getRepository('AppBundle:Pago');

        $adeudoSubquery = $pagoRepository->createQueryBuilder('pa')
            ->select('SUM(pa.cantidad)')
            ->where('pa.acotizacion = mc.id')
            ->groupBy('pa.acotizacion');

        $fechaSubquery = $pagoRepository->createQueryBuilder('pf')
            ->select('MAX(pf.fecharealpago)')
            ->where('pf.acotizacion = mc.id');

        $query = $repository->createQueryBuilder('mc')
            ->select('CASE WHEN  mc.foliorecotiza = 0 THEN mc.folio ' .
                'ELSE CONCAT(mc.folio, \'-\', mc.foliorecotiza) END AS folio')
            ->addSelect('c.id AS id_cliente', 'mc.id AS id_cotizacion')
            ->addSelect('c.nombre', 'mc.fechaLlegada', 'mc.fechaSalida', 'mc.total')
            ->addSelect("({$adeudoSubquery->getDQL()}) AS pagado")
            ->addSelect('COALESCE(p.fecharealpago, \'No se han realizado pagos\') AS lastPago')
            ->leftJoin('mc.cliente', 'c')
            ->leftJoin('mc.pagos', 'p')
            ->where("p.fecharealpago = ({$fechaSubquery->getDQL()})" .
                " AND mc.estatuspago = 1 OR mc.estatuspago IS NULL AND mc.validacliente = 2"
            );

        if ($request->search->value) {
            $query->where('(LOWER(c.nombre) LIKE :search)');
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('ac.folio', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('c.nombre', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(mc.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        $reportes = $query->getQuery()->getResult();

        foreach ($reportes as $reporte) {
            $results->data[] = [
                $reporte['folio'],
                $reporte['nombre'],
                $reporte['fechaLlegada']->format('Y-m-d H:i:s'),
                $reporte['fechaSalida']->format('Y-m-d H:i:s'),
                $reporte['lastPago'],
                '$' . number_format(($reporte['total'] / 100), 2),
                '$' . number_format(($reporte['pagado'] / 100), 2),
                [
                    'cliente' => $reporte['id_cliente'],
                    'cotizacion' => $reporte['id_cotizacion']
                ],
            ];
        }

        return $results;
    }
}