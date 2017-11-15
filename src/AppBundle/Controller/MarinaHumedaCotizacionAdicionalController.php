<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MarinaHumedaCotizacionAdicional;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\ValorSistema;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Marinahumedacotizacionadicional controller.
 *
 * @Route("/marina/servicios-adicionales")
 */
class MarinaHumedaCotizacionAdicionalController extends Controller
{
    /**
     * Muestra todos los servicios adicinales de marina humeda
     *
     * @Route("/", name="marina-humeda-cotizacion-adicional_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $marinaHumedaCotizacionAdicionals = $em->getRepository('AppBundle:MarinaHumedaCotizacionAdicional')->findAll();

        return $this->render('marinahumeda/cotizacionadicional/index.html.twig', [
            'title' => 'Servicios adicionales',
            'marinaHumedaCotizacionAdicionals' => $marinaHumedaCotizacionAdicionals,
        ]);
    }

    /**
     * Crea un nuevo servicio adicional de marina humeda
     *
     * @Route("/nuevo", name="marina-humeda-cotizacion-adicional_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $marinaHumedaCotizacionAdicional = new MarinaHumedaCotizacionAdicional();
        $dolarBase = $this->getDoctrine()
            ->getRepository(ValorSistema::class)
            ->find(1)
            ->getValor();
        $iva = $this->getDoctrine()
            ->getRepository(ValorSistema::class)
            ->find(2)
            ->getValor();


        $form = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionAdicionalType', $marinaHumedaCotizacionAdicional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $granSubtotal = 0;
            $granIvatotal = 0;
            $granTotal = 0;

            foreach ($marinaHumedaCotizacionAdicional->getMhcservicios() as $servicio){
                $cantidad = $servicio->getCantidad();
                $precio = $servicio->getMarinahumedaservicio()->getPrecio();
                $subtotal = $cantidad * $precio;
                $ivatotal = ($subtotal * $iva)/100;
                $total = $subtotal + $ivatotal;

                $servicio
                    ->setPrecio($precio)
                    ->setSubtotal($subtotal)
                    ->setIva($ivatotal)
                    ->setTotal($total)
                    ->setEstatus(true);

                $granSubtotal+=$subtotal;
                $granIvatotal+=$ivatotal;
                $granTotal+=$total;
            }

            $marinaHumedaCotizacionAdicional
                ->setIva($iva)
                ->setSubtotal($granSubtotal)
                ->setIvatotal($granIvatotal)
                ->setTotal($granTotal);

            $em->persist($marinaHumedaCotizacionAdicional);
            $em->flush();

            return $this->redirectToRoute('marina-humeda-cotizacion-adicional_show', ['id' => $marinaHumedaCotizacionAdicional->getId()]);
        }

        return $this->render('marinahumeda/cotizacionadicional/new.html.twig', [
            'title' => 'Nuevo Servicio',
            'marinaHumedaCotizacionAdicional' => $marinaHumedaCotizacionAdicional,
            'valdolar' => $dolarBase,
            'valiva' => $iva,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Muestra un servicio adicional en especificio
     *
     * @Route("/{id}", name="marina-humeda-cotizacion-adicional_show")
     * @Method("GET")
     *
     * @param MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional
     *
     * @return Response
     */
    public function showAction(MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional)
    {
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacionAdicional);

        return $this->render('marinahumeda/cotizacionadicional/show.html.twig', [
            'title' => 'Servicio adicional',
            'marinaHumedaCotizacionAdicional' => $marinaHumedaCotizacionAdicional,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Edita un servicio adicional en especifico
     *
     * @Route("/{id}/editar", name="marina-humeda-cotizacion-adicional_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional)
    {
        $originalServicios = new ArrayCollection();

        foreach ($marinaHumedaCotizacionAdicional->getMhcservicios() as $serv){
            $originalServicios->add($serv);
        }
        $deleteForm = $this->createDeleteForm($marinaHumedaCotizacionAdicional);
        $editForm = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionAdicionalType', $marinaHumedaCotizacionAdicional);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($originalServicios as $serv){
                if (false === $marinaHumedaCotizacionAdicional->getMhcservicios()->contains($serv)) {
                    // remove the Task from the Tag
                    $serv->getMhcservicios()->removeMhcservicio($serv);

                    // if it was a many-to-one relationship, remove the relationship like this
                    //$motor->setBarco(null);

                    $em->persist($serv);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($serv);
                }
            }

            $iva = $marinaHumedaCotizacionAdicional->getIva();
            $granSubtotal = 0;
            $granIvatotal = 0;
            $granTotal = 0;


            foreach ($marinaHumedaCotizacionAdicional->getMhcservicios() as $servicio){
                $cantidad = $servicio->getCantidad();
                $precio = $servicio->getMarinahumedaservicio()->getPrecio();
                $subtotal = $cantidad * $precio;
                $ivatotal = ($subtotal * $iva)/100;
                $total = $subtotal + $ivatotal;

                $servicio
                    ->setPrecio($precio)
                    ->setSubtotal($subtotal)
                    ->setIva($ivatotal)
                    ->setTotal($total)
                    ->setEstatus(true);

                $granSubtotal+=$subtotal;
                $granIvatotal+=$ivatotal;
                $granTotal+=$total;
            }
            $em->persist($marinaHumedaCotizacionAdicional);
            $em->flush();

            return $this->redirectToRoute('marina-humeda-cotizacion-adicional_show', array('id' => $marinaHumedaCotizacionAdicional->getId()));
        }

        return $this->render('marinahumeda/cotizacionadicional/edit.html.twig', [
            'title' => 'Editar servicio adicional',
            'marinaHumedaCotizacionAdicional' => $marinaHumedaCotizacionAdicional,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Elimina un servicio adicional
     *
     * @Route("/{id}", name="marina-humeda-cotizacion-adicional_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional)
    {
        $form = $this->createDeleteForm($marinaHumedaCotizacionAdicional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marinaHumedaCotizacionAdicional);
            $em->flush();
        }

        return $this->redirectToRoute('marina-humeda-cotizacion-adicional_index');
    }

    /**
     * Crea un formulario para eliminar una entidad
     *
     * @param MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional The marinaHumedaCotizacionAdicional entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MarinaHumedaCotizacionAdicional $marinaHumedaCotizacionAdicional)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marina-humeda-cotizacion-adicional_delete', array('id' => $marinaHumedaCotizacionAdicional->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
