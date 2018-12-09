<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2018-12-09
 * Time: 00:23
 */

namespace AppBundle\Controller\JRFMarine;


use AppBundle\Entity\JRFMarine\Categoria;
use AppBundle\Form\JRFMarine\CategoriaType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoriaController
 * @package AppBundle\Controller\JRFMarine
 *
 * @Route("/jrfmarine/categorias")
 */
class CategoriaController extends AbstractController
{
    /**
     * @Route("/", name="jrfmarine_categorias_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $categoriaRepository = $em->getRepository(Categoria::class);

        $categoria = $request->query->get('categoria') ?: null;
        $categoria = $categoria ? $categoriaRepository->find($categoria) : new Categoria();

        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categoria);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(
            'jrfmarine/categoria/index.html.twig',
            [
                'title' => 'Categorias',
                'categoria' => $categoria,
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
            $results = $dataTables->handle($request, 'jrfmarine/categorias');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
