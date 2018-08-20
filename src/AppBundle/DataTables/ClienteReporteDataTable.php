<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/29/18
 * Time: 15:52
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\MarinaHumedaCotizacion;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ClienteReporteDataTable extends AbstractDataTableHandler
{
    const ID = 'clienteReporteAdeudos';
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
        $results = new DataTableResults();
        $repository = $this->doctrine->getRepository(Cliente::class);

        $cliente = $request->customData['cliente'];

        $results->recordsTotal = $repository->getCotizacionesCount($cliente);

        $marinaRepository = $this->doctrine->getRepository(MarinaHumedaCotizacion::class);
        $astilleroRepository = $this->doctrine->getRepository(AstilleroCotizacion::class);

        $marinaQuery = $marinaRepository->createQueryBuilder('cotizacion');
        $astilleroQuery = $astilleroRepository->createQueryBuilder('cotizacion');

        $cotizacionesMarina = $marinaQuery
            ->andWhere('IDENTITY(cotizacion.cliente) = ?1 AND cotizacion.validacliente = 2')
            ->setParameter(1, $cliente);

        $cotizacionesAstillero = $astilleroQuery
            ->andWhere('IDENTITY(cotizacion.cliente) = ?1 AND cotizacion.validacliente = 2')
            ->setParameter(1, $cliente);

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
            } elseif ($order->column == 2) {
                $query->addOrderBy('u.correo', $order->dir);
            } elseif ($order->column == 3) {
                $query->addOrderBy('u.isActive', $order->dir);
            } elseif ($order->column == 4) {
                $query->addOrderBy('u.id', $order->dir);
            }
        }
        */

        /*
        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);
        */

        /** @var AstilleroCotizacion[]|MarinaHumedaCotizacion[] $cotizaciones */
        $cotizaciones = $cotizacionesMarina->getQuery()->getResult();

        $cotizaciones = array_merge(
            $cotizaciones,
            $cotizacionesAstillero->getQuery()->getResult()
        );

        dump($cotizaciones);

        $results->recordsFiltered = count($cotizaciones);

        foreach ($cotizaciones as $cotizacion) {
            $results->data[] = [
                $cotizacion->getFecharegistro()->format('d-m-Y'),
                $cotizacion->getFolioString(),
                '$ ' . number_format(($cotizacion->getTotal() / 100), 2),
                $cotizacion->getId(),
            ];
        }

        return $results;
    }
}
