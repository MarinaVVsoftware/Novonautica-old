<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Usuario controller.
 *
 * @Route("usuario")
 */
class UsuarioController extends Controller
{
    /**
     * Lists all user entities
     *
     * @Route("/", name="usuario_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('usuario/index.html.twig', ['title' => 'Usuarios']);
    }

    /**
     * @Route("/")
     * @Method("POST")
     *
     * @param Request $request
     * @param SessionInterface $session
     *
     * @return JsonResponse
     */
    public function setSidebarAction(Request $request, SessionInterface $session)
    {
        $session->set('isExpanded', $request->request->get('isExpanded'));

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/usuarios", name="usuario_index_data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function getUsuariosDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'usuario');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Creates a new usuario entity.
     *
     * @Route("/new", name="usuario_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $usuario = new Usuario();

        $this->denyAccessUnlessGranted('RH_CREATE', $usuario);

        $form = $this->createForm('AppBundle\Form\UsuarioType', $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            return $this->redirectToRoute('usuario_index');
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing usuario entity.
     *
     * @Route("/{id}/edit", name="usuario_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Usuario $usuario
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Usuario $usuario)
    {
        $this->denyAccessUnlessGranted('RH_EDIT', $usuario);

        $deleteForm = $this->createDeleteForm($usuario);
        $editForm = $this->createForm('AppBundle\Form\UsuarioType', $usuario);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('usuario_edit', ['id' => $usuario->getId()]);
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a usuario entity.
     *
     * @Route("/{id}", name="usuario_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Usuario $usuario
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Usuario $usuario)
    {
        $this->denyAccessUnlessGranted('RH_DELETE', $usuario);

        $form = $this->createDeleteForm($usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($usuario);
            $em->flush();
        }

        return $this->redirectToRoute('usuario_index');
    }

    /**
     * Creates a form to delete a usuario entity.
     *
     * @param Usuario $usuario The usuario entity
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Usuario $usuario)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuario_delete', ['id' => $usuario->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
