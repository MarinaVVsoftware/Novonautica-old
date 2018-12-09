<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2018-12-09
 * Time: 00:00
 */

namespace AppBundle\Controller\JRFMarine;


use AppBundle\Entity\JRFMarine\Marca;
use AppBundle\Form\JRFMarine\MarcaType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MarcaController
 * @package AppBundle\Controller\JRFMarine
 *
 * @Route("/jrfmarine/marcas")
 */
class MarcaController extends AbstractController
{
    /**
     * @Route("/", name="jrfmarine_marcas_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $marcaRepository = $em->getRepository(Marca::class);

        $marca = $request->query->get('marca') ?: null;
        $marca = $marca ? $marcaRepository->find($marca) : new Marca();

        $form = $this->createForm(MarcaType::class, $marca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($marca);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(
            'jrfmarine/marca/index.html.twig',
            [
                'title' => 'Marcas',
                'marca' => $marca,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'jrfmarine/marcas');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
