<?php
/**
 * User: inrumi
 * Date: 7/9/18
 * Time: 12:57
 */

namespace AppBundle\DataTables\Contabilidad;


use AppBundle\Entity\Contabilidad\Egreso;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class EgresoDataTable extends AbstractDataTableHandler
{
    const ID = 'egreso';
    private $doctrine;
    /**
     * @var Security
     */
    private $security;

    public function __construct(
        ManagerRegistry $doctrine,
        Security $security
    ) {
        $this->doctrine = $doctrine;
        $this->security = $security;
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
        $repository = $this->doctrine->getRepository(Egreso::class);
        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('e')->select('COUNT(e.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('e')
            ->leftJoin('e.empresa', 'empresa');

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $views = [];

            foreach ($this->security->getUser()->getRoles() as $role) {
                if (strpos($role, 'VIEW_EGRESO') === 0) {
                    $views[] = explode('_', $role)[3];
                }
            }

            $query->where(
                $query->expr()->in('e.empresa', $views)
            );
        }

        if ($request->search->value) {
            $query->andWhere('(LOWER(empresa.nombre) LIKE :search '.
                ' OR LOWER(e.subtotal) LIKE :search '.
                ' OR LOWER(e.ivatotal) LIKE :search '.
                ' OR LOWER(e.total) LIKE :search '.
                ') '
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        if ($request->customData) {
            $query->andWhere('e.fecha BETWEEN :start AND :end');
            $query->setParameter('start', $request->customData['dates']['start']);
            $query->setParameter('end', $request->customData['dates']['end']);
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('e.fecha', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('empresa.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('e.subtotal', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('e.ivatotal', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('e.total', $order->dir);
            } elseif ($order->column == 5) {
                $query->addOrderBy('e.id', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(e.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        if ($request->length > 0) {
            $query->setMaxResults($request->length);
            $query->setFirstResult($request->start);
        }

        /** @var Egreso[] $egresos */
        $egresos = $query->getQuery()->getResult();

        foreach ($egresos as $egreso) {
            $results->data[] = [
                $egreso->getFecha()->format('d/m/Y'),
                $egreso->getEmpresa()->getNombre(),
                'MX$ '.number_format(($egreso->getSubtotal() / 100), 2),
                'MX$ '.number_format(($egreso->getIvatotal() / 100), 2),
                'MX$ '.number_format(($egreso->getTotal() / 100), 2),
                $egreso->getId(),
            ];
        }

        return $results;
    }
}
