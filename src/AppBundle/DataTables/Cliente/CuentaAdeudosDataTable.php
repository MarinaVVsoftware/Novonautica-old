<?php
/**
 * User: inrumi
 * Date: 8/15/18
 * Time: 15:19
 */

namespace AppBundle\DataTables\Cliente;


use DataTables\AbstractDataTableHandler;
use DataTables\DataTableException;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\Common\Persistence\ManagerRegistry;

class CuentaAdeudosDataTable extends AbstractDataTableHandler
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
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        // TODO: Implement handle() method.
    }
}
