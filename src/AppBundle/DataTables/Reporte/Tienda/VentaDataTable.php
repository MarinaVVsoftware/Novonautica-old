<?php


namespace AppBundle\DataTables\Reporte\Tienda;


use AppBundle\Entity\Tienda\Venta;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class VentaDataTable extends AbstractDataTableHandler
{
    const ID = 'reporte/venta';
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
     * @return DataTableResults
     * @throws DataTableException
     *
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $results = new DataTableResults();
        $repository = $this->doctrine->getRepository(Venta\Concepto::class);

        $query = $repository->createQueryBuilder('concepto')->select('COUNT(concepto.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('concepto');
        $query
            ->select('concepto', 'producto', 'venta')
            ->leftJoin('concepto.producto', 'producto')
            ->leftJoin('concepto.venta', 'venta')
            ->leftJoin('producto.claveUnidad', 'claveUnidad');

        $query->andWhere('venta.createdAt BETWEEN :start AND :end');
        $query->setParameter('start', $request->customData['dates']['start']);
        $query->setParameter('end', $request->customData['dates']['end']);

        if ($request->customData['clasificacion'] !== '0') {
            $query->andWhere('venta.tipoVenta = :clasificacion');
            $query->setParameter('clasificacion', $request->customData['clasificacion'] === '1');

        }

        if ($request->search->value) {
            $query->where(
                '(LOWER(producto.nombre) LIKE :search OR ' .
                'LOWER(producto.codigoBarras) LIKE :search OR ' .
                'LOWER(claveUnidad.nombre) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('producto.codigoBarras', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('producto.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('claveUnidad.nombre', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('concepto.cantidad', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('producto.precio', $order->dir);
            } elseif ($order->column == 5) {
                $query->addOrderBy('concepto.subtotal', $order->dir);
            } elseif ($order->column == 6) {
                $query->addOrderBy('concepto.iva', $order->dir);
            } elseif ($order->column == 7) {
                $query->addOrderBy('concepto.total', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(concepto.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        /** @var Venta\Concepto[] $conceptos */
        $conceptos = $query->getQuery()->getResult();

        foreach ($conceptos as $concepto) {
            $producto = $concepto->getProducto();
            $claveUnidad = $producto->getClaveUnidad();

            $results->data[] = [
                $producto->getCodigoBarras(),
                $producto->getNombre(),
                $claveUnidad->getNombre(),
                $concepto->getCantidad(),
                $concepto->getPrecioUnitario() / 2,
                $concepto->getSubtotal() / 2,
                $concepto->getIva() / 2,
                $concepto->getTotal() / 2,
            ];
        }

        return $results;
    }
}
