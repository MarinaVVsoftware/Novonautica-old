<?php
/**
 * User: inrumi
 * Date: 7/5/18
 * Time: 15:49
 */

namespace AppBundle\Controller\Contabilidad\Egreso\Entrada;


use AppBundle\Entity\Contabilidad\Egreso\Entrada\Proveedor;
use AppBundle\Form\Contabilidad\Egreso\Entrada\ProveedorType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProveedorController
 * @package AppBundle\Controller\Contabilidad\Egreso\Entrada
 * @Route("/contabilidad/egreso/entrada/proveedor")
 */
class ProveedorController extends AbstractController
{
    /**
     * @Route("/", name="contabilidad_egreso_entrada_proveedor_index")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $proveedorRepository = $em->getRepository(Proveedor::class);

        $proveedor = $request->query->get('p') ?: null;
        $proveedor = $proveedor ? $proveedorRepository->find($proveedor) : new Proveedor();

        $form = $this->createForm(ProveedorType::class, $proveedor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($proveedor);
            $em->flush();

            return $this->redirect(
                $request->headers->get('referer')
            );
        }

        return $this->render('contabilidad/egreso/entrada/proveedor.html.twig', [
            'title' => 'Proveedores de egresos',
            'proveedor' => $proveedor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/proveedores.json")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'entrada/proveedor');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
