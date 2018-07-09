<?php
/**
 * User: inrumi
 * Date: 7/5/18
 * Time: 16:20
 */

namespace AppBundle\Controller\Contabilidad;


use AppBundle\Entity\Contabilidad\Egreso;
use AppBundle\Form\Contabilidad\EgresoType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EgresoController
 * @package AppBundle\Controller\Contabilidad
 * @Route("/contabilidad/egreso")
 */
class EgresoController extends AbstractController
{
    /**
     * @Route("/", name="contabilidad_egreso_index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render(
            'contabilidad/egreso/index.html.twig',
            [
                'title' => 'Listado de egresos',
            ]
        );
    }

    /**
     * @Route("/index-data")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function getIndexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'egreso');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/new", name="contabilidad_egreso_new")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $egreso = new Egreso();
        $egreso->setFecha(new \DateTime());

        $form = $this->createForm(EgresoType::class, $egreso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($egreso);
            $em->flush();

            return $this->redirectToRoute('contabilidad_egreso_index');
        }

        return $this->render(
            'contabilidad/egreso/new.html.twig',
            [
                'title' => 'Punto de venta',
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/conceptos")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getConceptoAction(Request $request)
    {
        $q = $request->query->get('q');
        $empresa = $request->query->get('empresa');
        $conceptoRepository = $this->getDoctrine()->getRepository(Egreso\Entrada\Concepto::class);

        return $this
            ->json(
                ['results' => $conceptoRepository->getConceptosByEmpresaAndNombre($empresa, $q)],
                JsonResponse::HTTP_OK
            )
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/{id}", name="contabilidad_egreso_Show")
     * @param Request $request
     * @param Egreso $egreso
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Egreso $egreso)
    {
        return $this->render(
            'contabilidad/egreso/show.html.twig',
            [
                'title' => 'Detalle de egreso',
                'egreso' => $egreso,
            ]
        );
    }
}
