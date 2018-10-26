<?php
/**
 * User: inrumi
 * Date: 7/5/18
 * Time: 16:20
 */

namespace AppBundle\Controller\Contabilidad;


use AppBundle\Entity\Contabilidad\Egreso;
use AppBundle\Form\Contabilidad\EgresoType;
use AppBundle\Repository\Contabilidad\Egreso\getEgresos;
use AppBundle\Repository\Contabilidad\EgresoRepository;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $em = $this->getDoctrine()->getManager();
        $iva = $em->getRepository('AppBundle:ValorSistema')->findOneBy(['id' => 1]);
        $egreso->setIva($iva->getIva());
        $form = $this->createForm(EgresoType::class, $egreso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($egreso);
            $em->flush();

            return $this->redirectToRoute('contabilidad_egreso_index');
        }

        return $this->render(
            'contabilidad/egreso/new.html.twig',
            [
                'title' => 'Nuevo egreso',
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", name="contabilidad_egreso_edit")
     * @param Request $request
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $egresoRepository = $this->getDoctrine()->getRepository(Egreso::class);
        $egreso = $egresoRepository->get($id);

        $this->denyAccessUnlessGranted('edit', $egreso);

        if (null === $egreso) {
            throw new NotFoundHttpException();
        }

        $clonedEntries = clone $egreso->getEntradas();
        $clonedEntries = $clonedEntries->toArray();

        $form = $this->createForm(EgresoType::class, $egreso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $egreso->setUpdateAt(new \DateTime());

            foreach ($clonedEntries as $clonedEntry) {
                if (!$egreso->getEntradas()->contains($clonedEntry)) {
                    $em->remove($clonedEntry);
                }
            }

            $em->persist($egreso);
            $em->flush();

            return $this->redirectToRoute('contabilidad_egreso_show', ['id' => $egreso->getId()]);
        }

        return $this->render(
            'contabilidad/egreso/new.html.twig',
            [
                'title' => 'Editar Egreso',
                'form' => $form->createView(),
                'egreso' => $egreso
            ]
        );
    }

    /**
     * @Route("/conceptos", name="contabilidad_egreso_conceptos")
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
     * @Route("/proveedores", name="contabilidad_egreso_proveedores")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getProveedorAction(Request $request)
    {
        $q = $request->query->get('q');
        $conceptoRepository = $this->getDoctrine()->getRepository(Egreso\Entrada\Proveedor::class);

        return $this
            ->json(
                ['results' => $conceptoRepository->getProveedorLike($q)],
                JsonResponse::HTTP_OK
            )
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    /**
     * @Route("/{id}", name="contabilidad_egreso_show")
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $egresoRepository = $this->getDoctrine()->getRepository(Egreso::class);
        $egreso = $egresoRepository->get($id);

        $this->denyAccessUnlessGranted('view', $egreso);

        if (null === $egreso) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'contabilidad/egreso/show.html.twig',
            [
                'title' => 'Detalle de egreso',
                'egreso' => $egreso,
            ]
        );
    }
}
