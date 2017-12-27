<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaTarifa;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Marinahumedatarifa controller.
 *
 * @Route("/marina/cotizacion/estadia/tarifas")
 */
class MarinaHumedaTarifaController extends Controller
{
    /**
     * Lists all marinaHumedaTarifa entities.
     *
     * @Route("/", name="marinahumeda-tarifas_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $marinaHumedaTarifas = $em->getRepository('AppBundle:MarinaHumedaTarifa')->findAll();

        return $this->render('marinahumeda/tarifa/index.html.twig', [
            'title' => 'Tarifas',
            'marinaHumedaTarifas' => $marinaHumedaTarifas
        ]);
    }

    /**
     * Creates a new marinaHumedaTarifa entity.
     *
     * @Route("/nueva", name="marinahumeda-tarifas_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $marinaHumedaTarifa = new Marinahumedatarifa();
        $form = $this->createForm('AppBundle\Form\MarinaHumedaTarifaType', $marinaHumedaTarifa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($marinaHumedaTarifa);
            $em->flush();

            return $this->redirectToRoute('marinahumeda-tarifas_index');
        }

        return $this->render('marinahumeda/tarifa/new.html.twig', [
            'title' => 'Nueva tarifa',
            'marinaHumedaTarifa' => $marinaHumedaTarifa,
            'form' => $form->createView()
        ]);
    }

    /**
     * Finds and displays a marinaHumedaTarifa entity.
     *
     * @Route("/{id}", name="marinahumeda-tarifas_show")
     * @Method("GET")
     */
    public function showAction(MarinaHumedaTarifa $marinaHumedaTarifa)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaTarifa);

        return $this->render('marinahumeda/tarifa/show.html.twig', [
            'title' => 'Tarifa',
            'marinaHumedaTarifa' => $marinaHumedaTarifa,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing marinaHumedaTarifa entity.
     *
     * @Route("/{id}/editar", name="marinahumeda-tarifas_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MarinaHumedaTarifa $marinaHumedaTarifa)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaTarifa);
        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaTarifaType', $marinaHumedaTarifa);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marinahumeda-tarifas_index');
        }

        return $this->render('marinahumeda/tarifa/edit.html.twig', [
            'title' => 'Editar tarifa',
            'marinaHumedaTarifa' => $marinaHumedaTarifa,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ]);
    }

    /**
     * Deletes a marinaHumedaTarifa entity.
     *
     * @Route("/{id}", name="marinahumeda-tarifas_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MarinaHumedaTarifa $marinaHumedaTarifa)
    {
        $form = $this->createDeleteForm($marinaHumedaTarifa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marinaHumedaTarifa);
            $em->flush();
        }

        return $this->redirectToRoute('marinahumeda-tarifas_index');
    }

    /**
     * Creates a form to delete a marinaHumedaTarifa entity.
     *
     * @param MarinaHumedaTarifa $marinaHumedaTarifa The marinaHumedaTarifa entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MarinaHumedaTarifa $marinaHumedaTarifa)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marinahumeda-tarifas_delete', array('id' => $marinaHumedaTarifa->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
