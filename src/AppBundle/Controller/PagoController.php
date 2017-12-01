<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\Pago;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Pago controller.
 *
 * @Route("marina/cotizacion/pago")
 */
class PagoController extends Controller
{
//    /**
//     * Lists all pago entities.
//     *
//     * @Route("/", name="marina_cotizacion_pago_index")
//     * @Method("GET")
//     */
//    public function indexAction()
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $pagos = $em->getRepository('AppBundle:Pago')->findAll();
//
//        return $this->render('pago/index.html.twig', array(
//            'pagos' => $pagos,
//        ));
//    }
//
//    /**
//     * Creates a new pago entity.
//     *
//     * @Route("/{id}/nuevo", name="marina_cotizacion_pago_new")
//     * @Method({"GET", "POST"})
//     */
//    public function newAction(Request $request,MarinaHumedaCotizacion $marinaHumedaCotizacion)
//    {
//
//        $pago = new Pago();
//        $marinaHumedaCotizacion->addPago($pago);
//        $form = $this->createForm('AppBundle\Form\PagoType', $pago);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($pago);
//            $em->flush();
//
//            return $this->redirectToRoute('marina_cotizacion_pago_show', array('id' => $pago->getId()));
//        }
//
//        return $this->render('marinahumeda/cotizacion/pago/new.html.twig', array(
//            'pago' => $pago,
//            'marinaHumedaCotizacion' => $marinaHumedaCotizacion,
//            'form' => $form->createView(),
//        ));
//    }
//
//    /**
//     * Finds and displays a pago entity.
//     *
//     * @Route("/{id}", name="marina_cotizacion_pago_show")
//     * @Method("GET")
//     */
//    public function showAction(Pago $pago)
//    {
//        $deleteForm = $this->createDeleteForm($pago);
//
//        return $this->render('pago/show.html.twig', array(
//            'pago' => $pago,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }
//
//    /**
//     * Displays a form to edit an existing pago entity.
//     *
//     * @Route("/{id}/edit", name="marina_cotizacion_pago_edit")
//     * @Method({"GET", "POST"})
//     */
//    public function editAction(Request $request, Pago $pago)
//    {
//        $deleteForm = $this->createDeleteForm($pago);
//        $editForm = $this->createForm('AppBundle\Form\PagoType', $pago);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('marina_cotizacion_pago_edit', array('id' => $pago->getId()));
//        }
//
//        return $this->render('pago/edit.html.twig', array(
//            'pago' => $pago,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }
//
//    /**
//     * Deletes a pago entity.
//     *
//     * @Route("/{id}", name="marina_cotizacion_pago_delete")
//     * @Method("DELETE")
//     */
//    public function deleteAction(Request $request, Pago $pago)
//    {
//        $form = $this->createDeleteForm($pago);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($pago);
//            $em->flush();
//        }
//
//        return $this->redirectToRoute('marina_cotizacion_pago_index');
//    }
//
//    /**
//     * Creates a form to delete a pago entity.
//     *
//     * @param Pago $pago The pago entity
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createDeleteForm(Pago $pago)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('marina_cotizacion_pago_delete', array('id' => $pago->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }
}
