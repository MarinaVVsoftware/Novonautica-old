<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Astillero\Contratista;
use AppBundle\Entity\Astillero\Contratista\Actividad;
use AppBundle\Entity\Astillero\Contratista\Actividad\Pausa;
use AppBundle\Entity\OrdenDeTrabajo;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use AppBundle\Entity\Correo;

/**
 * Ordendetrabajo controller.
 *
 * @Route("astillero/odt")
 */
class OrdenDeTrabajoController extends Controller
{
    /**
     * Lists all ordenDeTrabajo entities.
     *
     * @Route("/", name="ordendetrabajo_index")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'ODT');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }

        return $this->render('ordendetrabajo/index.html.twig', [
            'title' => 'Ordenes de trabajo'
        ]);
    }

    /**
     * Creates a new ordenDeTrabajo entity.
     *
     * @Route("/nueva", name="ordendetrabajo_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $ordenDeTrabajo = new Ordendetrabajo();
        $this->denyAccessUnlessGranted('ROLE_ODT_CREATE', $ordenDeTrabajo);

        $precioTotal = 0;
        $utilidadvvTotal = 0;
        $preciovvTotal = 0;
        $ivaTotal = 0;
        $granTotal = 0;
        $form = $this->createForm('AppBundle\Form\OrdenDeTrabajoType', $ordenDeTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $iva = $ordenDeTrabajo->getAstilleroCotizacion()->getIva();
            $notificados = [];
            foreach ($ordenDeTrabajo->getContratistas() as $contratista) {
                $precioTotal += $contratista->getPrecio();
                $utilidadvvTotal += $contratista->getUtilidadvv();
                $preciovvTotal += $contratista->getPreciovv();
                $ivatot = ($contratista->getPrecio() * $iva) / 100;
                $total = $contratista->getPrecio() + $ivatot;
                $porcentajevv = $contratista->getProveedor()->getPorcentaje();
                $contratista
                    ->setPorcentajevv($porcentajevv)
                    ->setIvatot($ivatot)
                    ->setTotal($total);
                $ivaTotal += $ivatot;
                $granTotal += $total;
                if($contratista->getProveedor()->getCorreo()){
                    array_push($notificados,$contratista->getProveedor()->getCorreo());
                }
            }
            $fechaHoraActual = new \DateTime('now');
            $ordenDeTrabajo
                ->setPrecioTotal($precioTotal)
                ->setUtilidadvvTotal($utilidadvvTotal)
                ->setPreciovvTotal($preciovvTotal)
                ->setIvaTotal($ivaTotal)
                ->setGranTotal($granTotal)
                ->setPagosTotal(0)
                ->setSaldoTotal($granTotal)
                ->setFecha($fechaHoraActual);
            $em->persist($ordenDeTrabajo);
            $em->flush();

            //enviar correo para avisar a contratistas y proveedores
            if(!empty($notificados)){
                if($ordenDeTrabajo->getAstilleroCotizacion()->getFoliorecotiza() == 0){
                    $folio = $ordenDeTrabajo->getAstilleroCotizacion()->getFolio();
                }else{
                    $folio = $ordenDeTrabajo->getAstilleroCotizacion()->getFolio().'-'.$ordenDeTrabajo->getAstilleroCotizacion()->getFoliorecotiza();
                }
                $message = (new \Swift_Message('¡Asignación de orden de trabajo!'));
                $message->setFrom('noresponder@novonautica.com');
                $message->setTo($notificados);
                $message->setBody(
                    $this->renderView('mail/asignacionODT.twig', [
                        'folio' => $folio
                    ]),
                    'text/html'
                );
                $mailer->send($message);
            }
            // Buscar correos a notificar libremente
            $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                'evento' => Correo\Notificacion::EVENTO_CREAR,
                'tipo' => Correo\Notificacion::TIPO_ODT
            ]);
            $this->enviaCorreoNotificacion($mailer, $notificables, $ordenDeTrabajo);
            return $this->redirectToRoute('ordendetrabajo_index');
        }

        return $this->render('ordendetrabajo/new.html.twig', array(
            'ordenDeTrabajo' => $ordenDeTrabajo,
            'form' => $form->createView(),
            'title' => 'Nueva Orden de Trabajo'
        ));
    }

    /**
     * @Route("/buscarcotizacion", name="odt_busca_cotizacion")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function buscarCotizacionAction(Request $request)
    {
        $idcotizacion = $request->get('idcotizacion');
        $em = $this->getDoctrine()->getManager();

        $cotizacion = $em->getRepository('AppBundle:AstilleroCotizacion')
            ->createQueryBuilder('ac')
            ->select('ac', 'cliente', 'barco', 'AstilleroCotizaServicio', 'AstilleroServicioBasico', 'AstilleroProducto', 'AstilleroServicio')
            ->join('ac.cliente', 'cliente')
            ->join('ac.barco', 'barco')
            ->join('ac.acservicios', 'AstilleroCotizaServicio')
            ->leftJoin('AstilleroCotizaServicio.astilleroserviciobasico', 'AstilleroServicioBasico')
            ->leftJoin('AstilleroCotizaServicio.producto', 'AstilleroProducto')
            ->leftJoin('AstilleroCotizaServicio.servicio', 'AstilleroServicio')
            ->andWhere('ac.id = ' . $idcotizacion)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $this->json($cotizacion);
    }

    /**
     * Finds and displays a ordenDeTrabajo entity.
     *
     * @Route("/{id}", name="ordendetrabajo_show")
     * @Method({"GET", "POST"})
     *
     * @param OrdenDeTrabajo $ordenDeTrabajo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(OrdenDeTrabajo $ordenDeTrabajo)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT', $ordenDeTrabajo);
        $folio = $ordenDeTrabajo->getAstilleroCotizacion()->getFoliorecotiza()
            ? $ordenDeTrabajo->getAstilleroCotizacion()->getFolio() . '-' . $ordenDeTrabajo->getAstilleroCotizacion()->getFoliorecotiza()
            : $ordenDeTrabajo->getAstilleroCotizacion()->getFolio();
        return $this->render('ordendetrabajo/show.html.twig', [
            'title' => 'Detalle ODT',
            'ordenDeTrabajo' => $ordenDeTrabajo,
            'folio' => $folio
        ]);
    }

    /**
     * Displays a form to edit an existing ordenDeTrabajo entity.
     *
     * @Route("/{id}/editar", name="ordendetrabajo_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param OrdenDeTrabajo $ordenDeTrabajo
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, OrdenDeTrabajo $ordenDeTrabajo)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT_CONTRATISTA_EDIT', $ordenDeTrabajo);

        $precioTotal = 0;
        $utilidadvvTotal = 0;
        $preciovvTotal = 0;
        $ivaTotal = 0;
        $granTotal = 0;
        $saldoTotal = 0;
        $materialesTotal = 0;
        $pagosTotal = 0;
        $em = $this->getDoctrine()->getManager();

        $originalContratistas = new ArrayCollection();
        foreach ($ordenDeTrabajo->getContratistas() as $contratista) {
            $originalContratistas->add($contratista);
        }
        $deleteForm = $this->createDeleteForm($ordenDeTrabajo);
        $editForm = $this->createForm('AppBundle\Form\OrdenDeTrabajoType', $ordenDeTrabajo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $iva = $ordenDeTrabajo->getAstilleroCotizacion()->getIva();
            //$this->getDoctrine()->getManager()->flush();
            foreach ($originalContratistas as $contratista) {
                if (false === $ordenDeTrabajo->getContratistas()->contains($contratista)) {

                    // remove the Task from the Tag
                    $contratista->getAstilleroODT()->removeContratista($contratista);

                    // if it was a many-to-one relationship, remove the relationship like this
                    //$motor->setBarco(null);
                    $em->persist($contratista);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($contratista);
                } else {
                    $precioTotal += $contratista->getPrecio();
                    $utilidadvvTotal += $contratista->getUtilidadvv();
                    $preciovvTotal += $contratista->getPreciovv();
                    $ivatot = ($contratista->getPrecio() * $iva) / 100;
                    $total = $contratista->getPrecio() + $ivatot;
                    $porcentajevv = $contratista->getProveedor()->getPorcentaje();
                    $contratista
                        ->setPorcentajevv($porcentajevv)
                        ->setIvatot($ivatot)
                        ->setTotal($total);
                    $ivaTotal += $ivatot;
                    $granTotal += $total;

                }
            }
            foreach ($ordenDeTrabajo->getContratistas() as $contratistanuevo) {
                if ($contratistanuevo->getId() == null) {
                    $precioTotal += $contratistanuevo->getPrecio();
                    $utilidadvvTotal += $contratistanuevo->getUtilidadvv();
                    $preciovvTotal += $contratistanuevo->getPreciovv();

                    $ivatot = ($contratistanuevo->getPrecio() * $iva) / 100;
                    $total = $contratistanuevo->getPrecio() + $ivatot;
                    $porcentajevv = $contratistanuevo->getProveedor()->getPorcentaje();
                    $contratistanuevo
                        ->setPorcentajevv($porcentajevv)
                        ->setIvatot($ivatot)
                        ->setTotal($total);
                    $ivaTotal += $ivatot;
                    $granTotal += $total;
                }
            }
            $ordenDeTrabajo
                ->setPrecioTotal($precioTotal)
                ->setUtilidadvvTotal($utilidadvvTotal)
                ->setPreciovvTotal($preciovvTotal)
                ->setSaldoTotal($granTotal)
                ->setIvaTotal($ivaTotal)
                ->setGranTotal($granTotal);
            $em->persist($ordenDeTrabajo);
            $em->flush();

            return $this->redirectToRoute('ordendetrabajo_show', ['id' => $ordenDeTrabajo->getId()]);
        }

        return $this->render('ordendetrabajo/edit.html.twig', [
            'title' => 'Editar ODT',
            'ordenDeTrabajo' => $ordenDeTrabajo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/actividad", name="ordendetrabajo_contratista_actividad")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Contratista $contratista
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function actividadAction(Request $request, Contratista $contratista)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT_ACTIVIDAD', $contratista);
        $originalActividades = new ArrayCollection();
        $oldFotos = new ArrayCollection();

        /** @var Contratista\Actividad $actividad */
        foreach ($contratista->getContratistaactividades() as $a => $actividad) {
            $originalActividades->add($actividad);

            $realOldFotos = new ArrayCollection();

            foreach ($actividad->getFotos() as $foto) {
                $realOldFotos->add($foto);
            }

            $oldFotos->add($realOldFotos);
        }

