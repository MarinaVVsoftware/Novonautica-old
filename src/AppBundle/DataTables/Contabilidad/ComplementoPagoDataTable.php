<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/29/18
 * Time: 13:31
 */

namespace AppBundle\DataTables\Contabilidad;


use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Entity\Contabilidad\Facturacion\Pago;
use DataTables\AbstractDataTableHandler;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class ComplementoPagoDataTable extends AbstractDataTableHandler
{
    const ID = 'complemento_pago';
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
        $facturasRepo = $this->doctrine->getRepository(Facturacion\Pago::class);
        $results = new DataTableResults();

        $factura = $request->customData['factura'];

        $query = $facturasRepo->createQueryBuilder('p');
        $results->recordsTotal = $query->select('COUNT(p.id)')
            ->leftJoin('p.factura', 'f')
            ->where('f.id = ?1')
            ->setParameter(1, $factura)
            ->getQuery()
            ->getSingleScalarResult();

        $query = $facturasRepo->createQueryBuilder('p')
            ->leftJoin('p.factura', 'f')
            ->where('f.id = ?1')
            ->setParameter(1, $factura);

        if ($request->search->value) {
            $query
                ->orWhere(
                    $query->expr()->like('LOWER(p.uuidFacturaRelacionada)', ':search'),
                    $query->expr()->like('LOWER(p.uuidFiscal)', ':search'),
                    $query->expr()->like('fa.fechaTimbrado', ':search')
                )
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }

        foreach ($request->order as $order) {
            if ($order->column === 0) {
                $query->addOrderBy('p.uuidFacturaRelacionada', $order->dir);
            } elseif ($order->column === 1) {
                $query->addOrderBy('p.uuidFiscal', $order->dir);
            } elseif ($order->column === 2) {
                $query->addOrderBy('p.montoPagos', $order->dir);
            } elseif ($order->column === 3) {
                $query->addOrderBy('p.fechaTimbrado', $order->dir);
            }
        }

        $queryCount = clone $query;
        $queryCount->select('COUNT(p.id)');
        $results->recordsFiltered = $queryCount->getQuery()->getSingleScalarResult();

        $query->setMaxResults($request->length);
        $query->setFirstResult($request->start);

        /** @var Pago[] $pagos */
        $pagos = $query->getQuery()->getResult();

        foreach ($pagos as $pago) {

            $nombreXML = explode('/', $pago->getXmlArchivo());
            $nombreXML = end($nombreXML);

            $results->data[] = [
                $pago->getFolio(),
                $pago->getUuidFacturaRelacionada(),
                $pago->getUuidFiscal(),
                $pago->getMoneda().'$ '.number_format($pago->getMontoPagos() / 100, 2),
                $pago->getFechaTimbrado(),
                [
                    'xml' => $nombreXML,
                ],
                [
                    'id' => $pago->getId(),
                    'status' => $pago->isCancelado(),
                ],
            ];
        }

        return $results;
    }
}
