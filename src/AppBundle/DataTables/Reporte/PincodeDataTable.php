<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 12/03/2019
 * Time: 11:25 AM
 */

namespace AppBundle\DataTables\Reporte;

use AppBundle\Entity\Pincode;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class PincodeDataTable extends AbstractDataTableHandler
{
    const ID = 'reporte/pincode';
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Handles specified DataTable request.
     *
     * @param DataTableQuery $request
     * @throws DataTableException
     * @return DataTableResults
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $repository = $this->doctrine->getRepository(Pincode::class);
        $results = new DataTableResults();

        $queryBuilder = $repository->createQueryBuilder('p');
        $results->recordsTotal = $queryBuilder->getQuery()->getScalarResult();

        $query = $queryBuilder
            ->select('p' , 'u1', 'u2')
            ->leftJoin('p.createdBy','u1')
            ->leftJoin('p.usedBy','u2');

        if ($request->search->value) {
            $query->andWhere('(LOWER(p.pin) LIKE :search OR ' .
                'LOWER(p.expiration) LIKE :search OR ' .
                'LOWER(u1.nombre) LIKE :search OR ' .
                'LOWER(u2.nombre) LIKE :search OR ' .
                'LOWER(p.description) LIKE :search OR ' .
                'LOWER(p.status) LIKE :search'.
                ')'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('p.createdAt', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('u1.nombre', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('p.usedAt', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('u2.nombre', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('p.pin', $order->dir);
            } elseif ($order->column == 5) {
                $query->addOrderBy('p.description', $order->dir);
            } elseif ($order->column == 6) {
                $query->addOrderBy('p.expiration', $order->dir);
            } elseif ($order->column == 7) {
                $query->addOrderBy('p.status', $order->dir);
            }
        }

        $pincodes = $query->getQuery()->getResult();
        $results->recordsFiltered = count($pincodes);
        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var Pincode $pincode */
            $pincode = $pincodes[$index];
            $results->data[] = [
                $pincode->getCreatedAt() ? $pincode->getCreatedAt()->format('d/m/Y h:i A') : '',
                $pincode->getUsedAt() ? $pincode->getUsedAt()->format('d/m/Y h:i A') : 'No utilizado',
                $pincode->getExpiration() ? $pincode->getExpiration()->format('d/m/Y h:i A') : '',
                $pincode->getCreatedBy() ? $pincode->getCreatedBy()->getNombre() : '',
                $pincode->getUsedBy() ? $pincode->getUsedBy()->getNombre() : 'No utilizado',
                $pincode->getPin(),
                $pincode->getDescription(),
                $pincode->getStatus() ? 'Activo' : 'Inactivo'
            ];
        }
        return $results;

    }
}