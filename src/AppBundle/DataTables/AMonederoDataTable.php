<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 28/08/2018
 * Time: 12:43 PM
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Cliente;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class AMonederoDataTable extends AbstractDataTableHandler
{
    CONST ID = 'AMonedero';
    private $doctrine;

    public function __construct(ManagerRegistry $registry)
    {
        $this->doctrine = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $clienteRepo = $this->doctrine->getRepository('AppBundle:Cliente');
        $results = new DataTableResults();
        $qb = $clienteRepo->createQueryBuilder('cl');
        $results->recordsTotal = $qb->select('COUNT(cl.id)')->getQuery()->getSingleScalarResult();
        $q = $qb->select('cl');

        if ($request->search->value) {
            $q->where('(LOWER(cl.nombre) LIKE :search OR ' .
                'cl.monederoAstillero LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('cl.nombre', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('cl.monederoAstillero', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('cl.id', $order->dir);
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
            $results->data[] = [
                $cliente->getNombre(),
                '$' . number_format($cliente->getMonederoAstillero() / 100, 2). ' MXN',
                $cliente->getId()
            ];
        }
        return $results;
    }
}