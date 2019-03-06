<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/29/18
 * Time: 13:31
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Contabilidad\Facturacion;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class FacturacionDataTable extends AbstractDataTableHandler
{
    const ID = 'facturas';
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $facturasRepo = $this->doctrine->getRepository(Facturacion::class);
        $results = new DataTableResults();

        $query = $facturasRepo->createQueryBuilder('fa');
        $results->recordsTotal = $query->select('COUNT(fa.id)')->getQuery()->getSingleScalarResult();

        $query = $facturasRepo->createQueryBuilder('fa')
            ->select('fa', 'emi', 'rec', 'cm', 'ca', 'cc', 'ct')
            ->leftJoin('fa.emisor', 'emi')
            ->leftJoin('fa.receptor', 'rec')
            ->leftJoin('fa.cotizacionMarina', 'cm')
            ->leftJoin('fa.cotizacionAstillero', 'ca')
            ->leftJoin('fa.cotizacionCombustible', 'cc')
            ->leftJoin('fa.cotizacionTienda', 'ct');

        if ($request->search->value) {
            $query
                ->orWhere(
                    $query->expr()->like('LOWER(fa.uuidFiscal)', ':search'),
                    $query->expr()->like('LOWER(emi.nombre)', ':search'),
                    $query->expr()->like('LOWER(emi.rfc)', ':search'),
                    $query->expr()->like('LOWER(fa.total)', ':search'),
                    $query->expr()->like('LOWER(fa.fechaTimbrado)', ':search')
                )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        if ($request->customData) {
            $query->andWhere('fa.fecha BETWEEN :start AND :end');
            $query->setParameter('start', $request->customData['dates']['start']);
            $query->setParameter('end', $request->customData['dates']['end']);
        }

        if ($request->columns[5]->search->value !== '' && $request->columns[5]->search->value >= 0) {

            if ($request->columns[5]->search->value == 3) {
                $query->andWhere('fa.metodoPago = \'PUE\'');
            } else {
                $query->andWhere('fa.isPagada = :pagada AND fa.metodoPago != \'PUE\'')
                    ->setParameter('pagada', $request->columns[5]->search->value);
            }
        }

        if ($request->columns[6]->search->value !== '' && $request->columns[6]->search->value >= 0) {
            $estatus = $request->columns[6]->search->value;

            if ($estatus == 0) {
                $query->andWhere(
                    $query->expr()->orX(
                        '(cm.estatuspago IS NULL AND cm.id IS NOT NULL)',
                        '(ca.estatuspago IS NULL AND ca.id IS NOT NULL)',
                        '(cc.estatuspago IS NULL AND cc.id IS NOT NULL)'
                    )
                );
            }

            if ($estatus == 1 || $estatus == 2) {
                $query->andWhere(
                    $query->expr()->orX(
                        'cm.estatuspago = :estatus',
                        'ca.estatuspago = :estatus',
                        'cc.estatuspago = :estatus'
                    )
                )
                    ->setParameter('estatus', $estatus);
            }

            if ($estatus == 3) {
                $query->andWhere(
                    'cm.factura IS NULL',
                    'ca.factura IS NULL',
                    'cc.factura IS NULL'
                );
            }
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $query->addOrderBy('fa.folio', $order->dir);
            } elseif ($order->column === 1) {
                $query->addOrderBy('emi.rfc', $order->dir);
            } elseif ($order->column === 2) {
                $query->addOrderBy('fa.rfc', $order->dir);
            } elseif ($order->column === 3) {
                $query->addOrderBy('fa.metodoPago', $order->dir);
            } elseif ($order->column === 4) {
                $query->addOrderBy('fa.total', $order->dir);
            } elseif ($order->column === 5) {
                $query->addOrderBy('fa.fechaTimbrado', $order->dir);
            } elseif ($order->column === 6) {
                $query->addOrderBy('fa.isPagada', $order->dir);
            } elseif ($order->column === 7) {
                $query->addOrderBy('fa.id', $order->dir);
            }
        }

        /** @var Facturacion[] $facturas */
        $facturas = $query->getQuery()->getResult();
        $results->recordsFiltered = count($facturas);

        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            $factura = $facturas[$index];
            $cotizacion = $factura->getCotizacion();

            $emisor = $factura->getEmisor();
            $receptor = $factura->getReceptor();
            $nombreXML = explode('/', $factura->getXmlArchivo());
            $nombreXML = end($nombreXML);

            $fecha = new \DateTime($factura->getFechaTimbrado());

            $results->data[] = [
                $fecha->format('d/m/Y'),
                $factura->getFolio(),
                [
                    'id' => $emisor->getId(),
                    'rfc' => $emisor->getRfc(),
                    'razonSocial' => $emisor->getNombre(),
                ],
                [
                    'rfc' => $receptor->getRfc(),
                    'razonSocial' => $receptor->getRazonSocial(),
                ],
                [
                    'metodo' => $factura->getMetodoPago(),
                    'monto' => '$'.number_format($factura->getTotal() / 100, 2).' '.$factura->getMoneda(),
                ],
                $factura->isPagada(),
                [
                    'id' => $cotizacion ? $cotizacion->getId() : null,
                    'pagada' => $cotizacion ? $cotizacion->getEstatuspago() : null,
                ],
                [
                    'xml' => $nombreXML,
                    'pdf' => $factura->getId(),
                    'id' => $factura->getId(),
                    'status' => $factura->isCancelada(),
                    'metodoPago' => $factura->getMetodoPago(),
                ],
            ];
        }

        return $results;
    }
}
