<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2018-12-09
 * Time: 00:44
 */

namespace AppBundle\Controller\JRFMarine;


use AppBundle\Entity\JRFMarine\Categoria;
use AppBundle\Entity\JRFMarine\Categoria\Subcategoria;
use AppBundle\Form\JRFMarine\Categoria\SubcategoriaType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SubcategoriaController
 * @package AppBundle\Controller\JRFMarine
 *
 * @Route("/jrfmarine/subcategorias")
 */
class SubcategoriaController extends AbstractController
{
    /**
     * @Route("/", name="jrfmarine_subcategorias_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $subcategoriaRepository = $em->getRepository(Subcategoria::class);

        $subcategoria = $request->query->get('subcategoria') ?: null;
        $subcategoria = $subcategoria ? $subcategoriaRepository->find($subcategoria) : new Subcategoria();

        if (
            $request->query->get('categoria')
            && null === $subcategoria->getCategoria()
        ) {
            $subcategoria->setCategoria(
                $em->getRepository(Categoria::class)->find(
                    $request->query->get('categoria')
                )
            );
        }

        $form = $this->createForm(SubcategoriaType::class, $subcategoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subcategoria);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(
            'jrfmarine/subcategoria/index.html.twig',
            [
                'title' => 'Categorias',
                'subcategoria' => $subcategoria,
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
            $results = $dataTables->handle($request, 'jrfmarine/subcategorias');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
