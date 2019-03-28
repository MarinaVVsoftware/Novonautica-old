<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 25/09/2018
 * Time: 03:07 PM
 */

namespace AppBundle\Controller\Tienda;

use DataTables\DataTables;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;


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
     * @Method({"GET", "POST"})
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

    public function indexDataAction(Request $request, DataTables $dataTables)
    {
        try {
            $dataTables->handle($request, 'reporte/venta');
        } catch (HttpException $exception) {
            return $exception->getMessage();
        }
    }
}
