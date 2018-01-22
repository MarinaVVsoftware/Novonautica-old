<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 12/14/17
 * Time: 13:33
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Barco;
use AppBundle\Entity\Cliente;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ClienteDataTable extends AbstractDataTableHandler
{
    const ID = 'cliente';
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
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $clienteRepo = $this->doctrine->getRepository('AppBundle:Cliente');
        $results = new DataTableResults();

        $qb = $clienteRepo->createQueryBuilder('cl');
        $results->recordsTotal = $qb->select('COUNT(cl.id)')->getQuery()->getSingleScalarResult();

        $q = $qb
            ->select('cl', 'ba')
            ->leftJoin('cl.barcos', 'ba')
            ->orderBy('cl.id', 'DESC')
        ;

        if ($request->search->value) {
            $q->where('(LOWER(cl.nombre) LIKE :search OR ' .
                'LOWER(cl.correo) LIKE :search OR ' .
                'LOWER(cl.telefono) LIKE :search OR ' .
                'LOWER(cl.celular) LIKE :search OR ' .
                'LOWER(cl.direccion) LIKE :search OR ' .
                'LOWER(ba.nombre) LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        /*foreach ($request->columns as $column) {
            if ($column->search->value) {
                $value = strtolower($column->search->value);

                if ($column->data == 'empresa') {
                    $q->andWhere('LOWER(cl.empresa) LIKE :empresa')
                        ->setParameter('empresa', "%{$value}%");
                }
            }
        }*/

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('cl.nombre', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('cl.correo', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('cl.telefono', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('cl.direccion', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('ba.nombre', $order->dir);
            }
        }

        $clientes = $q->getQuery()->getResult();

        $results->recordsFiltered = count($clientes);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var Cliente $cliente */
            $cliente = $clientes[$index];
            $barcos = $cliente->getBarcos()
                ->map(function ($barco) { return [$barco->getId(), $barco->getNombre()]; })
                ->toArray();

            $results->data[] = [
                $cliente->getNombre(),
                $cliente->getCorreo(),
                "{$cliente->getTelefono() } / {$cliente->getCelular()}",
                $cliente->getDireccion(),
                $barcos,
                $cliente->getId()
            ];
        }

        return $results;
    }
}