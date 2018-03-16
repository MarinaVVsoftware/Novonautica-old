<?php

namespace AppBundle\Controller\Astillero;

use AppBundle\Entity\Astillero\Proveedor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Proveedor controller.
 *
 * @Route("astillero/proveedor")
 */
class ProveedorController extends Controller
{
    /**
     * Lists all proveedor entities.
     *
     * @Route("/", name="astillero_proveedor_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        if($request->isXmlHttpRequest()){
            try{
                $datatables = $this->get('datatables');
                $results = $datatables->handle($request,'AstilleroProveedor');
                return $this->json($results);
            }catch (HttpException $e){
                return $this->json($e->getMessage(),$e->getStatusCode());
            }
        }
//        $em = $this->getDoctrine()->getManager();
//        $proveedors = $em->getRepository('AppBundle:Astillero\Proveedor')->findAll();

        return $this->render('astillero/proveedor/index.html.twig', [
//            'proveedors' => $proveedors,
            'title' => 'Proveedores',
        ]);
    }

    /**
     * Creates a new proveedor entity.
     *
     * @Route("/nuevo", name="astillero_proveedor_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $proveedor = new Proveedor();
        $form = $this->createForm('AppBundle\Form\Astillero\ProveedorType', $proveedor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($proveedor);
            $em->flush();

            return $this->redirectToRoute('astillero_proveedor_index');
        }

        return $this->render('astillero/proveedor/new.html.twig', array(
            'proveedor' => $proveedor,
            'form' => $form->createView(),
            'title' => 'Nuevo proveedor'
        ));
    }

    /**
     * Finds and displays a proveedor entity.
     *
     * @Route("/{id}", name="astillero_proveedor_show")
     * @Method("GET")
     */
    public function showAction(Proveedor $proveedor)
    {
        $deleteForm = $this->createDeleteForm($proveedor);

        return $this->render('astillero/proveedor/show.html.twig', array(
            'proveedor' => $proveedor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing proveedor entity.
     *
     * @Route("/{id}/editar", name="astillero_proveedor_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Proveedor $proveedor)
    {
        $deleteForm = $this->createDeleteForm($proveedor);
        $editForm = $this->createForm('AppBundle\Form\Astillero\ProveedorType', $proveedor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('astillero_proveedor_index');
        }

        return $this->render('astillero/proveedor/edit.html.twig', array(
            'proveedor' => $proveedor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Editar Proveedor'
        ));
    }

    /**
     * Deletes a proveedor entity.
     *
     * @Route("/{id}", name="astillero_proveedor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Proveedor $proveedor)
    {
        $form = $this->createDeleteForm($proveedor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($proveedor);
            $em->flush();
        }

        return $this->redirectToRoute('astillero_proveedor_index');
    }

    /**
     * Creates a form to delete a proveedor entity.
     *
     * @param Proveedor $proveedor The proveedor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Proveedor $proveedor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('astillero_proveedor_delete', array('id' => $proveedor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
