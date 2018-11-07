<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 12/11/2018
 * Time: 11:28 AM
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
 * Class AlmacenController
 * @Route("almacen")
 */
class AlmacenController extends Controller
{
    /**
     * @Route("/", name="almacen_index")
     * @Method("GET")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     * @return JsonResponse|Response
     */
    public function indexAlmacenAction(Request $request, DataTablesInterface $dataTables)
    {
        if($request->isXmlHttpRequest()){
            try{
                $results = $dataTables->handle($request,'almacen');
                return $this->json($results);
            } catch(HttpException $e){
                return $this->json($e->getMessage(),$e->getStatusCode());
            }
        }
        return $this->render('almacen/index.html.twig',['title' => 'Almacén']);
    }

    /**
     * @Route("/{id}", name="almacen_show")
     * @Method("GET")
     * @param Solicitud $solicitud
     * @return Response
     */
    public function showAlmacenction(Solicitud $solicitud)
    {
        return $this->render('almacen/show.html.twig',[
            'title' => 'Detalle almacén',
            'solicitud' => $solicitud
        ]);
    }

    /**
     * @Route("/{id}/validar", name="almacen_validar")
     * @Method({"GET", "POST"})
     */
    public function validarAlmacenAction(Request $request, Solicitud $solicitud)
    {
        $em = $this->getDoctrine()->getManager();
        $this->denyAccessUnlessGranted('ALMACEN_VALIDAR',$solicitud);

        if($solicitud->getValidadoCompra() === false || $solicitud->getValidadoAlmacen()){ throw new NotFoundHttpException(); }
        $editForm = $this->createForm('AppBundle\Form\Almacen\ValidarType',$solicitud);
        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            if($solicitud->getValidadoAlmacen()){
                $solicitud->setNombreValidoAlmacen($this->getUser()->getNombre());
                $solicitud->setFechaValidoAlmacen(new \DateTime('now'));
                $repositorio = '';
                foreach ($solicitud->getConceptos() as $concepto){
                    if($concepto->getMarinaServicio()){
                        $repositorio = $em->getRepository('AppBundle:MarinaHumedaServicio')
                            ->findOneBy(['id' => $concepto->getMarinaServicio()->getId()]);
                    }elseif($concepto->getCombustibleCatalogo()){
                        $repositorio = $em->getRepository('AppBundle:Combustible\Catalogo')
                            ->findOneBy(['id' => $concepto->getCombustibleCatalogo()->getId()]);
                    }elseif($concepto->getAstilleroProducto()){
                        $repositorio = $em->getRepository('AppBundle:Astillero\Producto')
                            ->findOneBy(['id' => $concepto->getAstilleroProducto()->getId()]);
                    }elseif($concepto->getTiendaProducto()){
                        $repositorio = $em->getRepository('AppBundle:Tienda\Producto')
                            ->findOneBy(['id' => $concepto->getTiendaProducto()->getId()]);
                    }
                    $repositorio->setExistencia($repositorio->getExistencia() + $concepto->getCantidad());
                }
            }
            $em->persist($solicitud);
            $em->flush();
            return $this->redirectToRoute('compra_show',['id' => $solicitud->getId()]);
        }
        return $this->render('almacen/validar.html.twig',[
            'form' => $editForm->createView(),
            'solicitud' => $solicitud,
            'title' => 'Almacen - Validar'
        ]);
    }
}