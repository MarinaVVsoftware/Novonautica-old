<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/11/18
 * Time: 13:57
 */

namespace AppBundle\Controller\Marina;


use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\Pago;
use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @Route("/ingresos", name="reporte_mar_ingresos")
     */
    public function ingresoReporteAction()
    {
        return $this->render('marinahumeda/reporte/ingreso.html.twig', ['title' => 'Reporte de ingresos']);
    }

    /**
     * @Route("/ingresos-data")
     */
    public function ingresoReporteDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'marina/reporte/ingreso');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/ingresos-data/{id}")
     */
    public function ingresoDetallePagoAction($id)
    {
        $pagoRepository = $this->getDoctrine()->getRepository(Pago::class);
        $pagos = array_map(
            function (Pago $pago) {
                $cantidad = $pago->getCantidad();

                if ($pago->getDivisa() === 'MXN') {
                    $cantidad = round($cantidad * $pago->getDolar() / 100, 2);
                }

                $data = [
                    'cantidad' => $cantidad,
                    'divisa' => $pago->getDivisa(),
                    'dolar' => $pago->getDolar(),
                    'fecha' => $pago->getFecharealpago()->format('d/m/y'),
                    'metodo' => $pago->getMetodopago(),
                    'cuentaEnvio' => [
                        'banco' => $pago->getBanco(),
                        'titular' => $pago->getTitular(),
                        'numero' => $pago->getNumcuenta(),
                        'codigoSeguimiento' => $pago->getCodigoseguimiento(),
                    ],
                    'cuentaDeposito' => [
                        'banco' => $pago->getCuentabancaria() ? $pago->getCuentabancaria()->getBanco() : null,
                        'numero' => $pago->getCuentabancaria() ? $pago->getCuentabancaria()->getClabe() : null,
                    ]
                ];

                return $data;
            },
            $pagoRepository->findBy(['mhcotizacion' => $id])
        );

        return $this->json($pagos);
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
     * @Route("/ocupacion", name="reporte_mar_ocupacion")
     * @Method("GET")
     *
     * @return Response
     */
    public function ocupacionReporteAction()
    {
        return $this->render('marinahumeda/reporte/ocupacion.html.twig', ['title' => 'Ocupacion']);
    }

    /**
     * @Route("/cotizaciones", name="reporte_mar_cotizaciones")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cotizacionReporteAction()
    {
        return $this->render('marinahumeda/reporte/cotizacion.html.twig', ['title' => 'Cotizaciones']);
    }

    /**
     * @Route("/ocupacion-data.{_format}")
     * @Method("GET")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getOcupacionDataAction(Request $request)
    {
        $response = [];
        $ocupacionTotal = 0;

        $start = $request->query->get('start')
            ? \DateTime::createFromFormat('Y-m-d', $request->query->get('start'))
            : new \DateTime('first day of this month');

        $end = $request->query->get('end')
            ? \DateTime::createFromFormat('Y-m-d', $request->query->get('end'))
            : new \DateTime('last day of this month');

        $movimientos = $this->getDoctrine()
            ->getRepository('AppBundle:SlipMovimiento')
            ->getOcupationRateByDaterange($start, $end);

        foreach ($movimientos as $i => $movimiento) {
            $ocupacionTotal += (float)$movimiento['porcentajeOcupacion'];
        }

        $response['movimientos'] = $movimientos;
        $response['fechas'] = [
            'inicio' => $start,
            'final' => $end,
        ];

        return $this
            ->json($response)
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/cotizacion-history.{_format}")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response|JsonResponse
     */
    public function getCotizacionHistoryAction(Request $request)
    {
        $cotizacionRepository = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion');

        $start = $request->query->get('start')
            ? new \DateTime($request->query->get('start'))
            : new \DateTime('-29 days');

        $end = $request->query->get('end')
            ? (new \DateTime($request->query->get('end')))->modify('+1 days')
            : new \DateTime();

        $cotizaciones = $cotizacionRepository->getCotizacionHistoryByDateRange(
            $start,
            $end,
            $request->query->get('novo'),
            $request->query->get('client')
        );

        if ($request->getRequestFormat() === 'csv') {
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $csvData = $serializer->encode($cotizaciones, 'csv');

            return new Response(
                $csvData,
                Response::HTTP_OK,
                ['Content-type' => 'text/csv']
            );
        }

        return $this->json($cotizaciones)
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/boats-history.{_format}")
     * @Method("GET")
     * @param Request $request
     *
     * @return Response|JsonResponse
     * @throws \Exception
     */
    public function getBoatHistoryAction(Request $request)
    {
        $cotizacionRepository = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion');

        $start = $request->query->get('start')
            ? new \DateTime($request->query->get('start'))
            : new \DateTime('-29 days');

        $end = $request->query->get('end')
            ? (new \DateTime($request->query->get('end')))->modify('+1 day')
            : new \DateTime('+1 day');

        $history = $cotizacionRepository
            ->getWorkedBoatsByDaterange($start, $end);

        if ($request->getRequestFormat() === 'csv') {
            foreach ($history as $i => $item) {
                $history[$i]['fecha'] = substr($history[$i]['fecha'], 0, -9);
            }

            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $csvData = $serializer->encode($history, 'csv');

            return new Response(
                $csvData,
                Response::HTTP_OK,
                ['Content-type' => 'text/csv']
            );
        }

        return $this->json($history)
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/cliente")
     */
    public function clientesMorososAction(Request $request)
    {
        $query = $request->query->get('q');
        $marinaRepository = $this->getDoctrine()->getRepository(MarinaHumedaCotizacion::class);
        $clientes = $marinaRepository->getClientesMorososLike($query);

        return $this->json($clientes);
    }

    /**
     * @Route("/embarcacion")
     */
    public function barcosDeMorososAction(Request $request)
    {
        $query = $request->query->get('q');
        $marinaRepository = $this->getDoctrine()->getRepository(MarinaHumedaCotizacion::class);
        $clientes = $marinaRepository->getEmbarcacionesdeMorososLike($query);

        return $this->json($clientes);
    }
}
