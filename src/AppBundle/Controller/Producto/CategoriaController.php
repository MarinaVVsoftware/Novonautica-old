<?php

namespace AppBundle\Controller\Producto;

use AppBundle\Entity\Producto\Categoria;
use AppBundle\Entity\Producto\Subcategoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Categorium controller.
 *
 * @Route("producto/categoria")
 */
class CategoriaController extends Controller
{
    /**
     * Creates a new categorium entity.
     *
     * @Route("/", name="producto_categoria")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $em = $this->getDoctrine()->getManager();
        $categoriasRepo = $em->getRepository('AppBundle:Producto\Categoria');

        $query = $request->query;
        $page = (int)$query->get('page') ?: 1;
        $length = (int)$query->get('length') ?: 10;
        $categoria = (int)$query->get('categoria') ?: null;

        $categoria = $categoria ? $categoriasRepo->find($categoria) : new Categoria();

        $form = $this->createForm('AppBundle\Form\Producto\CategoriaType', $categoria);
        $form->handleRequest($request);

        $paginacion = $categoriasRepo->paginacion($page, $length);
        $categorias = $paginacion->getQuery()->getResult();

        $pages = ceil($paginacion->count() / $length);

        $deleteForms = [];
        foreach ($categorias as $formCategoria) {
            $deleteForms[] = $this->createDeleteForm($formCategoria)->createView();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categoria);
            $em->flush();

            return $this->redirectToRoute('producto_categoria');
        }

        return $this->render(':producto/categoria:index.html.twig', [
            'title' => 'Categorías',
            'categoria' => $categoria,
            'categorias' => $categorias,
            'form' => $form->createView(),
            'deleteForms' => $deleteForms,
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    /**
     * @Route("/{id}/subcategorias.{_format}", name="embarcacion_marca_modelos_ajax", defaults={"_format" = "json"})
     *
     * @param Request $request
     * @param Categoria $categoria
     *
     * @return Response
     */
    public function subcategoriasAction(Request $request, Categoria $categoria) : Response
    {
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $normalizer->setIgnoredAttributes(['productos', 'categoria', 'imagen', 'imagenFile', 'updateAt']);

        $modelos = $serializer->serialize($categoria, $request->getRequestFormat());

        return new Response($modelos);
    }

    /**
     * Deletes a categorium entity.
     *
     * @Route("/{id}", name="producto_categoria_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Categoria $categorium)
    {
        $form = $this->createDeleteForm($categorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categorium);
            $em->flush();
        }

        return $this->redirectToRoute('producto_categoria');
    }

    /**
     * Creates a form to delete a categorium entity.
     *
     * @param Categoria $categorium The categorium entity
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Categoria $categorium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('producto_categoria_delete', ['id' => $categorium->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
