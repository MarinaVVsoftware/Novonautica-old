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

        $query = $repository->createQueryBuilder('producto')->select('COUNT(producto.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('producto');
        $query
            ->select('producto', 'venta')
            ->leftJoin('producto.venta', 'venta');

        /*
        if ($request->search->value) {
            $query->where(
                '(LOWER(u.nombre) LIKE :search OR ' .
                'LOWER(u.nombreUsuario) LIKE :search OR ' .
                'LOWER(u.correo) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }
        */

        /*
        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('u.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('u.nombreUsuario', $order->dir);
            }
        }
        */

        $queryCount = clone $query;
        $queryCount->select('COUNT(u.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Usuario[] $usuarios */
        $usuarios = $query->getQuery()->getResult();

        foreach ($usuarios as $usuario) {
            $results->data[] = [
                $usuario->getNombre(),
                $usuario->getNombreUsuario(),
                $usuario->getCorreo(),
                $usuario->getIsActive() ? 'Activo' : 'Inactivo',
                $usuario->getId(),
            ];
        }

        return $results;
    }
}
