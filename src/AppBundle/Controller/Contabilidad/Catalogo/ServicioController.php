<?php
/**
 * User: inrumi
 * Date: 9/27/18
 * Time: 09:40
 */

namespace AppBundle\Controller\Contabilidad\Catalogo;


use AppBundle\Entity\Contabilidad\Catalogo\Servicio;
use AppBundle\Form\Contabilidad\Catalogo\ServicioType;
use DataTables\DataTablesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Servicio
 * @package AppBundle\Controller\Contabilidad\Catalogo
 *
 * @Route("/contabilidad/catalogo/servicio")
 */
class ServicioController extends AbstractController
{
    /**
     * @Route("/", name="contabilidad_catalogo_servicio_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $servicioRepository = $em->getRepository(Servicio::class);

        $servicio = $request->query->get('s') ?: null;
        $servicio = $servicio ? $servicioRepository->find($servicio) : new Servicio();

        if (null === $servicio->getCodigo()) {
            $servicio->setCodigo($servicioRepository->createCodigo());
        }

        $form = $this->createForm(ServicioType::class, $servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($servicio);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(
            'contabilidad/catalogo/servicio.html.twig',
            [
                'title' => 'Catalogo de servicios',
                'servicio' => $servicio,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/eliminar")
     */
    public function eliminarAction(Servicio $servicio)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($servicio);
        $em->flush();

        return $this->redirectToRoute('contabilidad_catalogo_servicio_index');
    }

    /**
     * @Route("/servicios")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUsuariosDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'contabilidad/catalogo/servicio');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
