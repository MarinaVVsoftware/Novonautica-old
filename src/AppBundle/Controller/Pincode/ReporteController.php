<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 12/03/2019
 * Time: 01:33 PM
 */

namespace AppBundle\Controller\Pincode;


use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Class ReporteController
 * @package AppBundle\Controller\Pincode
 * @Route("/reporte/pincodes")
 */
class ReporteController extends AbstractController
{
    /**
     * @Route("/", name="reporte_pincodes")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function pincodesAction()
    {
        return $this->render('pincode/reporte.html.twig',['title' => 'Pincodes']);
    }

    /**
     * @Route("/pincodes-data")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function pincodesDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'reporte/pincode');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}