<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 31/10/2018
 * Time: 03:39 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Solicitud;
use AppBundle\Entity\Correo;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SolicitudController
 * @Route("solicitud")
 */
class SolicitudController extends Controller
{
    /**
     * @Route("/", name="solicitud_index")
     * @Method("GET")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     * @return JsonResponse|Response
     */
    public function indexSolicitudAction(Request $request, DataTablesInterface $dataTables)
    {
        if($request->isXmlHttpRequest()){
            try{
                $results = $dataTables->handle($request,'solicitud');
                return $this->json($results);
            } catch(HttpException $e){
                return $this->json($e->getMessage(),$e->getStatusCode());
            }
        }
        return $this->render('solicitud/index.html.twig',['title' => 'Solicitudes']);
    }

    /**
     * @Route("/nuevo", name="solicitud_new")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return RedirectResponse|Response
     */
    public function newSolicitudAction(Request $request, \Swift_Mailer $mailer)
    {
        $solicitud = new Solicitud();
        $this->denyAccessUnlessGranted('SOLICITUD_CREATE',$solicitud);
        $form = $this->createForm('AppBundle\Form\SolicitudType',$solicitud);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $variable = $em->getRepository('AppBundle:ValorSistema')->findAll()[0];
            $folioNuevo = $variable->getFolioSolicitud() + 1;
            $solicitud->setFolio($folioNuevo);
            $solicitud->setFecha(new \DateTime('now'));
            $solicitud->setCreador($this->getUser());
            $variable->setFolioSolicitud($folioNuevo);
            $em->persist($solicitud);
            $em->persist($variable);
            $em->flush();

            //Buscar correos a notificar
            $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                'evento' => Correo\Notificacion::EVENTO_CREAR,
                'tipo' => Correo\Notificacion::TIPO_SOLICITUD
            ]);
            $this->enviaCorreoNotificacion($mailer,$notificables,$solicitud);

            return $this->redirectToRoute('solicitud_show',['id' => $solicitud->getId()]);
        }

        return $this->render('solicitud/edit.html.twig',[
            'form' => $form->createView(),
            'title' => 'Nueva solicitud',
        ]);
    }

    /**
     * @Route("/{id}", name="solicitud_show")
     * @Method("GET")
     * @param Solicitud $solicitud
     * @return Response
     */
    public function showSolicitudAction(Solicitud $solicitud)
    {
        $em = $this->getDoctrine()->getManager();
        $permiso = $em->getRepository(Solicitud::class)
            ->compruebaRol($this->getUser()->getRoles(),$solicitud->getEmpresa()->getId());
        if(!$permiso){ throw new NotFoundHttpException(); }
        return $this->render('solicitud/show.html.twig',[
            'title' => 'Detalle solicitud',
            'solicitud' => $solicitud
        ]);
    }

    /**
     * @Route("/{id}/editar", name="solicitud_edit")
     * @Method({"GET", "POST"})
     */
    public function editSolicitudAction(Request $request, Solicitud $solicitud)
    {
        $this->denyAccessUnlessGranted('SOLICITUD_EDIT',$solicitud);

        $em = $this->getDoctrine()->getManager();
        $permiso = $em->getRepository(Solicitud::class)
            ->compruebaRol($this->getUser()->getRoles(),$solicitud->getEmpresa()->getId());
        if(!$permiso || $solicitud->getValidadoCompra()){ throw new NotFoundHttpException(); }

        $solicitud = $em->getRepository(Solicitud::class)->find($solicitud->getId());
        $originalConceptos = new ArrayCollection();
        foreach ($solicitud->getConceptos() as $concepto){
            $originalConceptos->add($concepto);
        }
        $deleteForm = $this->createDeleteForm($solicitud);
        $editForm = $this->createForm('AppBundle\Form\SolicitudType',$solicitud);
        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            foreach ($originalConceptos as $concepto){
                if(false === $solicitud->getConceptos()->contains($concepto)){
                    $concepto->getSolicitud()->removeConcepto($concepto);
                    $em->persist($concepto);
                    $em->remove($concepto);
                }
            }
            $em->persist($solicitud);
            $em->flush();
            return $this->redirectToRoute('solicitud_show',['id' => $solicitud->getId()]);
        }

        return $this->render('solicitud/edit.html.twig',[
            'form' => $editForm->createView(),
            'solicitud' => $solicitud,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Editar solicitud'
        ]);
    }

    /**
     * @Route("/solicitud/{id}", name="solicitud_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Solicitud $solicitud
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Solicitud $solicitud)
    {
        $this->denyAccessUnlessGranted('SOLICITUD_DELETE',$solicitud);
        $form = $this->createDeleteForm($solicitud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($solicitud);
            $em->flush();
        }

        return $this->redirectToRoute('solicitud_index');
    }

    /**
     * @param Solicitud $solicitud
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Solicitud $solicitud)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('solicitud_delete', ['id' => $solicitud->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Correo\Notificacion[] $notificables
     * @param Solicitud $solicitud
     * @param \Swift_Mailer $mailer
     *
     * @return void
     */
    private function enviaCorreoNotificacion($mailer, $notificables, $solicitud)
    {
        if (!count($notificables)) {
            return;
        }

        $recipientes = [];
        foreach ($notificables as $key => $notificable) {
            $recipientes[$key] = $notificable->getCorreo();
        }

        $message = (new \Swift_Message('¡Solicitud de productos!'));
        $message->setFrom('noresponder@novonautica.com');
        $message->setTo($recipientes);

        $message->setBody(
            $this->renderView('mail/solicitud-nueva.html.twig', [
                'notificacion' => $notificables[0],
                'solicitud' => $solicitud
            ]),
            'text/html'
        );
        $mailer->send($message);
    }
}