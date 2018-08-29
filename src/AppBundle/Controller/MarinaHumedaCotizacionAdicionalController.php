<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\MarinaHumedaCotizacionAdicional;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\MarinaHumedaServicio;
use AppBundle\Entity\ValorSistema;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Marinahumedacotizacionadicional controller.
 *
 * @Route("/marina/servicios-adicionales")
 */
class MarinaHumedaCotizacionAdicionalController extends Controller
{
    /**
     * Muestra todos los servicios adicionales de marina humeda
     *
     * @Route("/", name="marina-humeda-cotizacion-adicional_index")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'cotizacionMarinaAdicional');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getCode());
            }
        }
        return $this->render('marinahumeda/cotizacionadicional/index.html.twig', [
            'title' => 'Servicios adicionales',
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
        $em = $this->getDoctrine()->getManager();
        $sistema = $em->getRepository('AppBundle:ValorSistema')->findOneBy(['id'=>1]);
        $dolarBase = $sistema->getDolar();
        $iva = $sistema->getIva();
        $marinaHumedaCotizacionAdicional
            ->setDolar($dolarBase)
            ->setIva($iva);
        $form = $this->createForm('AppBundle\Form\MarinaHumedaCotizacionAdicionalType', $marinaHumedaCotizacionAdicional);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $marinaHumedaCotizacionAdicional->setFecharegistro(new \DateTime('now'));
            $em->persist($marinaHumedaCotizacionAdicional);
            $em->flush();
            return $this->redirectToRoute('marina-humeda-cotizacion-adicional_show', ['id' => $marinaHumedaCotizacionAdicional->getId()]);
        }
        return $this->render('marinahumeda/cotizacionadicional/new.html.twig', [
            'title' => 'Nuevo Servicio',
            'marinaHumedaCotizacionAdicional' => $marinaHumedaCotizacionAdicional,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cliente.json")
     * @return Response
     */
    public function buscarClienteAction()
    {
        $clientes = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacionAdicional')->getAllClientes();
        return new JsonResponse($clientes, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/barco.json")
     * @return Response
     */
    public function buscarBarcoAction()
    {
        $barco = $this->getDoctrine()->getRepository('AppBundle:MarinaHumedaCotizacionAdicional')->getAllBarcos();
        return new JsonResponse($barco, JsonResponse::HTTP_OK);
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
        return $this->render('marinahumeda/cotizacionadicional/show.html.twig', [
            'title' => 'Servicio adicional',
            'marinaHumedaCotizacionAdicional' => $marinaHumedaCotizacionAdicional
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
                    $em->persist($serv);
                    $em->remove($serv);
                }
            }
            $em->persist($marinaHumedaCotizacionAdicional);
            $em->flush();

            return $this->redirectToRoute('marina-humeda-cotizacion-adicional_show', array('id' => $marinaHumedaCotizacionAdicional->getId()));
        }

        return $this->render('marinahumeda/cotizacionadicional/edit.html.twig', [
            'title' => 'Editar servicio adicional',
            'marinaHumedaCotizacionAdicional' => $marinaHumedaCotizacionAdicional,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ]);
    }

    /**
     * @Route("/buscarservicio/{id}", name="mhca_buscaservicio")
     * @param $id
     * @return JsonResponse
     */
    public function buscarAction($id)
    {
        $servicioRepository = $this->getDoctrine()->getRepository(MarinaHumedaServicio::class);
        return new JsonResponse($servicioRepository->getServicioCatalogo($id),JsonResponse::HTTP_OK);
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
