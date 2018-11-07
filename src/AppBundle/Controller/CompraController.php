<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 12/11/2018
 * Time: 12:32 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Solicitud;
use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CompraController
 * @Route("compra")
 */
class CompraController extends Controller
{
    /**
     * @Route("/", name="compra_index")
     * @Method("GET")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     * @return JsonResponse|Response
     */
    public function indexCompraAction(Request $request, DataTablesInterface $dataTables)
    {
        if($request->isXmlHttpRequest()){
            try{
                $results = $dataTables->handle($request,'compra');
                return $this->json($results);
            } catch(HttpException $e){
                return $this->json($e->getMessage(),$e->getStatusCode());
            }
        }
        return $this->render('compra/index.html.twig',['title' => 'Compras']);
    }

    /**
     * @Route("/{id}", name="compra_show")
     * @Method("GET")
     * @param Solicitud $solicitud
     * @return Response
     */
    public function showAction(Solicitud $solicitud)
    {
        return $this->render('compra/show.html.twig',[
            'title' => 'Detalle compra',
            'solicitud' => $solicitud
        ]);
    }

    /**
     * @Route("/{id}/editar", name="compra_edit")
     * @Method({"GET", "POST"})
     */
    public function editCompraAction(Request $request, Solicitud $solicitud)
    {
        $em = $this->getDoctrine()->getManager();

        $this->denyAccessUnlessGranted('COMPRA_EDIT',$solicitud);

        if($solicitud->getValidadoCompra()){ throw new NotFoundHttpException(); }
        if(!$solicitud->getIva()){
            $valor = $em->getRepository('AppBundle:ValorSistema')->findAll()[0];
            $solicitud->setIva($valor->getIva());
        }
        $editForm = $this->createForm('AppBundle\Form\CompraType',$solicitud);
        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            if($solicitud->getValidadoCompra()){
                $solicitud->setNombreValidoCompra($this->getUser()->getNombre());
                $solicitud->setFechaValidoCompra(new \DateTime('now'));
            }
            $em->persist($solicitud);
            $em->flush();
            return $this->redirectToRoute('compra_show',['id' => $solicitud->getId()]);
        }
        return $this->render('compra/edit.html.twig',[
            'form' => $editForm->createView(),
            'solicitud' => $solicitud,
            'title' => 'Compra validar'
        ]);
    }
}