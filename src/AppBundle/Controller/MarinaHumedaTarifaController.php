<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaTarifa;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Marinahumedatarifa controller.
 *
 * @Route("/marina/cotizacion/estadia-tarifas")
 */
class MarinaHumedaTarifaController extends Controller
{
    /**
     * Lists all marinaHumedaTarifa entities.
     *
     * @Route("/", name="marinahumeda-tarifas_index")
     * @Method("GET")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'marinaTarifa');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }
        return $this->render('marinahumeda/tarifa/index.html.twig', ['title' => 'Tarifas']);
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
//            $em->persist($marinaHumedaTarifa);
//            $em->flush();
//            return $this->redirectToRoute('marinahumeda-tarifas_index');

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

        return $this->render('marinahumeda/tarifa/new.html.twig', [
            'title' => 'Editar tarifa',
            'marinaHumedaTarifa' => $marinaHumedaTarifa,
            'form' => $editForm->createView(),
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
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(MarinaHumedaTarifa $marinaHumedaTarifa)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marinahumeda-tarifas_delete', ['id' => $marinaHumedaTarifa->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
