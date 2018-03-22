<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/21/18
 * Time: 12:52
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Usuario;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class UsuarioDataTable extends AbstractDataTableHandler
{
    const ID = 'usuario';
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
        $repository = $this->doctrine->getRepository('AppBundle:Usuario');

        $results = new DataTableResults();

        $query = $repository->createQueryBuilder('u')->select('COUNT(u.id)');
        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('u');

        if ($request->search->value) {
            $query->where('(LOWER(u.nombre) LIKE :search OR ' .
                'LOWER(u.nombreUsuario) LIKE :search OR ' .
                'LOWER(u.correo) LIKE :search)'
            );
            $query->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column == 0) {
                $query->addOrderBy('u.nombre', $order->dir);
            } elseif ($order->column == 1) {
                $query->addOrderBy('u.nombreUsuario', $order->dir);
            } elseif ($order->column == 2) {
                $query->addOrderBy('u.correo', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('u.isActive', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('u.id', $order->dir);
            }
        }

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