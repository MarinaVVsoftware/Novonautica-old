<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenDeTrabajo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ordendetrabajo controller.
 *
 * @Route("astillero/odt")
 */
class OrdenDeTrabajoController extends Controller
{
    /**
     * Lists all ordenDeTrabajo entities.
     *
     * @Route("/", name="ordendetrabajo_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ordenDeTrabajos = $em->getRepository('AppBundle:OrdenDeTrabajo')->findAll();
dump($ordenDeTrabajos);
        return $this->render('ordendetrabajo/index.html.twig', array(
            'ordenDeTrabajos' => $ordenDeTrabajos,
            'title' => 'Ordenes de trabajo'
        ));
    }

    /**
     * Creates a new ordenDeTrabajo entity.
     *
     * @Route("/nueva", name="ordendetrabajo_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ordenDeTrabajo = new Ordendetrabajo();
        $form = $this->createForm('AppBundle\Form\OrdenDeTrabajoType', $ordenDeTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ordenDeTrabajo);
            $em->flush();

            return $this->redirectToRoute('ordendetrabajo_index');
        }

        return $this->render('ordendetrabajo/new.html.twig', array(
            'ordenDeTrabajo' => $ordenDeTrabajo,
            'form' => $form->createView(),
            'title' => 'Nueva Orden de Trabajo'
        ));
    }

    /**
     * @Route("/buscarcotizacion", name="odt_busca_cotizacion")
     * @Method({"GET"})
     */
    public function buscarCotizacionAction(Request $request){
        $idcotizacion=$request->get('idcotizacion');
        $em = $this->getDoctrine()->getManager();

        $cotizacion = $em->getRepository('AppBundle:AstilleroCotizacion')
            ->createQueryBuilder('ac')
            ->select('ac','cliente','barco','AstilleroCotizaServicio','AstilleroServicioBasico','AstilleroProducto','AstilleroServicio')
            ->join('ac.cliente','cliente')
            ->join('ac.barco','barco')
            ->join('ac.acservicios','AstilleroCotizaServicio')
            ->leftJoin('AstilleroCotizaServicio.astilleroserviciobasico','AstilleroServicioBasico')
            ->leftJoin('AstilleroCotizaServicio.producto','AstilleroProducto')
            ->leftJoin('AstilleroCotizaServicio.servicio','AstilleroServicio')
            ->andWhere('ac.id = '.$idcotizacion)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $this->json($cotizacion);
    }

    /**
     * Finds and displays a ordenDeTrabajo entity.
     *
     * @Route("/{id}", name="ordendetrabajo_show")
     * @Method("GET")
     */
    public function showAction(OrdenDeTrabajo $ordenDeTrabajo)
    {
        $deleteForm = $this->createDeleteForm($ordenDeTrabajo);

        return $this->render('ordendetrabajo/show.html.twig', array(
            'ordenDeTrabajo' => $ordenDeTrabajo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ordenDeTrabajo entity.
     *
     * @Route("/{id}/editar", name="ordendetrabajo_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, OrdenDeTrabajo $ordenDeTrabajo)
    {
        $deleteForm = $this->createDeleteForm($ordenDeTrabajo);
        $editForm = $this->createForm('AppBundle\Form\OrdenDeTrabajoType', $ordenDeTrabajo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ordendetrabajo_edit', array('id' => $ordenDeTrabajo->getId()));
        }

        return $this->render('ordendetrabajo/edit.html.twig', array(
            'ordenDeTrabajo' => $ordenDeTrabajo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ordenDeTrabajo entity.
     *
     * @Route("/{id}", name="ordendetrabajo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OrdenDeTrabajo $ordenDeTrabajo)
    {
        $form = $this->createDeleteForm($ordenDeTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ordenDeTrabajo);
            $em->flush();
        }

        return $this->redirectToRoute('ordendetrabajo_index');
    }

    /**
     * Creates a form to delete a ordenDeTrabajo entity.
     *
     * @param OrdenDeTrabajo $ordenDeTrabajo The ordenDeTrabajo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrdenDeTrabajo $ordenDeTrabajo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordendetrabajo_delete', array('id' => $ordenDeTrabajo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
