<?php

namespace AppBundle\Controller\Astillero;

use AppBundle\Entity\Astillero\Producto;
use Proxies\__CG__\AppBundle\Entity\Astillero\Servicio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Producto controller.
 *
 * @Route("astillero/producto")
 */
class ProductoController extends Controller
{
    /**
     * Lists all producto entities.
     *
     * @Route("/", name="astillero_producto_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $productos = $em->getRepository('AppBundle:Astillero\Producto')->findAll();

        return $this->render('astillero/producto/index.html.twig', array(
            'productos' => $productos,
            'title' => 'Astillero Productos'
        ));
    }

    /**
     * Creates a new producto entity.
     *
     * @Route("/nuevo", name="astillero_producto_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $producto = new Producto();
        $form = $this->createForm('AppBundle\Form\Astillero\ProductoType', $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($producto);
            $em->flush();

            return $this->redirectToRoute('astillero_producto_index');
        }

        return $this->render('astillero/producto/new.html.twig', array(
            'producto' => $producto,
            'form' => $form->createView(),
            'title' => 'Astillero Nuevo Producto'
        ));
    }
    /**
     * @Route("/buscarproducto/{id}.{_format}", name="ajax_astillero_busca_producto", defaults={"_format"="JSON"})
     *
     */
    public function buscarAction(Request $request, Producto $producto){
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizer->setIgnoredAttributes(['ACotizacionesServicios']);
        $normalizers = [$normalizer];
        $serializer = new Serializer($normalizers, $encoders);
        return new Response($producto = $serializer->serialize($producto,$request->getRequestFormat()));
    }

    /**
     * Finds and displays a producto entity.
     *
     * @Route("/{id}", name="astillero_producto_show")
     * @Method("GET")
     */
    public function showAction(Producto $producto)
    {
        $deleteForm = $this->createDeleteForm($producto);

        return $this->render('astillero/producto/show.html.twig', array(
            'producto' => $producto,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Astillero Detalle Producto',
        ));
    }

    /**
     * Displays a form to edit an existing producto entity.
     *
     * @Route("/{id}/editar", name="astillero_producto_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Producto $producto)
    {
        $deleteForm = $this->createDeleteForm($producto);
        $editForm = $this->createForm('AppBundle\Form\Astillero\ProductoType', $producto);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('astillero_producto_index');
        }

        return $this->render('astillero/producto/edit.html.twig', array(
            'producto' => $producto,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Astillero Editar Producto'
        ));
    }

    /**
     * Deletes a producto entity.
     *
     * @Route("/{id}", name="astillero_producto_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Producto $producto)
    {
        $form = $this->createDeleteForm($producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($producto);
            $em->flush();
        }

        return $this->redirectToRoute('astillero_producto_index');
    }

    /**
     * Creates a form to delete a producto entity.
     *
     * @param Producto $producto The producto entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Producto $producto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('astillero_producto_delete', array('id' => $producto->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
