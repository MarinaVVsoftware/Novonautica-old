<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/11/18
 * Time: 15:01
 */

namespace AppBundle\Controller\Astillero;


use DataTables\DataTablesInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ReporteController
 * @package AppBundle\Controller\Astillero
 * @Route("/reporte/astillero")
 */
class ReporteController extends AbstractController
{
    /**
     * Muestra los adeudos y abonos sumados de los clientes que han cotizado en astillero
     *
     * @Route("/", name="reporte_ast_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexReporteAction()
    {
        return $this->render('astillero/reporte/index.html.twig', ['title' => 'Embarcaciones']);
    }

    /**
     * @Route("/contratista", name="reporte_ast_contratista")
     * @Method("GET")
     */
    public function contratistaReporteAction()
    {
        return $this->render('astillero/reporte/contratista.html.twig', ['title' => 'Contratistas']);
    }

    /**
     * @Route("/cliente", name="reporte_ast_client")
     * @Method("GET")
     */
    public function clienteReporteAction()
    {
        return $this->render('astillero/reporte/cliente.html.twig', ['title' => 'Clientes']);
    }

    /**
     * @Route("/clientes-datum.json", name="reporte_ast_client_data")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function clienteDataReporteAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'astilleroReporte');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/datum.json", name="reporte_ast_contratista_data")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function getContratistaReporteDataAction(Request $request)
    {
        $proveedor = $request->query->get('proveedor');
        $inicio = $request->query->get('start');
        $fin = $request->query->get('end');

        $em = $this->getDoctrine();
        $contratistaRepository = $em->getRepository('AppBundle:Astillero\Contratista');
        $trabajos = $contratistaRepository->getTrabajosByProveedor($proveedor, $inicio, $fin);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $encoders = [new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);

        $response = $serializer->serialize($trabajos, 'json', [
            'groups' => ['AstilleroReporte'],
            DateTimeNormalizer::FORMAT_KEY => 'd-m-Y'
        ]);

        return JsonResponse::fromJsonString($response)
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/proveedores.json")
     * @Method("GET")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getProveedoresAction(Request $request)
    {
        $query = $request->query->get('query');

        if (null === $query) { return $this->json([]); }

        $proveedorRepository = $this->getDoctrine()->getRepository('AppBundle:Astillero\Proveedor');
        $proveedores = $proveedorRepository->findProveedorNameLike($query);

        return $this->json($proveedores);
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
        $cotizacionRepository = $this->getDoctrine()->getRepository('AppBundle:AstilleroCotizacion');

        $start = $request->query->get('start')
            ? new \DateTime($request->query->get('start'))
            : new \DateTime('-29 days');

        $end = $request->query->get('end')
            ? new \DateTime($request->query->get('end'))
            : new \DateTime();

        $dates = $cotizacionRepository
            ->getWorkedBoatsByDaterange($start, $end);

        // Rellena las fechas faltantes
        /*$fechas = array_column($dates, 'fecha');

        $days = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        foreach ($days as $day) {
            $keyTime = $day->format('Y-m-d H:i:s');
            if (!in_array($keyTime, $fechas)) {
                $dates[] = [
                    'fecha' => $keyTime,
                    'total' => 0,
                ];
            }
        }

        sort($dates);*/
        return (new JsonResponse($dates))->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/income-report.json")
     * @Method("GET")
     * @param Request $request
     *
     * @return Response
     */
    public function getIncomeAction(Request $request)
    {
        $serviciosRepository = $this->getDoctrine()->getRepository('AppBundle:AstilleroCotizaServicio');

        $start = $request->query->get('start')
            ? new \DateTime($request->query->get('start'))
            : new \DateTime('-29 days');

        $end = $request->query->get('end')
            ? new \DateTime($request->query->get('end'))
            : new \DateTime();

        $incomeReport = $serviciosRepository->getIncomeReport($start, $end);

        return (new JsonResponse($incomeReport))->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}