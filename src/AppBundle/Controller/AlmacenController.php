<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 12/11/2018
 * Time: 11:28 AM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Solicitud;
use AppBundle\Extra\FacturacionHelper;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
    public function indexAction(Request $request, DataTablesInterface $dataTables)
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
    public function showAction(Solicitud $solicitud)
    {
        return $this->render('almacen/show.html.twig',[
            'title' => 'Detalle almacén',
            'solicitud' => $solicitud
        ]);
    }

    /**
     * Lists all marinaHumedaServicio entities.
     *
     * @Route("/inventario/puerto-mujeres", name="almacen_inventario-marina")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function inventarioMarinaAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'inventario_marina');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }
        return $this->render('almacen/inventario/marina.html.twig', ['title' => 'Inventario Puerto Mujeres']);
    }

    /**
     * @Route("/inventario/servicios-marinos", name="almacen_inventario-combustible")
     * @Method("GET")
     */
    public function inventarioCombustibleAction()
    {
        $em = $this->getDoctrine()->getManager();
        $catalogo = $em->getRepository('AppBundle:Combustible\Catalogo')->findAll();
        return $this->render('almacen/inventario/combustible.html.twig', [
            'catalogo' => $catalogo,
            'title' => 'Inventario Servicios Marinos'
        ]);
    }

    /**
     * Lists all producto entities.
     *
     * @Route("/inventario/astillero", name="almacen_inventario-astillero")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function inventarioAstilleroAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'inventario_astillero');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }

        return $this->render('almacen/inventario/astillero.html.twig', ['title' => 'Inventario Astillero']);
    }

    /**
     * @Route("/inventario/tienda", name="almacen_inventario-tienda")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function inventarioTiendaAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'inventario_tienda');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }
        return $this->render('almacen/inventario/tienda.html.twig',['title' => 'Inventario V&V Store']);
    }

    /**
     * @Route("/{id}/validar", name="almacen_validar")
     * @Method({"GET", "POST"})
     */
    public function validarAction(Request $request, Solicitud $solicitud)
    {
        $em = $this->getDoctrine()->getManager();
        $this->denyAccessUnlessGranted('ALMACEN_VALIDAR',$solicitud);

        if($solicitud->getValidadoCompra() === false || $solicitud->getValidadoAlmacen()){ throw new NotFoundHttpException(); }

        $clonConceptos = new ArrayCollection();
        foreach ($solicitud->getConceptos() as $concepto){
            $clonConceptos->add(clone $concepto);
        }
        $editForm = $this->createForm('AppBundle\Form\Almacen\ValidarType',$solicitud);
        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            foreach ($solicitud->getConceptos() as $concepto){
                foreach ($clonConceptos as $clonConcepto){
                    if($concepto->getId() === $clonConcepto->getId()){
                        //Sí es un concepto validado actualmente y antes no estaba validado entonces se suman las existencias
                        if($concepto->getValidadoAlmacen() && $clonConcepto->getValidadoAlmacen() !== true){
                            $repositorio = FacturacionHelper::getProductoRepositoryByEmpresa($this->getDoctrine()->getManager(),$solicitud->getEmpresa()->getId());
                            $objetoProducto = $em->getRepository('AppBundle:Solicitud')->seleccionaObjetoProducto($concepto);
                            $producto = $repositorio->findOneBy(['id' => $objetoProducto->getId()]);
                            $producto->setExistencia($producto->getExistencia() + $concepto->getCantidad());
                            $concepto->setNombreValidoAlmacen($this->getUser()->getNombre());
                            $concepto->setFechaValidoAlmacen(new \DateTime('now'));
                            $em->persist($concepto);
                        }
                    }
                }
            }
            if($solicitud->getValidadoAlmacen()){
                $solicitud->setNombreValidoAlmacen($this->getUser()->getNombre());
                $solicitud->setFechaValidoAlmacen(new \DateTime('now'));
            }
            $em->persist($solicitud);
            $em->flush();
            return $this->redirectToRoute('almacen_show',['id' => $solicitud->getId()]);
        }
        return $this->render('almacen/validar.html.twig',[
            'form' => $editForm->createView(),
            'solicitud' => $solicitud,
            'title' => 'Almacen - Validar'
        ]);
    }
}