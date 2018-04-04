<?php

namespace AppBundle\Controller\Astillero;

use AppBundle\Entity\Astillero\Producto;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use DataTables\DataTablesInterface;
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
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'AstilleroProducto');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
            }

        return $this->render('astillero/producto/index.html.twig', ['title' => 'Astillero Productos']);
    }

    /**
     * Creates a new producto entity.
     *
     * @Route("/nuevo", name="astillero_producto_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
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

        return $this->render('astillero/producto/new.html.twig', [
            'producto' => $producto,
            'form' => $form->createView(),
            'title' => 'Astillero Nuevo Producto'
        ]);
    }

    /**
     * @Route("/buscarproducto/{id}.{_format}", name="ajax_astillero_busca_producto", defaults={"_format"="JSON"})
     *
     * @param Request $request
     * @param Producto $producto
     *
     * @return Response
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
     * @param Producto $producto
     * @return Response
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
     *
     *
     * @param Request $request
     * @param Producto $producto
     *
     * @return RedirectResponse|Response
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
     *
     * @param Request $request
     * @param Producto $producto
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Producto $producto)
    {
        $form = $this->createDeleteForm($producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $em = $this->getDoctrine()->getManager();
                $em->remove($producto);
                $em->flush();
                return $this->redirectToRoute('astillero_producto_index');
            }catch (ForeignKeyConstraintViolationException $e){
                $this->addFlash('error','Error!, No se puede borrar este producto, esta siendo utilizado en las cotizaciones');
            }

        }
        return $this->redirectToRoute('astillero_producto_edit',['id'=>$producto->getId()]);
    }

    /**
     * Creates a form to delete a producto entity.
     *
     * @param Producto $producto The producto entity
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Producto $producto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('astillero_producto_delete', ['id' => $producto->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
