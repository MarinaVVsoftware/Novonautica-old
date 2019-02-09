<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2019-02-07
 * Time: 09:33
 */

namespace AppBundle\Controller\Combustible;


use AppBundle\Entity\Combustible\TipoPago;
use AppBundle\Form\Combustible\TipoPagoType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/combustible/tipopago")
 *
 * Class TipoPagoController
 * @package AppBundle\Controller\Combustible
 */
class TipoPagoController extends AbstractController
{
    /**
     * @Route("/", name="combustible-tipopago-index")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tipoPagoRepository = $em->getRepository(TipoPago::class);

        $tipoPago = $request->query->get('tipo') ?: null;
        $tipoPago = $tipoPago ? $tipoPagoRepository->find($tipoPago) : new TipoPago();

        $form = $this->createForm(TipoPagoType::class, $tipoPago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tipoPago);
            $em->flush();

            return $this->redirectToRoute('combustible-tipopago-index');
        }

        return $this->render(':combustible/tipopago:index.html.twig', [
            'title' => 'Tipos de pago',
            'tipopago' => $tipoPago,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'combustible/tipopago');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/eliminar/{id}")
     *
     * @param TipoPago $tipoPago
     *
     * @return RedirectResponse
     */
    public function deleteAction(TipoPago $tipoPago)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($tipoPago);
        $em->flush();

        return $this->redirectToRoute('combustible-tipopago-index');
    }
}
