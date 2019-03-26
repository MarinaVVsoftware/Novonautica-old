<?php
/**
 * User: inrumi
 * Date: 10/2/18
 * Time: 13:20
 */

namespace AppBundle\DataTables\Reporte\Ingreso;


use AppBundle\Entity\MarinaHumedaCotizacion;
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
        $repository = $this->doctrine->getRepository(MarinaHumedaCotizacion::class);
        $results = new DataTableResults();

        $inicio = \DateTime::createFromFormat('Y-m-d', $request->customData['dates']['start'])->modify('-1 day');
        $fin = \DateTime::createFromFormat('Y-m-d', $request->customData['dates']['end']);

        $query = $repository->createQueryBuilder('mc');
        $query->select('COUNT(mc.id)');

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        // Ahora mostraremos los dias de la cotizacion en vez de hacer un calculo que se adecue
        // a el formato de reportes de excel
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

        $query = $repository->createQueryBuilder('cotizacion');

        $query
            ->select(
                'cotizacion',
                'servicio',
                'embarcacion',
                'cliente'
            )
            ->leftJoin('cotizacion.mhcservicios', 'servicio')
            ->leftJoin('cotizacion.barco', 'embarcacion')
            ->leftJoin('cotizacion.cliente', 'cliente')
            ->andWhere(
                'cotizacion.fecharegistro BETWEEN :inicio AND :fin',
                'cotizacion.validacliente = 2'
            )
            ->setParameters(
                [
                    'inicio' => $inicio,
                    'fin' => $fin,
                ]
            );

        if ($request->search->value) {
            $query
                ->andWhere(
                    '(LOWER(cotizacion.folio) LIKE :search OR '.
                    'LOWER(embarcacion.nombre) LIKE :search)'
                )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query
                    ->addOrderBy('cotizacion.fecharegistro', $order->dir)
                    ->addOrderBy('cotizacion.folio', $order->dir);
            }
            if ($order->column == 1) {
                $query
                    ->addOrderBy('embarcacion.nombre', $order->dir);
            }
            if ($order->column == 2) {
                $query
                    ->addOrderBy('servicio.cantidad', $order->dir);
            }
            if ($order->column == 3) {
                $query
                    ->addOrderBy('servicio.cantidad', $order->dir);
            }
            if ($order->column == 4) {
                $query
                    ->addOrderBy('cotizacion.subtotal', $order->dir);
            }
            if ($order->column == 5) {
                $query
                    ->addOrderBy('cotizacion.ivatotal', $order->dir);
            }
            if ($order->column == 6) {
                $query
                    ->addOrderBy('cotizacion.total', $order->dir);
            }
            if ($order->column == 7) {
                $query
                    ->addOrderBy('cotizacion.pagado', $order->dir);
            }
            if ($order->column == 7) {
                $query
                    ->addOrderBy('cotizacion.diasEstadia', $order->dir);
            }
        }

        $queryCount = clone $query;
        $results->recordsFiltered = $queryCount->select('COUNT(cotizacion.id)')->getQuery()->getSingleScalarResult();

        /** @var MarinaHumedaCotizacion[] $cotizaciones */
        $cotizaciones = $query->getQuery()->getResult();

        foreach ($cotizaciones as $cotizacion) {
            $cliente = $cotizacion->getCliente();
            $embarcacion = $cotizacion->getBarco();
            $servicios = $cotizacion->getMHCservicios();

            $amarre = $servicios[0];
            $electricidad = $servicios[1];

            $valorDolar = $cotizacion->getDolar() / 100;

            $results->data[] = [
                [
                    'id' => $cotizacion->getId(),
                    'folio' => $cotizacion->getFolioString(),
                ],
                [
                    'id' => $cliente->getId(),
                    'embarcacion' => $embarcacion->getNombre(),
                ],
                ($amarre->getTotal() / 100) * $valorDolar,
                ($electricidad->getTotal() / 100) * $valorDolar,
                ($cotizacion->getSubtotal() / 100) * $valorDolar,
                ($cotizacion->getIvatotal() / 100) * $valorDolar,
                ($cotizacion->getTotal() / 100) * $valorDolar,
                ($cotizacion->getPagado() / 100) * $valorDolar,
                $cotizacion->getDiasEstadia(),
            ];
        }

        return $results;
    }
}
