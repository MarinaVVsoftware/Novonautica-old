<?php

namespace AppBundle\Controller\Producto;

use AppBundle\Entity\Producto\Marca;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Marca controller.
 *
 * @Route("producto/marca")
 */
class MarcaController extends Controller
{
    /**
     * @Route("/", name="producto_marca")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $em = $em = $this->getDoctrine()->getManager();
        $marcasRepo = $em->getRepository('AppBundle:Producto\Marca');

        $query = $request->query;
        $page = (int)$query->get('page') ?: 1;
        $length = (int)$query->get('length') ?: 10;
        $marca = (int)$query->get('marca') ?: null;

        $marca = $marca ? $marcasRepo->find($marca) : new Marca();

        $form = $this->createForm('AppBundle\Form\Producto\MarcaType', $marca);
        $form->handleRequest($request);

        $paginacion = $marcasRepo->paginacion($page, $length);
        $marcas = $paginacion->getQuery()->getResult();

        $pages = ceil($paginacion->count() / $length);

        $deleteForms = [];
        foreach ($marcas as $formMarca) {
            $deleteForms[] = $this->createDeleteForm($formMarca)->createView();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($marca);
            $em->flush();

            return $this->redirectToRoute('producto_marca');
        }

        return $this->render(':producto/marca:index.html.twig', [
            'title' => 'Marcas',
            'marca' => $marca,
            'marcas' => $marcas,
            'form' => $form->createView(),
            'deleteForms' => $deleteForms,
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    /**
     * Deletes a marca entity.
     *
     * @Route("/{id}", name="producto_marca_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Marca $marca)
    {
        $form = $this->createDeleteForm($marca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marca);
            $em->flush();
        }

        return $this->redirectToRoute('producto_marca_index');
    }

    /**
     * Creates a form to delete a marca entity.
     *
     * @param Marca $marca The marca entity
     *
     * @return FormInterface
     */
    private function createDeleteForm(Marca $marca)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('producto_marca_delete', ['id' => $marca->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
