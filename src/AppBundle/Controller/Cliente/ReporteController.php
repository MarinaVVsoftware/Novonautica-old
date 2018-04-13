<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/28/18
 * Time: 17:10
 */

namespace AppBundle\Controller\Cliente;


use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("cliente/reporte")
 */
class ReporteController extends Controller
{
    /**
     * @Route("/", name="cliente_reporte_index")
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cliente\Reporte');

        return $this->render('cliente/reporte/index.html.twig', [
            'title' => 'Reporte de clientes',
            'cliente' => $repository->getBRClient(),
            'adeudo' => $repository->getAdeudoTotal(),
            'abono' => $repository->getAbonoTotal()
        ]);
    }

    /**
     * @Route("/reportes", name="cliente_reporte_index_data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function reporteDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'reporte');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}