<?php

namespace AppBundle\Controller\Producto;

use AppBundle\Entity\Producto\Subcategoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Subcategorium controller.
 *
 * @Route("producto/subcategoria")
 */
class SubcategoriaController extends Controller
{
    /**
     * @Route("/", name="producto_subcategoria")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $em = $this->getDoctrine()->getManager();
        $subCategoriasRepo = $em->getRepository('AppBundle:Producto\Subcategoria');

        $query = $request->query;
        $page = (int)$query->get('page') ?: 1;
        $length = (int)$query->get('length') ?: 10;

        $categoria = (int)$query->get('categoria') ?: null;
        $categoria = $categoria ? $em->getRepository('AppBundle:Producto\Categoria')->find($categoria) : null;

        $subcategoria = (int)$query->get('subcategoria') ?: null;
        $subcategoria = $subcategoria ? $subCategoriasRepo->find($subcategoria) : new Subcategoria();

        if (!$subcategoria->getCategoria()) {
            $subcategoria->setCategoria($categoria);
        }

        $form = $this->createForm('AppBundle\Form\Producto\SubcategoriaType', $subcategoria);
        $form->handleRequest($request);

        $paginacion = $subCategoriasRepo->paginacion($page, $length, 'DESC', $categoria);
        $subcategorias = $paginacion->getQuery()->getResult();

        $pages = ceil($paginacion->count() / $length);

        $deleteForms = [];
        foreach ($subcategorias as $formSubcategoria) {
            $deleteForms[] = $this->createDeleteForm($formSubcategoria)->createView();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subcategoria);
            $em->flush();

            return $this->redirectToRoute('producto_subcategoria');
        }

        return $this->render(':producto/subcategoria:index.html.twig', [
            'title' => 'Subcategorías',
            'subcategoria' => $subcategoria,
            'subcategorias' => $subcategorias,
            'categoria' => $categoria,
            'form' => $form->createView(),
            'deleteForms' => $deleteForms,
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    /**
     * Deletes a subcategorium entity.
     *
     * @Route("/{id}", name="producto_subcategoria_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Subcategoria $subcategorium)
    {
        $form = $this->createDeleteForm($subcategorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($subcategorium);
            $em->flush();
        }

        return $this->redirectToRoute('producto_subcategoria');
    }

    /**
     * Creates a form to delete a subcategorium entity.
     *
     * @param Subcategoria $subcategorium The subcategorium entity
     *
     * @return Form|FormInterface
     */
    private function createDeleteForm(Subcategoria $subcategorium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('producto_subcategoria_delete', ['id' => $subcategorium->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
