<?php
/**
 * User: inrumi
 * Date: 8/15/18
 * Time: 15:20
 */

namespace AppBundle\DataTables\Cliente;


use AppBundle\Entity\Pago;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class CuentaAbonosDataTable extends AbstractDataTableHandler
{
    const ID = 'clienteReporteAbonos';
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

        $cliente = $request->customData['cliente'];

        $query = $repository->createQueryBuilder('p')->select('COUNT(p.id), SUM(p.cantidad)')
            ->leftJoin('p.mhcotizacion', 'marina')
            ->leftJoin('p.acotizacion', 'astillero')
            ->leftJoin('p.combustible', 'combustible')
            ->andWhere('marina.cliente = :cliente OR astillero.cliente = :cliente OR combustible.cliente = :cliente')
            ->setParameter('cliente', $cliente);

        $queryData = $query->getQuery()->getSingleResult();
        $results->recordsTotal = $queryData[1];

        $query = $repository->createQueryBuilder('p')
            ->leftJoin('p.mhcotizacion', 'marina')
            ->leftJoin('p.acotizacion', 'astillero')
            ->leftJoin('p.combustible', 'combustible')
            ->andWhere('marina.cliente = :cliente OR astillero.cliente = :cliente OR combustible.cliente = :cliente')
            ->setParameter('cliente', $cliente);

        if ($request->search->value) {
            $query->where('(LOWER(marina.folio) LIKE :search OR '.
                'LOWER(astillero.folio) LIKE :search OR '.
                'LOWER(combustible.folio) LIKE :search OR '.
                'LOWER(p.cantidad) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('p.fecharealpago', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('p.cantidad', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('p.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(p.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Pago[] $pagos */
        $pagos = $query->getQuery()->getResult();

        foreach ($pagos as $pago) {
            $cotizacion = $pago->getAcotizacion() ?? $pago->getMhcotizacion() ?? $pago->getCombustible();

            $cantidad = $pago->getDivisa() === 'USD'
                ?
                : ($pago->getCantidad() / 100);

            $results->data[] = [
                $pago->getFecharealpago()->format('d-m-Y'),
                'MX $'.number_format($cantidad, 2),
//                'Cotizacion '.$cotizacion->getKind().' #'.$cotizacion->getFolioString(),
                $cotizacion->getId(),
                [
                    'id' => $pago->getId(),
                    'total' => $queryData[2],
                ],
            ];
        }

        return $results;
    }
}
