<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 07/08/2018
 * Time: 04:47 PM
 */

namespace AppBundle\Controller\Combustible;


use AppBundle\Entity\Combustible\Catalogo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use AppBundle\Entity\Combustible\Catalogo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Catalogo controller.
 *
 * @Route("/combustible/catalogo")
 */
class CatalogoController extends Controller
{
    /**
     * @Route("/", name="combustible_catalogo_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $catalogo = $em->getRepository('AppBundle:Combustible\Catalogo')->findAll();
        $this->denyAccessUnlessGranted('ROLE_COMBUSTIBLE_CATALOGO', $catalogo);
        return $this->render('combustible/catalogo/index.html.twig', [
            'catalogo' => $catalogo,
            'title' => 'Catálogo Combustibles'
        ]);
    }

    /**
     *
     * @Route("/nuevo", name="combustible_catalogo_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $catalogo = new Catalogo();
        $this->denyAccessUnlessGranted('ROLE_COMBUSTIBLE_CATALOGO', $catalogo);
        $form = $this->createForm('AppBundle\Form\Combustible\CatalogoType',$catalogo);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($catalogo);
            $em->flush();
            return $this->redirectToRoute('combustible_catalogo_index');
        }
        return $this->render('combustible/catalogo/new.html.twig',[
            'form' => $form->createView(),
            'title' => 'Nuevo Combustible'
        ]);
    }

    /**
     * @Route("/{id}/editar", name="combustible_catalogo_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Catalogo $catalogo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Catalogo $catalogo)
    {
        $this->denyAccessUnlessGranted('ROLE_COMBUSTIBLE_CATALOGO', $catalogo);
        $deleteForm = $this->createDeleteForm($catalogo);
        $editForm = $this->createForm('AppBundle\Form\Combustible\CatalogoType', $catalogo);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('combustible_catalogo_index');
        }
        return $this->render('combustible/catalogo/new.html.twig', [
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Editar Combustible'
        ]);
    }

    /**
     * @Route("/{id}", name="combustible_catalogo_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Catalogo $catalogo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Catalogo $catalogo)
    {
        $this->denyAccessUnlessGranted('ROLE_COMBUSTIBLE_CATALOGO', $catalogo);
        $form = $this->createDeleteForm($catalogo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($catalogo);
            $em->flush();
        }
        return $this->redirectToRoute('combustible_catalogo_index');
    }

    /**
     * @param Catalogo $catalogo The catalogo entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Catalogo $catalogo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('combustible_catalogo_delete', array('id' => $catalogo->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}