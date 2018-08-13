<?php
/**
 * User: inrumi
 * Date: 6/25/18
 * Time: 15:44
 */

namespace AppBundle\Controller\Tienda\Inventario;


use AppBundle\Entity\Tienda\Inventario\Registro;
use AppBundle\Entity\Tienda\Producto;
use AppBundle\Form\Tienda\Inventario\RegistroType;
use AppBundle\Repository\Tienda\Inventario\AllInventoryData;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistroController
 * @package AppBundle\Controller\Tienda\Inventario
 *
 * @Route("/tienda/inventario/registro")
 */
class RegistroController extends AbstractController
{
    /**
     * @Route("/", name="tienda_inventario_index")
     */
    public function indexAction()
    {
        return $this->render(
            'tienda/inventario/index.html.twig',
            ['title' => 'Inventario de tienda',]
        );
    }

    /**
     * @Route("/inventario.json")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function indexData(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'inventario');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/entradas", name="tienda_inventario_registro_index_entrada")
     * @Route("/salidas", name="tienda_inventario_registro_index_salida")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registrosAction(Request $request)
    {
        $tipo = $this->getType($request);

        return $this->render(
            'tienda/inventario/registros.html.twig',
            [
                'title' => "Listado de {$tipo}s",
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * @Route("/registros.json")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function registrosData(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'registros');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/productos.json", name="tienda_inventario_registro_productos")
     *
     * @return JsonResponse
     */
    public function getProductosAction(Request $request)
    {
        $query = $request->query->get('q');
        $productoRepository = $this->getDoctrine()->getRepository(Producto::class);

        $response = [
            'results' => $productoRepository->findProductosLike($query),
        ];

        return $this->json(
            $response,
            JsonResponse::HTTP_OK
        )
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/entradas/new", name="tienda_inventario_registro_new_entrada")
     * @Route("/salidas/new", name="tienda_inventario_registro_new_salida")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $tipo = $this->getType($request);

        $entrada = new Registro\Entrada();

        $registro = new Registro();
        $registro->addEntrada($entrada);
        $registro->setTipo($tipo === 'entrada' ? Registro::TIPO_ENTRADA : Registro::TIPO_SALIDA);

        $form = $this->createForm(RegistroType::class, $registro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($registro);
            $em->flush();

            return $this->redirectToRoute(
                "tienda_inventario_registro_{$tipo}_show",
                [
                    'id' => $registro->getId(),
                ]
            );
        }

        return $this->render(
            'tienda/inventario/new.html.twig',
            [
                'title' => "Registro de ${tipo}",
                'form' => $form->createView(),
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * @Route("/entradas/{id}/edit", name="tienda_inventario_registro_entrada_edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroRepostory = $em->getRepository(Registro::class);

        $registro = $registroRepostory->get($id);

        $this->denyAccessUnlessGranted('edit', $registro);

        if (null === $registro) {
            throw new NotFoundHttpException();
        }

        $clonedEntries = clone $registro->getEntradas();
        $clonedEntries = $clonedEntries->toArray();

        $form = $this->createForm(RegistroType::class, $registro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($clonedEntries as $clonedEntry) {
                if (!$registro->getEntradas()->contains($clonedEntry)) {
                    $em->remove($clonedEntry);
                }
            }

            $em->persist($registro);
            $em->flush();


            return $this->redirectToRoute(
                'tienda_inventario_registro_entrada_show',
                [
                    'id' => $registro->getId(),
                ]
            );
        }

        return $this->render(
            'tienda/inventario/new.html.twig',
            [
                'title' => 'Edicion de entrada',
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/entradas/{id}", name="tienda_inventario_registro_entrada_show")
     * @Route("/salidas/{id}", name="tienda_inventario_registro_salida_show")
     *
     * @param Registro $registro
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroRepostory = $em->getRepository(Registro::class);

        $registro = $registroRepostory->get($id);

        return $this->render(
            'tienda/inventario/show.html.twig',
            [
                'title' => 'Conceptos',
                'registro' => $registro,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getType(Request $request)
    {
        $type = explode('_', $request->get('_route'));

        return array_pop($type);
    }
}
