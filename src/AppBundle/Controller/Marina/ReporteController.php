<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/11/18
 * Time: 13:57
 */

namespace AppBundle\Controller\Marina;


use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ReporteController
 * @package AppBundle\Controller\Marina
 * @Route("/reporte/marina")
 */
class ReporteController extends AbstractController
{
    /**
     * Muestra los adeudos y abonos sumados de los clientes que han cotizado en marina
     *
     * @Route("/", name="reporte_mar_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexReporteAction()
    {
        return $this->render('marinahumeda/reporte/index.html.twig', ['title' => 'Clientes']);
    }

    /**
     * Rellena la tabla de reportes de marina
     *
     * @Route("/data", name="reporte_mar_index_data")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexDataReporteAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'marinaReporte');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/embarcaciones", name="reporte_mar_embarcaciones")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function embarcacionReporteAction()
    {
        return $this->render('marinahumeda/reporte/embarcacion.html.twig', ['title' => 'Embarcaciones']);
    }

    /**
     * @Route("/boats-history.json")
     * @Method("GET")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getBoatHistoryAction(Request $request)
    {
        $cotizacionRepository = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion');

        $start = $request->query->get('start')
            ? new \DateTime($request->query->get('start'))
            : new \DateTime('-29 days');

        $end = $request->query->get('end')
            ? new \DateTime($request->query->get('end'))
            : new \DateTime();

        $dates = $cotizacionRepository
            ->getWorkedBoatsByDaterange($start, $end);

        return (new JsonResponse($dates))->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}