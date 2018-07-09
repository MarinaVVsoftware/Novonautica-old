<?php
/**
 * User: inrumi
 * Date: 7/5/18
 * Time: 15:49
 */

namespace AppBundle\Controller\Contabilidad\Egreso\Entrada;


use AppBundle\Entity\Contabilidad\Egreso\Entrada\Concepto;
use AppBundle\Form\Contabilidad\Egreso\Entrada\ConceptoType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConceptoController
 * @package AppBundle\Controller\Contabilidad\Egreso\Entrada
 * @Route("/contabilidad/egreso/entrada/concepto")
 */
class ConceptoController extends AbstractController
{
    /**
     * @Route("/", name="contabilidad_egreso_entrada_concepto_index")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $conceptoRepository = $em->getRepository(Concepto::class);

        $concepto = $request->query->get('c') ?: null;
        $concepto = $concepto ? $conceptoRepository->find($concepto) : new Concepto();

        $form = $this->createForm(ConceptoType::class, $concepto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($concepto);
            $em->flush();

            return $this->redirect(
                $request->headers->get('referer')
            );
        }

        return $this->render('contabilidad/egreso/entrada/concepto.html.twig', [
            'title' => 'Conceptos de egresos',
            'concepto' => $concepto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/conceptos.json")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'entrada/concepto');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
