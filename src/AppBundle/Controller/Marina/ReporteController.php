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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ReporteController
 * @package AppBundle\Controller\Marina
 * @Route("/marina/reporte")
 */
class ReporteController extends AbstractController
{
    /**
     * Muestra los adeudos y abonos sumados de los clientes que han cotizado en marina
     *
     * @Route("/", name="marina-humeda_reporte_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function indexReporteAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacion');
        $mostDebtor = $repository->getMostDebtor();

        return $this->render('marinahumeda/reporte/index.html.twig', [
            'title' => 'Reportes Marina',
            'mostDebtor' => $mostDebtor,
        ]);
    }

    /**
     * Rellena la tabla de reportes de marina
     *
     * @Route("/data", name="marina-humeda_reporte_index_data")
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
}