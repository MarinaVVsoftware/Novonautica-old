<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 25/09/2018
 * Time: 03:07 PM
 */

namespace AppBundle\Controller\Tienda;

use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @package AppBundle\Controller\Tienda
 * @Route("/reporte/tienda")
 */
class ReporteController extends AbstractController
{
    /**
     * Muestra los adeudos y abonos sumados de los clientes que han cotizado en astillero
     *
     * @Route("/", name="reporte_store_venta")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexReporteAction(Request $request)
    {
        return $this->render(
            'tienda/reporte/venta.html.twig',
            [
                'title' => 'Reporte de tienda',
            ]
        );
    }

    /**
     * @Route("/productos.json")
     *
     * @param Request $request
     * @param DataTables $dataTables
     *
     * @return string
     */
    public function indexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $dataTables = $dataTables->handle($request, 'reporte/venta');
            return $this->json($dataTables);
        } catch (HttpException $exception) {
            return $this->json($exception->getMessage(), $exception->getStatusCode());
        }
    }
}
