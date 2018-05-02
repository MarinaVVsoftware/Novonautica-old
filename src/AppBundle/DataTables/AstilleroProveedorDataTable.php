<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 16/03/2018
 * Time: 01:15 PM
 */

namespace AppBundle\DataTables;


use AppBundle\Entity\Astillero\Proveedor;
use DataTables\AbstractDataTableHandler;
//use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;


class AstilleroProveedorDataTable extends AbstractDataTableHandler
{

    CONST ID = 'AstilleroProveedor';
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
        $astilleroProveedorRepo = $this->doctrine->getRepository('AppBundle:Astillero\Proveedor');
        $results = new DataTableResults();

        $qb = $astilleroProveedorRepo->createQueryBuilder('ap');
        $results->recordsTotal = $qb->select('COUNT(ap.id)')->getQuery()->getSingleScalarResult();

        $q = $qb->select('ap');
        if($request->search->value){
            $q->where('(LOWER(ap.nombre) LIKE :search OR ap.razonsocial LIKE :search OR ap.porcentaje LIKE :search OR ap.correo LIKE :search OR ap.telefono LIKE :search OR ap.proveedorcontratista LIKE :search)')
                ->setParameter('search', strtolower("%{$request->search->value}%"));
        }
        foreach ($request->order as $order) {
            if($order->column === 0){
                $q->addOrderBy('ap.proveedorcontratista', $order->dir);
            } elseif ($order->column === 1) {
                $q->addOrderBy('ap.nombre', $order->dir);
            } elseif ($order->column === 2) {
                $q->addOrderBy('ap.razonsocial', $order->dir);
            } elseif ($order->column === 3) {
                $q->addOrderBy('ap.porcentaje', $order->dir);
            } elseif ($order->column === 4) {
                $q->addOrderBy('ap.tipo', $order->dir);
            }
        }
        $proveedores = $q->getQuery()->getResult();
        $results->recordsFiltered = count($proveedores);
        for ($i = 0; $i < $request->length || $request->length === -1; $i++) {
            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            /** @var Proveedor $proveedor */
            $proveedor = $proveedores[$index];

            $results->data[] = [
                $proveedor->getProveedorcontratista() == 0 ? 'Proveedor' : 'Contratista',
                $proveedor->getNombre(),
                $proveedor->getRazonsocial(),
                $proveedor->getRfc(),
                $proveedor->getDireccionfiscal(),
                $proveedor->getCorreo(),
                $proveedor->getTelefono(),
                number_format($proveedor->getPorcentaje(), 2).' %',
                $proveedor->getTipo() == 0  ? 'Externo' : 'Interno',
                $proveedor->getId()
            ];
        }

        return $results;

    }
}