        $editForm = $this->createForm('AppBundle\Form\Astillero\ContratistaActividadType', $contratista);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($originalActividades as $a => $actividad) {
                if (!$contratista->getContratistaactividades()->contains($actividad)) {
                    $actividad->setContratista(null);
                    $em->remove($actividad);
                }

                foreach ($oldFotos[$a] as $dFoto) {
                    if (!$actividad->getFotos()->contains($dFoto)) {
                        $dFoto->setActividad(null);
                        $em->remove($dFoto);
                    }
                }
            }

            foreach ($contratista->getContratistaactividades() as $act1) {
                $ban = false;

                foreach ($originalActividades as $act2) {
                    if ($act2->getId() == $act1->getId()) {
                        $ban = true;
                    }
                }

                if (!$ban) {
                    $act1->setUsuario($this->getUser()->getNombre());
                }
            }

            $em->persist($contratista);
            $em->flush();

            return $this->redirectToRoute('ordendetrabajo_show', ['id' => $contratista->getAstilleroODT()->getId()]);
        }

        return $this->render('ordendetrabajo/actividad.html.twig', [
            'title' => 'Registrar Actividad Contratista',
            'contratista' => $contratista,
            'edit_form' => $editForm->createView()
        ]);
    }

    /**
     * @Route("/{id}/pausa-actividad/", name="ordendetrabajo_contratista_pausa-actividad")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Actividad $actividad
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function pausaActividadAction(Request $request, Actividad $actividad)
    {
        $pausa = new Pausa();
        $form = $this->createForm('AppBundle\Form\Astillero\Contratista\Actividad\PausaType', $pausa);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fechaPausa = new \DateTime('now');
            if($fechaPausa->format('d-m-Y') >= $actividad->getInicio()->format('d-m-Y') && $fechaPausa->format('d-m-Y') <= $actividad->getFin()->format('d-m-Y')){
                $em = $this->getDoctrine()->getManager();
                $pausa
                    ->setInicio($fechaPausa)
                    ->setRegistro($fechaPausa)
                    ->setCreador($this->getUser());
                $actividad->addPausa($pausa);
                $actividad->setIsPausado(true);
                $em->persist($pausa);
                $em->persist($actividad);
                $em->flush();
                return $this->redirectToRoute('ordendetrabajo_show', ['id' => $actividad->getContratista()->getAstilleroODT()->getId()]);
            }else{
                $this->addFlash('notice', 'Error! la actividad que ha intenta pausar no esta vigente');
            }
        }
        return $this->render('ordendetrabajo/pausa.html.twig',[
            'title' => 'Pausando actividad',
            'actividad' => $actividad,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/reanuda-actividad/", name="ordendetrabajo_contratista_reanuda-actividad")
     * @Method({"GET", "POST"})
     *
     * @param Actividad $actividad
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function reanudaActividadAction(Actividad $actividad)
    {
        $em = $this->getDoctrine()->getManager();
        $pausaActual = $actividad->getPausas()->last();
        $pausaActual->setFin(new \DateTime('now'));
        $diasPausa = $pausaActual->getInicio()->diff($pausaActual->getFin())->format("%a");
        $nuevoFinActividad = $actividad->getFin()->add(new \DateInterval('P'.$diasPausa.'D'));
        $actividad
            ->setIsPausado(false)
            ->setFin(new \DateTime($nuevoFinActividad->format('Y-m-d')));
        $em->persist($actividad);
        $em->persist($pausaActual);
        $em->flush();
        return $this->redirectToRoute('ordendetrabajo_show', ['id' => $actividad->getContratista()->getAstilleroODT()->getId()]);
    }

    /**
     * @Route("/{id}/pdf-contratista", name="odt-contratista-pdf")
     * @Method("GET")
     *
     * @param OrdenDeTrabajo $odt
     *
     * @return PdfResponse
     */
    public function displayODTpdf(OrdenDeTrabajo $odt)
    {
        $html = $this->renderView('ordendetrabajo/pdf/contenido.html.twig', [
            'odt' => $odt
        ]);
        $header = $this->renderView('ordendetrabajo/pdf/encabezado.html.twig', [
            'astillero' => $odt->getAstilleroCotizacion()
        ]);
        $footer = $this->renderView('ordendetrabajo/pdf/pie.html.twig');
        $hojapdf = $this->get('knp_snappy.pdf');
        $options = [
            'margin-top' => 19,
            'margin-right' => 0,
            'margin-left' => 0,
            'header-html' => utf8_decode($header),
            'footer-html' => utf8_decode($footer)
        ];

        return new PdfResponse(
            $hojapdf->getOutputFromHtml($html, $options),
            'Cotizacion-' . $odt->getAstilleroCotizacion()->getFolio() . '-' . $odt->getAstilleroCotizacion()->getFoliorecotiza() . '.pdf',
            'application/pdf',
            'inline'
        );
    }

    /**
     * Deletes a ordenDeTrabajo entity.
     *
     * @Route("/{id}", name="ordendetrabajo_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param OrdenDeTrabajo $ordenDeTrabajo
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, OrdenDeTrabajo $ordenDeTrabajo)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT_DELETE', $ordenDeTrabajo);

        $form = $this->createDeleteForm($ordenDeTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ordenDeTrabajo);
            $em->flush();
        }

        return $this->redirectToRoute('ordendetrabajo_index');
    }

    /**
     * Creates a form to delete a ordenDeTrabajo entity.
     *
     * @param OrdenDeTrabajo $ordenDeTrabajo The ordenDeTrabajo entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(OrdenDeTrabajo $ordenDeTrabajo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordendetrabajo_delete', ['id' => $ordenDeTrabajo->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Correo\Notificacion[] $notificables
     * @param OrdenDeTrabajo $odt
     * @param \Swift_Mailer $mailer
     *
     * @return void
     */
    private function enviaCorreoNotificacion($mailer, $notificables, $odt)
    {
        if (!count($notificables)) { return; }
        $recipientes = [];
        foreach ($notificables as $key => $notificable) {
            $recipientes[$key] = $notificable->getCorreo();
        }
        $message = (new \Swift_Message('¡Orden de trabajo!'));
        $message->setFrom('noresponder@novonautica.com');
        $message->setTo($recipientes);
        $message->setBody(
            $this->renderView('mail/notificacion-odt.html.twig', [
                'notificacion' => $notificables[0],
                'odt' => $odt
            ]),
            'text/html'
        );

        $mailer->send($message);
    }
}
