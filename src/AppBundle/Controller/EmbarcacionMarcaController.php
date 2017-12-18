<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EmbarcacionMarca;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Embarcacionmarca controller.
 *
 * @Route("embarcacion/marca")
 */
class EmbarcacionMarcaController extends Controller
{
    /**
     * Creates a new embarcacionMarca entity.
     *
     * @Route("/new", name="embarcacion_marca_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $marcasRepo = $em->getRepository('AppBundle:EmbarcacionMarca');
        $query = $request->query;
        $page = (int)$query->get('page') ?: 1;
        $marca = (int)$query->get('marca') ?: null;
        $marca = $marca ? $marcasRepo->find($marca) : new Embarcacionmarca();
        $newForm = $this->createForm('AppBundle\Form\EmbarcacionMarcaType', $marca);
        $newForm->handleRequest($request);

        $paginacion = $marcasRepo->paginacion($page);
        $marcas = $paginacion['marcas']->getQuery()->getResult();

        if ($page > $paginacion['pages'] && $paginacion['total'] > 0) {
            return $this->redirectToRoute('embarcacion_marca_new', ['page' => $paginacion['pages']]);
        }

        $deleteForms = [];
        foreach ($marcas as $formMarca) {
            $deleteForms[] = $this->createDeleteForm($formMarca['id'])->createView();
        }

        if ($newForm->isSubmitted() && $newForm->isValid()) {
            $em->persist($marca);
            $em->flush();

            return $this->redirectToRoute('embarcacion_marca_new');
        }

        return $this->render('embarcacion/marca/new.html.twig', [
            'marca' => $marca,
            'embarcacionMarcas' => $marcas,
            'newForm' => $newForm->createView(),
            'deleteForms' => $deleteForms,
            'page' => $page,
            'pages' => $paginacion['pages']
        ]);
    }

    /**
     * @Route("/{id}/modelos.{_format}", name="embarcacion_marca_modelos_ajax", defaults={"_format" = "json"})
     */
    public function ajaxModelosAction(Request $request, EmbarcacionMarca $marca)
    {
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $normalizer->setIgnoredAttributes(['marca']);

        $modelos = $serializer->serialize($marca, $request->getRequestFormat());

        return new Response($modelos);
    }

    /**
     * Deletes a embarcacionMarca entity.
     *
     * @Route("/{id}", name="embarcacion_marca_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EmbarcacionMarca $embarcacionMarca)
    {
        $form = $this->createDeleteForm($embarcacionMarca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($embarcacionMarca);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param EmbarcacionMarca $embarcacionMarca
     *
     * @return Form The form
     */
    private function createDeleteForm($embarcacionMarca)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('embarcacion_marca_delete', ['id' => $embarcacionMarca]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
