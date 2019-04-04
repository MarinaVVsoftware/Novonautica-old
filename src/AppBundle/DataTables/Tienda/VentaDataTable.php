<?php
/**
 * User: inrumi
 * Date: 7/3/18
 * Time: 12:54
 */

namespace AppBundle\DataTables\Tienda;


use AppBundle\Entity\Tienda\Venta;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class VentaDataTable extends AbstractDataTableHandler
{
    const ID = 'venta';
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
        $repository = $this->doctrine->getRepository(Venta::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('v')->select('COUNT(v.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('v');

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('v.id', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('v.cliente', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('v.tipoVenta', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('v.createdAt', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('v.iva', $order->dir);
            } elseif ($order->column == 5) {
                $query->addOrderBy('v.descuento', $order->dir);
            } elseif ($order->column == 6) {
                $query->addOrderBy('v.subtotal', $order->dir);
            } elseif ($order->column == 7) {
                $query->addOrderBy('v.total', $order->dir);
            } elseif ($order->column == 8) {
                $query->addOrderBy('v.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(v.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Venta[] $ventas */
        $ventas = $query->getQuery()->getResult();

        foreach ($ventas as $venta) {
            $results->data[] = [
                $venta->getId(),
                $venta->getCliente() ? $venta->getCliente()->getNombre() : '',
                $venta->getTipoVentaName(),
                $venta->getCreatedAt()->format('d/m/Y'),
                'MX$ ' . number_format(($venta->getIva() / 100), 2),
                'MX$ ' . number_format(($venta->getDescuento() / 100), 2),
                'MX$ ' . number_format(($venta->getSubtotal() / 100), 2),
                'MX$ ' . number_format(($venta->getTotal() / 100), 2),
                $venta->getId(),
            ];
        }

        return $results;
    }
}
