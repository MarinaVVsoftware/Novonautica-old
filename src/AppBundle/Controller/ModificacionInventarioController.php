<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/20/18
 * Time: 12:48 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use AppBundle\Entity\ModificacionInventario;
use AppBundle\Extra\FacturacionHelper;
use AppBundle\Form\ModificacionInventarioType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ModificacionInventarioController
 * @package AppBundle\Controller
 *
 * @Route("almacen/inventario/modificar")
 */
class ModificacionInventarioController extends AbstractController
{

    /**
     * @Route("/listado", name="almacen_modificacion_index", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render(
            'modificacioninventario/index.html.twig',
            [
                'title' => 'Registro de modificaciones de inventario',
            ]
        );
    }

    /**
     * @Route("/index-data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'modificacion_inventario');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/nueva", name="almacen_modificacion_new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $empresaId = $request->query->get('e');
        $empresa = $em->getRepository(Emisor::class)->find($empresaId);
        $modificacion = new ModificacionInventario($empresa, $this->getUser());

        $form = $this->createForm(
            ModificacionInventarioType::class,
            $modificacion,
            [
                'action' => $this->generateUrl('almacen_modificacion_new', ['e' => $empresaId]),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productoRepository = FacturacionHelper::getProductoRepositoryByEmpresa($em, $empresaId);

            foreach ($modificacion->getConceptos() as $concepto) {
                $producto = $productoRepository->find($concepto['producto']);
                $producto->setExistencia($concepto['existencia']);

                $em->persist($producto);
            }

            $em->persist($modificacion);
            $em->flush();

            return $this->redirectToRoute('almacen_modificacion_new', ['e' => $empresaId]);
        }

        return $this->render(
            'modificacioninventario/new.html.twig',
            [
                'empresa' => $empresa,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/productos", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getProductosAction(Request $request)
    {
        $empresaId = $request->query->get('e');
        $query = $request->query->get('search');

        if (!$query) {
            return $this->json(['results' => []]);
        }

        $em = $this->getDoctrine()->getManager();

        $productoRepository = FacturacionHelper::getProductoRepositoryByEmpresa($em, $empresaId);
        $productos = $productoRepository->getProductoSelect2($query);

        return $this->json(
            [
                'results' => $productos,
            ]
        );
    }

    /**
     * @Route("/quantity", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public
    function getProductoQuantity(
        Request $request
    ) {
        $empresaId = $request->query->get('e');
        $productoId = $request->query->get('p');

        if (!$productoId) {
            return $this->json(['results' => []]);
        }

        $em = $this->getDoctrine()->getManager();

        $productoRepository = FacturacionHelper::getProductoRepositoryByEmpresa($em, $empresaId);
        $producto = $productoRepository->find($productoId);

        return $this->json(
            [
                'results' => (array)$producto,
            ]
        );
    }

    /**
     * @Route("/{id}", name="almacen_modificacion_show")
     *
     * @param ModificacionInventario $modificacionInventario
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ModificacionInventario $modificacionInventario)
    {
        $em = $this->getDoctrine()->getManager();
        $productoRepository = FacturacionHelper::getProductoRepositoryByEmpresa(
            $em,
            $modificacionInventario->getEmpresa()->getId()
        );

        $conceptos = [];

        foreach ($modificacionInventario->getConceptos() as $i => $concepto) {
            $conceptos[] =
                [
                    'existencia' => $concepto['existencia'],
                    'producto' => $productoRepository->find($concepto['producto']),
                ];
        }

        return $this->render(
            'modificacioninventario/show.html.twig',
            [
                'title' => 'Detalle de modificación de inventario',
                'modificacion' => $modificacionInventario,
                'conceptos' => $conceptos,
            ]
        );
    }
}
