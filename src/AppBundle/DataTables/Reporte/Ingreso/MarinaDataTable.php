<?php
/**
 * User: inrumi
 * Date: 10/2/18
 * Time: 13:20
 */

namespace AppBundle\DataTables\Reporte\Ingreso;


use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\Pago;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class MarinaDataTable extends AbstractDataTableHandler
{
    const ID = 'marina/reporte/ingreso';
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
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $repository = $this->doctrine->getRepository(Pago::class);
        $results = new DataTableResults();

        $inicio = \DateTime::createFromFormat('Y-m-d', $request->customData['dates']['start'])->modify('-1 day');
        $fin = \DateTime::createFromFormat('Y-m-d', $request->customData['dates']['end']);

        $queryBuilder = $repository->createQueryBuilder('pagos')
            ->select('COUNT(pagos.id)')
            ->leftJoin('pagos.mhcotizacion', 'marina')
            ->andWhere(
                'pagos.mhcotizacion IS NOT NULL'
            )/*->andWhere(
                '(:inicio BETWEEN marina.fechaLlegada AND marina.fechaSalida OR '.
                ':fin BETWEEN marina.fechaLlegada AND marina.fechaSalida)'
            )
            ->setParameters(
                [
                    'inicio' => $inicio,
                    'fin' => $fin
                ]
            )*/
        ;

        $results->recordsTotal = $queryBuilder->getQuery()->getSingleScalarResult();

        $queryBuilder = $repository->createQueryBuilder('pagos')
            ->select('pagos', 'marina', 'servicios', 'cliente', 'embarcacion')
            ->leftJoin('pagos.mhcotizacion', 'marina')
            ->innerJoin('marina.mhcservicios', 'servicios')
            ->leftJoin('marina.cliente', 'cliente')
            ->leftJoin('marina.barco', 'embarcacion')
            ->andWhere(
                'pagos.mhcotizacion IS NOT NULL',
                'marina.fecharegistro BETWEEN :inicio AND :fin'
            )
            ->setParameters(
                [
                    'inicio' => $inicio,
                    'fin' => $fin,
                ]
            );

        if ($request->search->value) {
            $queryBuilder->andWhere(
                '(LOWER(marina.folio) LIKE :search OR '.
                'LOWER(cliente.nombre) LIKE :search OR '.
                'LOWER(embarcacion.nombre) LIKE :search)'
            );

            $queryBuilder->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $queryBuilder->addOrderBy('marina.fecharegistro', $order->dir);
            } elseif ($order->column == 1) {
                $queryBuilder->addOrderBy('pagos.fecharealpago', $order->dir);
            } elseif ($order->column == 2) {
                $queryBuilder->addOrderBy('servicios.cantidad', $order->dir);
            } elseif ($order->column == 3) {
                $queryBuilder->addOrderBy('servicios.cantidad', $order->dir);
            } elseif ($order->column == 4) {
                $queryBuilder->addOrderBy('pagos.cantidad', $order->dir);
            }
        }

        $queryCount = clone $queryBuilder;
        $results->recordsFiltered = COUNT($queryCount->select('COUNT(marina.id)')->getQuery()->getResult());

        if ($request->length > 0) {
            $queryBuilder->setMaxResults($request->length);
        }

        $queryBuilder->setFirstResult($request->start);

        /** @var Pago[] $pagos */
        $pagos = $queryBuilder->getQuery()->getResult();

        $acumulaciones = [];

        foreach ($pagos as $pago) {
            $cotizacion = $pago->getMhcotizacion();
            $cliente = $cotizacion->getCliente();
            $embarcacion = $cotizacion->getBarco();
            $servicios = $cotizacion->getMHCservicios();

            if (isset($acumulaciones[$cotizacion->getId()])) {
                $acumulaciones[$cotizacion->getId()] = $acumulaciones[$cotizacion->getId()] + $pago->getCantidad();
            } else {
                $acumulaciones[$cotizacion->getId()] = $pago->getCantidad();
            }

            $results->data[] = [
                [
                    'id' => $cotizacion->getId(),
                    'folio' => $cotizacion->getFolioString(),
                    'fecha' => $cotizacion->getFecharegistro()->format('d/m/Y'),
                    'nombre' => $cliente->getNombre(),
                    'embarcacion' => $embarcacion->getNombre(),
                ],
                [
                    'metodoPago' => $pago->getMetodopago(),
                    'banco' => $pago->getBanco(),
                    'titular' => $pago->getTitular(),
                    'cuenta' => $pago->getNumcuenta(),
                    'seguimiento' => $pago->getCodigoseguimiento(),
                    'fecha' => $pago->getFecharealpago()->format('d/m/Y'),
                ],
                \number_format(($servicios->first()->getTotal() / 100), 2),
                \number_format(($servicios->last()->getTotal() / 100), 2),
                \number_format(($pago->getCantidad() / 100), 2),
                \number_format((($acumulaciones[$cotizacion->getId()] / 1.16) / 100), 2),
                \number_format((($acumulaciones[$cotizacion->getId()] / 1.16) * .16 / 100), 2),
                \number_format(($acumulaciones[$cotizacion->getId()] / 100), 2),
                $cotizacion->getDiasEstadia(),
            ];
        }

        /*
        $query = $repository
            ->createQueryBuilder('mc')
            ->select('COUNT(mc.id)')
            ->where(
                'mc.validacliente = 2 AND '.
                '(:inicio BETWEEN mc.fechaLlegada AND mc.fechaSalida OR '.
                ':fin BETWEEN mc.fechaLlegada AND mc.fechaSalida)')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin);

        $results->recordsTotal = $queryBuilder->getQuery()->getSingleScalarResult();

        $cantidadQuery =
            '(CASE '.
            'WHEN :inicio <= mc.fechaLlegada '.
            'THEN (CASE '.
            'WHEN DATE_DIFF(:fin, :inicio) <> 30 '.
            'THEN DATE_DIFF(:fin, mc.fechaLlegada)'.
            'ELSE (DATE_DIFF(:fin, mc.fechaLlegada) - 1) '.
            'END) '.
            'WHEN :fin >= mc.fechaSalida '.
            'THEN (CASE '.
            'WHEN DATE_DIFF(:fin, :inicio) <> 30 '.
            'THEN DATE_DIFF(mc.fechaSalida, :inicio) '.
            'ELSE DATE_DIFF(mc.fechaSalida, :inicio) '.
            'END) '.
            'ELSE 30 '.
            'END) AS dias_cantidad';

        $query = $repository
            ->createQueryBuilder('mc')
            ->innerJoin('mc.mhcservicios', 's')
            ->leftJoin('mc.barco', 'b')
            ->leftJoin('mc.pagos', 'p')
            ->leftJoin('mc.cliente', 'cliente')
            ->select(
                '(CASE WHEN mc.foliorecotiza <= 0 THEN mc.folio ELSE CONCAT(mc.folio, \'-\', mc.foliorecotiza) END) AS folio ',
                'mc.id AS cotizacion_id',
                'cliente.id AS cliente_id',
                'cliente.nombre AS cliente_nombre',
                'b.nombre AS embarcacion ',
                'MAX(CASE WHEN s.tipo = 1 THEN s.total ELSE 0 END) AS amarre_usd',
                'MAX(CASE WHEN s.tipo = 2 THEN s.total ELSE 0 END) AS servicio_usd',
                'CASE WHEN p.divisa = \'USD\' THEN (p.cantidad / 1.16) ELSE ((p.cantidad / p.dolar) / 1.16) END  AS pago_subtotal',
                'CASE WHEN p.divisa = \'USD\' THEN ((p.cantidad / 1.16) * 0.16) ELSE (((p.cantidad / p.dolar) / 1.16) * 0.16) END AS pago_iva',
                'p.cantidad                                           AS pago_total',
                $cantidadQuery,
                'mc.diasEstadia AS dias',
                'mc.id'
            )
            ->groupBy('mc.id')
            ->where(
                'mc.validacliente = 2 AND '.
                '(:inicio BETWEEN mc.fechaLlegada AND mc.fechaSalida OR '.
                ':fin BETWEEN mc.fechaLlegada AND mc.fechaSalida)')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin);

        if ($request->search->value) {
            $queryBuilder->andWhere(
                '(LOWER(mc.folio) LIKE :search OR '.
                'LOWER(b.nombre) LIKE :search)'
            );
            $queryBuilder->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $queryBuilder->addOrderBy('mc.slip', $order->dir);
            } elseif ($order->column == 1) {
                $queryBuilder->addOrderBy('mc.barco', $order->dir);
            } elseif ($order->column == 2) {
                $queryBuilder->addOrderBy('s.cantidad', $order->dir);
            } elseif ($order->column == 3) {
                $queryBuilder->addOrderBy('s.cantidad', $order->dir);
            } elseif ($order->column == 4) {
                $queryBuilder->addOrderBy('mc.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $results->recordsFiltered = COUNT($queryCount->select('COUNT(mc.id)')->getQuery()->getResult());

        if ($request->length > 0) {
            $queryBuilder->setMaxResults($request->length);
        }

        $queryBuilder->setFirstResult($request->start);

        / ** @var MarinaHumedaCotizacion[] $cotizacions * /
        $cotizacions = $queryBuilder->getQuery()->getResult();

        foreach ($cotizacions as $cotizacion) {
            $results->data[] = [
                [
                    'id' => $cotizacion['cotizacion_id'],
                    'folio' => $cotizacion['folio'],
                ],
                [
                  'id' => $cotizacion['cliente_id'],
                  'nombre' => $cotizacion['cliente_nombre']
                ],
                $cotizacion['embarcacion'],
                '$ '.number_format(($cotizacion['amarre_usd'] / 100), 2),
                '$ '.number_format(($cotizacion['servicio_usd'] / 100), 2),
                '$ '.number_format(($cotizacion['pago_subtotal'] / 100), 2),
                '$ '.number_format(($cotizacion['pago_iva'] / 100), 2),
                '$ '.number_format(($cotizacion['pago_total'] / 100), 2),
                $cotizacion['dias_cantidad'],
            ];
        }
        */

        return $results;
    }
}
