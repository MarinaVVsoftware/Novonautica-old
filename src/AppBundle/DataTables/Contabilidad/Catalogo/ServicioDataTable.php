<?php
/**
 * User: inrumi
 * Date: 9/27/18
 * Time: 10:33
 */

namespace AppBundle\DataTables\Contabilidad\Catalogo;


use AppBundle\Entity\Contabilidad\Catalogo\Servicio;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ServicioDataTable extends AbstractDataTableHandler
{
    const ID = 'contabilidad/catalogo/servicio';
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
        $repository = $this->doctrine->getRepository(Servicio::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('s')->select('COUNT(s.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('s');

        if ($request->search->value) {
            $query->where('(LOWER(s.codigo) LIKE :search OR ' .
                'LOWER(s.nombre) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('s.codigo', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('s.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('s.claveUnidad', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('s.claveProdServ', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('s.emisor', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(s.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Servicio[] $servicios */
        $servicios = $query->getQuery()->getResult();

        foreach ($servicios as $servicio) {
            $results->data[] = [
                $servicio->getCodigo(),
                $servicio->getNombre(),
                "{$servicio->getClaveUnidad()->getClaveUnidad()} / {$servicio->getClaveUnidad()->getNombre()}",
                "{$servicio->getClaveProdServ()->getClaveProdServ()} / {$servicio->getClaveProdServ()->getDescripcion()}",
                $servicio->getEmisor()->getAlias(),
                $servicio->getId(),
            ];
        }

        return $results;
    }
}
