<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaServicio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use DataTables\DataTablesInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Marinahumedaservicio controller.
 *
 * @Route("/marina/servicios-adicionales/catalogo")
 */
class MarinaHumedaServicioController extends Controller
{
    /**
     * Lists all marinaHumedaServicio entities.
     *
     * @Route("/", name="marina-humeda-servicio_index")
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
                $results = $dataTables->handle($request, 'MarinaServicioAdicional');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }
        return $this->render('marinahumeda/servicio/index.html.twig', ['title' => 'Catálogo productos/servicios']);
    }

    /**
     * Creates a new marinaHumedaServicio entity.
     *
     * @Route("/nuevo", name="marina-humeda-servicio_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $marinaHumedaServicio = new Marinahumedaservicio();
        $form = $this->createForm('AppBundle\Form\MarinaHumedaServicioType', $marinaHumedaServicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($marinaHumedaServicio);
            $em->flush();

            return $this->redirectToRoute('marina-humeda-servicio_index');
        }

        return $this->render('marinahumeda/servicio/new.html.twig', [
            'title' => 'Nuevo servicio',
            'marinaHumedaServicio' => $marinaHumedaServicio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a marinaHumedaServicio entity.
     *
     * @Route("/{id}", name="marina-humeda-servicio_show")
     * @Method("GET")
     */
    public function showAction(MarinaHumedaServicio $marinaHumedaServicio)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaServicio);

        return $this->render('marinahumeda/servicio/show.html.twig', [
            'title' => 'Catálogo',
            'marinaHumedaServicio' => $marinaHumedaServicio,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing marinaHumedaServicio entity.
     *
     * @Route("/{id}/editar", name="marina-humeda-servicio_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MarinaHumedaServicio $marinaHumedaServicio)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaServicio);
        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaServicioType', $marinaHumedaServicio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marina-humeda-servicio_index');
        }

        return $this->render('marinahumeda/servicio/edit.html.twig', [
            'title' => 'Editar servicio',
            'marinaHumedaServicio' => $marinaHumedaServicio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a marinaHumedaServicio entity.
     *
     * @Route("/{id}", name="marina-humeda-servicio_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MarinaHumedaServicio $marinaHumedaServicio)
    {
        $form = $this->createDeleteForm($marinaHumedaServicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marinaHumedaServicio);
            $em->flush();
        }

        return $this->redirectToRoute('marina-humeda-servicio_index');
    }

    /**
     * Creates a form to delete a marinaHumedaServicio entity.
     *
     * @param MarinaHumedaServicio $marinaHumedaServicio The marinaHumedaServicio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MarinaHumedaServicio $marinaHumedaServicio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda-servicio_delete', array('id' => $marinaHumedaServicio->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
