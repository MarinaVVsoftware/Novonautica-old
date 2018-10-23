<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/11/18
 * Time: 15:01
 */

namespace AppBundle\Controller\Astillero;


use AppBundle\Entity\Astillero\Contratista;
use AppBundle\Entity\AstilleroCotizacion;
use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
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
     * @Route("/contratista", name="reporte_ast_contratista", methods={"POST", "GET"})
     */
    public function contratistaReporteAction(Request $request)
    {
        $proveedor = $request->request->get('contratista');

        $dates = explode(' - ', $request->request->get('dates'));

        $inicio = isset($dates[0]) && "" !== $dates[0]
            ? \DateTime::createFromFormat('d/m/Y', $dates[0])
            : new \DateTime('first day of this month');

        $fin = isset($dates[1])
            ? \DateTime::createFromFormat('d/m/Y', $dates[1])
            : new \DateTime('last day of this month');

        $trabajos = $this->getDoctrine()
            ->getRepository(Contratista::class)
            ->getTrabajosByProveedor($inicio, $fin, $proveedor);

        return $this->render(
            'astillero/reporte/contratista.html.twig',
            [
                'title' => 'Contratistas',
                'trabajos' => $trabajos,
                'values' => [
                    'dates' => [
                        'inicio' => $inicio,
                        'fin' => $fin,
                    ],
                    'proveedor' => $proveedor,
                ],
            ]
        );
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
     * @Route("/proveedores.json")
     * @Method("GET")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getProveedoresAction(Request $request)
    {
        $query = $request->query->get('query');

        if (null === $query) {
            return $this->json([]);
        }

        $proveedorRepository = $this->getDoctrine()->getRepository('AppBundle:Astillero\Proveedor');
        $proveedores = $proveedorRepository->findProveedorNameLike($query);

        return $this->json($proveedores);
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
        $cotizacionRepository = $this->getDoctrine()->getRepository('AppBundle:AstilleroCotizacion');

        $start = $request->query->get('start')
            ? new \DateTime($request->query->get('start'))
            : new \DateTime('-29 days');

        $end = $request->query->get('end')
            ? new \DateTime($request->query->get('end'))
            : new \DateTime();

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
     * @Route("/income-report.{_format}")
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

        if ($request->getRequestFormat() === 'csv') {
            foreach ($incomeReport as $i => $item) {
                $incomeReport[$i]['fecha'] = substr($incomeReport[$i]['fecha'], 0, -9);
            }

            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $csvData = $serializer->encode($incomeReport, 'csv');

            return new Response(
                $csvData,
                Response::HTTP_OK,
                ['Content-type' => 'text/csv']
            );
        }

        return $this->json($incomeReport)
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/cliente/cliente")
     */
    public function clientesMorososAction(Request $request)
    {
        $query = $request->query->get('q');
        $marinaRepository = $this->getDoctrine()->getRepository(AstilleroCotizacion::class);
        $clientes = $marinaRepository->getClientesMorososLike($query);

        return $this->json($clientes);
    }

    /**
     * @Route("/cliente/embarcacion")
     */
    public function barcosDeMorososAction(Request $request)
    {
        $query = $request->query->get('q');
        $marinaRepository = $this->getDoctrine()->getRepository(AstilleroCotizacion::class);
        $clientes = $marinaRepository->getEmbarcacionesdeMorososLike($query);

        return $this->json($clientes);
    }

    /**
     * @Route("/ingresos", name="reporte_ast_ingresos")
     * @Method({"GET", "POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function ingresosAstilleroAction(Request $request)
    {
        $ingresos = [];
        $form = $this->createFormBuilder()
            ->add('inicio', DateType::class, [
                'label' => 'Fecha inicio',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
            ])
            ->add('fin', DateType::class, [
                'label' => 'Fecha fin',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
            ])
            ->add('barco', EntityType::class, [
                'class' => 'AppBundle:Barco',
                'placeholder' => 'Todos',
                'required' => false,
                'attr' => ['class' => 'select-buscador' ]
            ])
            ->add('buscar', SubmitType::class, [
                'attr' => ['class' => 'btn-xs btn-azul pull-right no-loading'],
                'label' => 'Buscar',
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $datos = $form->getData();
            $idbarco = $datos['barco'] ? $datos['barco']->getId() : '0';
            $em = $this->getDoctrine()->getManager();
            $ingresos = $em->getRepository('AppBundle:AstilleroCotizacion')
                ->obtenIngresosTodos($idbarco,
                    $datos['inicio']->format('Y-m-d'),
                    $datos['fin']->format('Y-m-d')
                );
        }

        return $this->render('astillero/reporte/ingreso.html.twig', [
            'title' => 'Reportes Ingresos Astillero',
            'ingresos' => $ingresos,
            'form' => $form->createView(),
        ]);
    }
}
