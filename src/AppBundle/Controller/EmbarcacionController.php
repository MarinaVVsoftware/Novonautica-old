<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Embarcacion;
use AppBundle\Entity\EmbarcacionImagen;
use AppBundle\Entity\EmbarcacionLayout;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use SensioLabs\Security\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'embarcaciones');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getCode());
            }
        }
        return $this->render('embarcacion/index.html.twig', ['title' => 'Embarcaciones']);
    }

    /**
     * Creates a new embarcacion entity.
     *
     * @Route("/new", name="embarcacion_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $embarcacion = new Embarcacion();
        $this->denyAccessUnlessGranted('EMBARCACION_CREATE', $embarcacion);

        // Hack para que aparezca al menos un motor el primer [] abre un arreglo, el segundo [] inserta un arreglo de motor
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
     * Displays a form to edit an existing embarcacion entity.
     *
     * @Route("/{id}/edit", name="embarcacion_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Embarcacion $embarcacion
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function editAction(Request $request, Embarcacion $embarcacion)
    {
        $this->denyAccessUnlessGranted('EMBARCACION_EDIT', $embarcacion);

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
            'title' => $embarcacion->getNombre(),
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
     *
     * @param Request $request
     * @param Embarcacion $embarcacion
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Embarcacion $embarcacion)
    {
        $this->denyAccessUnlessGranted('EMBARCACION_DELETE', $embarcacion);
        $form = $this->createDeleteForm($embarcacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($embarcacion);
            $em->flush();
        }

        return $this->redirectToRoute('embarcacion_index');
    }

    /**
     * @Route("/{id}/brochure", name="embarcacion_brochure")
     *
     * @param Embarcacion $embarcacion
     *
     * @return PdfResponse|Response
     */
    public function showBrochureAction(Embarcacion $embarcacion)
    {
        $head = $this->renderView('embarcacion/pdf/head.html.twig');
        $body = $this->renderView('embarcacion/pdf/body.html.twig', [
            'title' => 'brochure.pdf',
            'embarcacion' => $embarcacion
        ]);

        $options = [
            'margin-top' => 24,
            'margin-right' => 5,
            'margin-left' => 5,
            'margin-bottom' => 5,
            'header-html' => utf8_decode($head),
        ];

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($body, $options),
            'brochure.pdf', 'application/pdf', 'inline'
        );
    }

    /**
     * @Route("/paises.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getPaisesAction(Request $request)
    {
        $marcas = $this->getDoctrine()->getRepository('AppBundle:Embarcacion')->findPaises();
        return new Response($this->serializeEntities($marcas, $request->getRequestFormat()));
    }

    /**
     * @Route("/marcas.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getMarcasAction(Request $request)
    {
        $marcas = $this->getDoctrine()->getRepository('AppBundle:Embarcacion')->findMarcas();
        return new Response($this->serializeEntities($marcas, $request->getRequestFormat(), [
            'imagenFile',
            'updateAt',
            'modelos'
        ]));
    }

    /**
     * @Route("/modelos.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getModelosAction(Request $request)
    {
        $marcas = $this->getDoctrine()->getRepository('AppBundle:Embarcacion')->findModelos();
        return new Response($this->serializeEntities($marcas, $request->getRequestFormat(), [
            'marca'
        ]));
    }

    /**
     * @Route("/anos.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getYearsAction(Request $request)
    {
        $years = $this->getDoctrine()->getRepository('AppBundle:Embarcacion')->findAnos();
        return new Response($this->serializeEntities($years, $request->getRequestFormat()));
    }

    /**
     * @Route("/categoria.json")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getCategoriaAction(Request $request)
    {
        $categorias = $this->getDoctrine()->getRepository('AppBundle:Embarcacion')->findCategorias();
        $nombres = [];
        foreach ($categorias as $categoria) {
            switch ($categoria['nombre']){
                case 1: $nombre= 'Dingui';  break;
                case 2: $nombre= 'Express'; break;
                case 3: $nombre= 'Fly Bridge'; break;
                case 4: $nombre= 'Mega Yates'; break;
                case 5: $nombre= 'Sport Fishing'; break;
                case 6: $nombre= 'Vela'; break;
                default: $nombre= '-'; break;
            }
            array_push($nombres,['nombre'=>$nombre]);
        }
        return $this->json($nombres, $request->getRequestFormat());
    }


    private function serializeEntities($entity, $format, $ignoredAttributes = [])
    {
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $normalizer->setIgnoredAttributes($ignoredAttributes);

        return $serializer->serialize($entity, $format);
    }

    /**
     * Creates a form to delete a embarcacion entity.
     *
     * @param Embarcacion $embarcacion The embarcacion entity
     *
     * @return FormInterface
     */
    private function createDeleteForm(Embarcacion $embarcacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('embarcacion_delete', ['id' => $embarcacion->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
