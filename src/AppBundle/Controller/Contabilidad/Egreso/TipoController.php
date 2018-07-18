<?php
/**
 * User: inrumi
 * Date: 7/18/18
 * Time: 11:55
 */

namespace AppBundle\Controller\Contabilidad\Egreso;


use AppBundle\Entity\Contabilidad\Egreso\Tipo;
use AppBundle\Form\Contabilidad\Egreso\TipoType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TipoController
 * @package AppBundle\Controller\Contabilidad\Egreso
 * @Route("/contabilidad/egreso/tipo")
 */
class TipoController extends AbstractController
{
    /**
     * @Route("/", name="contabilidad_egreso_tipo_index")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tipoRepository = $em->getRepository(Tipo::class);

        $tipo = $request->query->get('t') ?: null;
        $tipo = $tipo ? $tipoRepository->find($tipo) : new Tipo();

        $form = $this->createForm(TipoType::class, $tipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tipo);
            $em->flush();

            return $this->redirect(
                $request->headers->get('referer')
            );
        }

        return $this->render(
            'contabilidad/egreso/tipo/index.html.twig',
            [
                'title' => 'Proveedores de egresos',
                'tipo' => $tipo,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/tipos.json")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'egreso/tipo');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
