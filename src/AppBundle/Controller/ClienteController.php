<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Barco;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\Motor;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Cliente controller.
 *
 * @Route("cliente")
 */
class ClienteController extends Controller
{
    /**
     * Lists all cliente entities.
     *
     * @Route("/", name="cliente_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientes = $em->getRepository('AppBundle:Cliente')->findAll();

        return $this->render('cliente/index.html.twig', array(
            'clientes' => $clientes,
            'clientelistado' => 1
        ));
    }

    /**
     * Creates a new cliente entity.
     *
     * @Route("/nuevo", name="cliente_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cliente = new Cliente();
        $barco = new Barco();
        $motor = new Motor();
        $cliente->addBarco($barco);
        $barco->addMotore($motor);
        $form = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cliente);
            $em->flush();

            return $this->redirectToRoute('cliente_show', array('id' => $cliente->getId()));

        }

        return $this->render('cliente/new.html.twig', array(
            'cliente' => $cliente,
            'form' => $form->createView(),
            'clienteagregar' => 1
        ));
    }

    /**
     * Finds and displays a cliente entity.
     *
     * @Route("/{id}", name="cliente_show")
     * @Method("GET")
     */
    public function showAction(Cliente $cliente)
    {
        $deleteForm = $this->createDeleteForm($cliente);
        //$barcos = $cliente->getBarcos();
        dump($cliente);
        return $this->render('cliente/show.html.twig', array(
            'cliente' => $cliente,
            'delete_form' => $deleteForm->createView(),
            'clientelistado' => 1,
        ));
    }

    /**
     * Displays a form to edit an existing cliente entity.
     *
     * @Route("/{id}/editar", name="cliente_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cliente $cliente)
    {
        $barcos = $cliente->getBarcos();
        $barcomotores = [];

        $em = $this->getDoctrine()->getManager();

        foreach ($barcos as $barco){
            $barco = $em->getRepository(Barco::class)->find($barco->getId());
            $cliente = $barco->getCliente();
            if (!$barco) {
                throw $this->createNotFoundException('No hay barcos encontrados para el id '.$barco->getId());
            }
            $originalMotores = new ArrayCollection();

            foreach ($barco->getMotores() as $motor) {
                $originalMotores->add($motor);
            }
            $barcomotores[$barco->getId()] = $originalMotores; //guardamos en el arreglo la coleccion de motores correspondiente a su id de barco
        }

        $deleteForm = $this->createDeleteForm($cliente);
        $editForm = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {


            foreach ($barcos as $barco){
                $om = $barcomotores[$barco->getId()]; //extraemos la coleccion de motores del barco correspondiente

                foreach ($om as $motor){

                    if (false === $barco->getMotores()->contains($motor)) {
                        // remove the Task from the Tag
                        $motor->getBarco()->removeMotore($motor);

                        // if it was a many-to-one relationship, remove the relationship like this
                        //$motor->setBarco(null);

                        $em->persist($motor);

                        // if you wanted to delete the Tag entirely, you can also do that
                        $em->remove($motor);
                    }
                    $em->persist($barco);

                }
            }

            $em->flush();

            // redirect back to some edit page
            return $this->redirectToRoute('cliente_show', array('id' => $cliente->getId()));

        }
        return $this->render('cliente/edit.html.twig', array(
            'cliente' => $cliente,
            'barcos' => $barcos,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'clientelistado' => 1,
        ));
    }

    /**
     * Deletes a cliente entity.
     *
     * @Route("/{id}", name="cliente_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Cliente $cliente)
    {
        $form = $this->createDeleteForm($cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cliente);
            $em->flush();
        }

        return $this->redirectToRoute('cliente_index');
    }

    /**
     * Creates a form to delete a cliente entity.
     *
     * @param Cliente $cliente The cliente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cliente $cliente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cliente_delete', array('id' => $cliente->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
