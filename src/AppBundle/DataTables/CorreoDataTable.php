<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2/7/18
 * Time: 12:03
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Correo;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class CorreoDataTable extends AbstractDataTableHandler
{
    const ID = 'correo';
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
        $correoRepo = $this->doctrine->getRepository('AppBundle:Correo');
        $results = new DataTableResults();

        $qb = $correoRepo->createQueryBuilder('c');
        $results->recordsTotal = $qb->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();

        $q = $qb->select('c');

        if ($request->search->value) {
            $q->where('(c.fecha LIKE :search OR ' .
                'LOWER(c.tipo) LIKE :search OR ' .
                'LOWER(c.descripcion) LIKE :search)'
            )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->columns as $column) {
            if ($column->search->value) {
                $value = strtolower($column->search->value);

                if ($column->data == 1) {
                    $q->andWhere('LOWER(c.tipo) LIKE :tipo')
                        ->setParameter('tipo', "%{$value}%");
                }
            }
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $q->addOrderBy('c.fecha', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('c.tipo', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('c.descripcion', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('c.id', $order->dir);
            }
        }

        $correos = $q->getQuery()->getResult();
        $results->recordsFiltered = count($correos);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var Correo $correo */
            $correo = $correos[$index];
            $cotizacion = $correo->getMhcotizacion() ? $correo->getMhcotizacion() : $correo->getAcotizacion();
            $results->data[] = [
                $correo->getFecha()->format('Y/m/d'),
                $cotizacion ? $cotizacion->getBarco()->getNombre() : '',
                $cotizacion ? $cotizacion->getCliente()->getNombre() : '',
                $cotizacion ? $cotizacion->getBarco()->getNombreCapitan() : '',
                $cotizacion ? $cotizacion->getBarco()->getNombreResponsable() : '',
                $correo->getTipo(),
                $correo->getDescripcion(),
                [
                    'id' => $correo->getId(),
                    'folio' => $correo->getFolioCotizacion()
                ]
            ];
        }

        return $results;

    }
}