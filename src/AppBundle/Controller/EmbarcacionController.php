<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Embarcacion;
use AppBundle\Entity\EmbarcacionImagen;
use AppBundle\Entity\EmbarcacionLayout;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Embarcacion controller.
 *
 * @Route("/embarcacion")
 */
class EmbarcacionController extends Controller
{
    /**
     * Lists all embarcacion entities.
     *
     * @Route("/", name="embarcacion_index")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page') ?: 1;
        $limit = $request->query->get('limit') ?: 10;

        $em = $this->getDoctrine()->getManager();
        $paginacion = $em->getRepository('AppBundle:Embarcacion')
            ->paginacion($page);

        $pages = ceil($paginacion->count() / $limit);
        $embarcaciones = $paginacion->getQuery()->getResult();

        $deleteForms = [];
        foreach ($embarcaciones as $embarcacion) {
            $deleteForms[] = $this->createDeleteForm($embarcacion)->createView();
        }

        return $this->render('embarcacion/index.html.twig', compact('embarcaciones', 'page', 'pages', 'deleteForms'));
    }

    /**
     * Creates a new embarcacion entity.
     *
     * @Route("/new", name="embarcacion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $embarcacion = new Embarcacion();
        // Hack para que aparezca al menos un motor el primer [] abre un arreglo, el segundo [] insera un arreglo de motor
        $embarcacion->setMotores([[]]);
        $form = $this->createForm('AppBundle\Form\EmbarcacionType', $embarcacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $embarcacion->setMotores(array_values($embarcacion->getMotores()));
            $em->persist($embarcacion);
            $em->flush();

            return $this->redirectToRoute('embarcacion_index');
        }

        return $this->render('embarcacion/new.html.twig', [
            'embarcacion' => $embarcacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a embarcacion entity.
     *
     * @Route("/{id}", name="embarcacion_show")
     * @Method("GET")
     */
    public function showAction(Embarcacion $embarcacion)
    {
        $deleteForm = $this->createDeleteForm($embarcacion);

        return $this->render('embarcacion/show.html.twig', array(
            'embarcacion' => $embarcacion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing embarcacion entity.
     *
     * @Route("/{id}/edit", name="embarcacion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Embarcacion $embarcacion)
    {
        $deleteForm = $this->createDeleteForm($embarcacion);

        $oldImages = new ArrayCollection();
        foreach ($embarcacion->getImagenes() as $imagen) {
            $oldImages->add($imagen);
        }

        $oldLayouts = new ArrayCollection();
        foreach ($embarcacion->getLayouts() as $layout) {
            $oldLayouts->add($layout);
        }

        $editForm = $this->createForm('AppBundle\Form\EmbarcacionType', $embarcacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $embarcacion->setMotores(array_values($embarcacion->getMotores()));

            foreach ($oldImages as $oldImage) {
                if (!$embarcacion->getImagenes()->contains($oldImage)) {
                    /** @var EmbarcacionImagen $oldImage */
                    $fs = new Filesystem();
                    if ($fs->exists('../web/uploads/embarcacion/' . $oldImage->getBasename())) {
                        $fs->remove('../web/uploads/embarcacion/' . $oldImage->getBasename());
                    }

                    $oldImage->setEmbarcacion(null);
                    $em->remove($oldImage);
                }
            }

            foreach ($oldLayouts as $oldLayout) {
                if (!$embarcacion->getLayouts()->contains($oldLayout)) {
                    /** @var EmbarcacionLayout $oldLayout */
                    $fs = new Filesystem();
                    if ($fs->exists('../web/uploads/embarcacion/' . $oldLayout->getBasename())) {
                        $fs->remove('../web/uploads/embarcacion/' . $oldLayout->getBasename());
                    }

                    $oldLayout->setEmbarcacion(null);
                    $em->remove($oldLayout);
                }
            }

            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['ok' => true]);
            }
            return $this->redirectToRoute('embarcacion_edit', ['id' => $embarcacion->getId()]);
        }

        return $this->render('embarcacion/new.html.twig', [
            'embarcacion' => $embarcacion,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a embarcacion entity.
     *
     * @Route("/{id}", name="embarcacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Embarcacion $embarcacion)
    {
        $form = $this->createDeleteForm($embarcacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($embarcacion);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Creates a form to delete a embarcacion entity.
     *
     * @param Embarcacion $embarcacion The embarcacion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Embarcacion $embarcacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('embarcacion_delete', array('id' => $embarcacion->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
