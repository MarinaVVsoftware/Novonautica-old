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
use DataTables\DataTableException;
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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $facturasRepo = $this->doctrine->getRepository('AppBundle:Contabilidad\Facturacion');
        $results = new DataTableResults();

        $query = $facturasRepo->createQueryBuilder('fa');
        $results->recordsTotal = $query->select('COUNT(fa.id)')->getQuery()->getSingleScalarResult();

        $query = $facturasRepo->createQueryBuilder('fa')
            ->leftJoin('fa.emisor', 'emi');

        if ($request->search->value) {
            $query
                ->orWhere(
                    $query->expr()->like('LOWER(fa.folioFiscal)', ':search'),
                    $query->expr()->like('LOWER(emi.nombre)', ':search'),
                    $query->expr()->like('LOWER(emi.rfc)', ':search'),
                    $query->expr()->like('LOWER(fa.rfc)', ':search'),
                    $query->expr()->like('LOWER(fa.razonSocial)', ':search'),
                    $query->expr()->like('LOWER(fa.total)', ':search'),
                    $query->expr()->like('LOWER(fa.fechaTimbrado)', ':search')
                )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $query->addOrderBy('fa.folioFiscal', $order->dir);
            } elseif ($order->column === 1) {
                $query->addOrderBy('emi.rfc', $order->dir);
            } elseif ($order->column === 2) {
                $query->addOrderBy('fa.rfc', $order->dir);
            } elseif ($order->column === 3) {
                $query->addOrderBy('fa.razonSocial', $order->dir);
            } elseif ($order->column === 4) {
                $query->addOrderBy('fa.total', $order->dir);
            } elseif ($order->column === 5) {
                $query->addOrderBy('fa.fechaTimbrado', $order->dir);
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
            $emisor = $factura->getEmisor();
            $nombreXML = explode('/', $factura->getXmlArchivo());
            $nombreXML = end($nombreXML);

            $results->data[] = [
                $factura->getFolio(),
                $emisor->getRfc() . ' / ' . $emisor->getNombre(),
                $factura->getRfc() . ' / ' . $factura->getCliente(),
                $factura->getRazonSocial(),
                '$' . number_format($factura->getTotal() / 100, 2) . ' ' . $factura->getMoneda(),
                $factura->getFechaTimbrado(),
                [
                    'xml' => $nombreXML,
                    'pdf' => $factura->getId()
                ],
                [
                    'id' => $factura->getId(),
                    'status' => $factura->getEstatus()
                ],
            ];
        }

        return $results;
    }
}