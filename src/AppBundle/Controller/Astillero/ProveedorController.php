<?php

namespace AppBundle\Controller\Astillero;

use AppBundle\Entity\Astillero\Proveedor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Proveedor controller.
 *
 * @Route("proveedor")
 */
class ProveedorController extends Controller
{
    /**
     * Lists all proveedor entities.
     *
     * @Route("/", name="proveedor_index")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $datatables = $this->get('datatables');
                $results = $datatables->handle($request, 'AstilleroProveedor');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }

        return $this->render('astillero/proveedor/index.html.twig', [
            'title' => 'Proveedores / Contratistas',
        ]);
    }

    /**
     * Creates a new proveedor entity.
     *
     * @Route("/nuevo", name="proveedor_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $proveedor = new Proveedor();
        $banco = new Proveedor\Banco();

        $this->denyAccessUnlessGranted('PROVEEDOR_CREATE', $proveedor);

        $proveedor->setPassword($this->generateRandomString());
        $proveedor->addBanco($banco);

        $form = $this->createForm('AppBundle\Form\Astillero\ProveedorType', $proveedor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($proveedor);
            $em->flush();

            return $this->redirectToRoute('proveedor_show', ['id' => $proveedor->getId()]);
        }

        return $this->render('astillero/proveedor/new.html.twig', array(
            'proveedor' => $proveedor,
            'form' => $form->createView(),
            'title' => 'Nuevo proveedor / contratista'
        ));
    }

    /**
     * @Route("/buscarproveedor", name="astillero_proveedor_ajax")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function buscarProveedorAction(Request $request)
    {
        $idproveedor = $request->get('idproveedor');
        $proveedor = $this->getDoctrine()
            ->getRepository('AppBundle:Astillero\Proveedor')
            ->getOneByArray($idproveedor);

        unset($proveedor['password']);

        return $this->json($proveedor);
    }

    /**
     * Finds and displays a proveedor entity.
     *
     * @Route("/{id}", name="proveedor_show")
     * @Method("GET")
     *
     * @param Proveedor $proveedor
     *
     * @return Response
     */
    public function showAction(Proveedor $proveedor)
    {
        return $this->render('astillero/proveedor/show.html.twig', array(
            'proveedor' => $proveedor,
            'title' => 'Detalle proveedor / Contratista'
        ));
    }

    /**
     * Displays a form to edit an existing proveedor entity.
     *
     * @Route("/{id}/editar", name="proveedor_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Proveedor $proveedor
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Proveedor $proveedor)
    {
        $this->denyAccessUnlessGranted('PROVEEDOR_EDIT', $proveedor);
        $em = $this->getDoctrine()->getManager();
        $originalBancos = new ArrayCollection();
        foreach ($proveedor->getBancos() as $banco) {
            $originalBancos->add($banco);
        }
        $deleteForm = $this->createDeleteForm($proveedor);
        $editForm = $this->createForm('AppBundle\Form\Astillero\ProveedorType', $proveedor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($originalBancos as $banco) {
                if (false === $proveedor->getBancos()->contains($banco)) {
                    $banco->getProveedor()->removeBanco($banco);
                    $em->persist($banco);
                    $em->remove($banco);

                }
            }
            $em->persist($proveedor);
            $em->flush();

            return $this->redirectToRoute('proveedor_show', ['id' => $proveedor->getId()]);
        }

        return $this->render('astillero/proveedor/edit.html.twig', array(
            'proveedor' => $proveedor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'title' => 'Editar Proveedor / contratista'
        ));
    }

    /**
     * Deletes a proveedor entity.
     *
     * @Route("/{id}", name="proveedor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Proveedor $proveedor)
    {
        $this->denyAccessUnlessGranted('PROVEEDOR_DELETE', $proveedor);
        $form = $this->createDeleteForm($proveedor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($proveedor);
            $em->flush();
        }

        return $this->redirectToRoute('proveedor_index');
    }

    /**
     * Creates a form to delete a proveedor entity.
     *
     * @param Proveedor $proveedor The proveedor entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Proveedor $proveedor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proveedor_delete', array('id' => $proveedor->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function generateRandomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
