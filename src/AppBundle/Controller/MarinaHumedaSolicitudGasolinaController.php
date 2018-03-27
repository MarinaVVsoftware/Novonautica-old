<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaSolicitudGasolina;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Marinahumedasolicitudgasolina controller.
 *
 * @Route("/marina/cotizacion")
 */
class MarinaHumedaSolicitudGasolinaController extends Controller
{
    /**
     * Lists all marinaHumedaSolicitudGasolina entities.
     *
     * @Route("/gasolina/app", name="marinahumedasolicitud_gasolina_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('marinahumedasolicitudgasolina/index.html.twig', ['title' => 'Gasolina']);
    }

    /**
     * @Route("/gasolina/app/solicitudes", name="solicitud_index_data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSolicitudDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'appgasolina');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }


    /**
     * Finds and displays a marinaHumedaSolicitudGasolina entity.
     *
     * @Route("/{id}", name="marinahumedasolicitudgasolina_show")
     * @Method("GET")
     * @param MarinaHumedaSolicitudGasolina $marinaHumedaSolicitudGasolina
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(MarinaHumedaSolicitudGasolina $marinaHumedaSolicitudGasolina)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaSolicitudGasolina);

        return $this->render('marinahumedasolicitudgasolina/show.html.twig', array(
            'marinaHumedaSolicitudGasolina' => $marinaHumedaSolicitudGasolina,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing marinaHumedaSolicitudGasolina entity.
     *
     * @Route("/{id}/edit", name="marinahumedasolicitudgasolina_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MarinaHumedaSolicitudGasolina $marinaHumedaSolicitudGasolina)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaSolicitudGasolina);
        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaSolicitudGasolinaType', $marinaHumedaSolicitudGasolina);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marinahumedasolicitudgasolina_edit', array('id' => $marinaHumedaSolicitudGasolina->getId()));
        }

        return $this->render('marinahumedasolicitudgasolina/edit.html.twig', array(
            'marinaHumedaSolicitudGasolina' => $marinaHumedaSolicitudGasolina,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a marinaHumedaSolicitudGasolina entity.
     *
     * @Route("/{id}", name="marinahumedasolicitudgasolina_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MarinaHumedaSolicitudGasolina $marinaHumedaSolicitudGasolina)
    {
        $form = $this->createDeleteForm($marinaHumedaSolicitudGasolina);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marinaHumedaSolicitudGasolina);
            $em->flush();
        }

        return $this->redirectToRoute('marinahumedasolicitudgasolina_index');
    }

    /**
     * Creates a form to delete a marinaHumedaSolicitudGasolina entity.
     *
     * @param MarinaHumedaSolicitudGasolina $marinaHumedaSolicitudGasolina The marinaHumedaSolicitudGasolina entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(MarinaHumedaSolicitudGasolina $marinaHumedaSolicitudGasolina)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marinahumedasolicitudgasolina_delete', array('id' => $marinaHumedaSolicitudGasolina->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
