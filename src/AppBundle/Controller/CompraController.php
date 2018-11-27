<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 12/11/2018
 * Time: 12:32 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Solicitud;
use AppBundle\Entity\Correo;
use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

/**
 * Class CompraController
 * @Route("compra")
 */
class CompraController extends Controller
{
    /**
     * @Route("/", name="compra_index")
     * @Method("GET")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     * @return JsonResponse|Response
     */
    public function indexCompraAction(Request $request, DataTablesInterface $dataTables)
    {
        if($request->isXmlHttpRequest()){
            try{
                $results = $dataTables->handle($request,'compra');
                return $this->json($results);
            } catch(HttpException $e){
                return $this->json($e->getMessage(),$e->getStatusCode());
            }
        }
        return $this->render('compra/index.html.twig',['title' => 'Compras']);
    }

    /**
     * @Route("/{id}", name="compra_show")
     * @Method("GET")
     * @param Solicitud $solicitud
     * @return Response
     */
    public function showAction(Solicitud $solicitud)
    {
        return $this->render('compra/show.html.twig',[
            'title' => 'Detalle compra',
            'solicitud' => $solicitud
        ]);
    }

    /**
     * @Route("/{id}/editar", name="compra_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Solicitud $solicitud
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editCompraAction(Request $request, Solicitud $solicitud, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();

        $this->denyAccessUnlessGranted('COMPRA_EDIT',$solicitud);

        if($solicitud->getValidadoCompra()){ throw new NotFoundHttpException(); }
        if(!$solicitud->getIva()){
            $valor = $em->getRepository('AppBundle:ValorSistema')->findAll()[0];
            $solicitud->setIva($valor->getIva());
        }
        $editForm = $this->createForm('AppBundle\Form\CompraType',$solicitud);
        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            $em->persist($solicitud);
            $em->flush();

            $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                    'evento' => Correo\Notificacion::EVENTO_EDITAR,
                    'tipo' => Correo\Notificacion::TIPO_COMPRA
            ]);
            $this->enviaCorreoNotificacion($mailer,$notificables,$solicitud,
                'Notificación de compras - Asignación de proveedor');
            return $this->redirectToRoute('compra_show',['id' => $solicitud->getId()]);
        }
        return $this->render('compra/edit.html.twig',[
            'form' => $editForm->createView(),
            'solicitud' => $solicitud,
            'title' => 'Compra editar'
        ]);
    }

    /**
     * @Route("/{id}/validar", name="compra_validar")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Solicitud $solicitud
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function validarAction(Request $request, Solicitud $solicitud, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $this->denyAccessUnlessGranted('COMPRA_VALIDAR',$solicitud);

        if($solicitud->getValidadoCompra()){ throw new NotFoundHttpException(); }

        $editForm = $this->createForm('AppBundle\Form\Compra\ValidarType',$solicitud);
        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){

            if(!is_null($solicitud->getValidadoCompra())){
                $solicitud->setNombreValidoCompra($this->getUser()->getNombre());
                $solicitud->setFechaValidoCompra(new \DateTime());

                //Buscar correos a notificar, el que creo la solicitud le llegará un correo si es rechazado.
                //Si es aceptado llegará correo a los asignados con EVENTO_VALIDAR y EVENTO_ACEPTAR
                if($solicitud->getValidadoCompra()){
                   $asunto = 'Notificación compras - Aceptado';
                    $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                        'evento' => [Correo\Notificacion::EVENTO_VALIDAR, Correo\Notificacion::EVENTO_ACEPTAR],
                        'tipo' => Correo\Notificacion::TIPO_COMPRA
                    ]);
                    $proveedores = $em->getRepository('AppBundle:Solicitud\Concepto')->getCorreoProveedores($solicitud);
                    foreach ($proveedores as $proveedor){
                        $this->enviaCorreoProveedor($mailer,$proveedor['correo'],$solicitud,$proveedor['id']);
                    }
                }else{
                    $asunto = 'Notificación compras - Rechazado';
                    $notificables = [$solicitud->getCreador()];
                }
                $this->enviaCorreoNotificacion($mailer, $notificables, $solicitud, $asunto);

            }

            $em->persist($solicitud);
            $em->flush();
            return $this->redirectToRoute('compra_show',['id' => $solicitud->getId()]);
        }
        return $this->render('compra/validar.html.twig',[
            'form' => $editForm->createView(),
            'solicitud' => $solicitud,
            'title' => 'Compra validar'
        ]);
    }

    /**
     * @Route("/{id}/pdf", name="compra_pdf")
     * @Method("GET")
     *
     * @param Solicitud $solicitud
     * @return PdfResponse
     */
    public function displayPdfAction(Solicitud $solicitud)
    {
        $em = $this->getDoctrine()->getManager();
        $valor = $em->getRepository('AppBundle:ValorSistema')->find(1);
        $html = $this->renderView('compra/pdf/body.html.twig',['solicitud' => $solicitud]);
        $header = $this->renderView('compra/pdf/header.html.twig');
        $footer = $this->renderView('compra/pdf/footer.html.twig',['valor' => $valor]);

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
            'compra-' . $solicitud->getFolio() . '.pdf',
            'application/pdf',
            'inline'
        );
    }

    /**
     * @param \Swift_Mailer $mailer
     * @param Correo\Notificacion[] $notificables
     * @param Solicitud $solicitud
     * @param $asunto
     *
     * @return void
     */
    private function enviaCorreoNotificacion($mailer, $notificables, $solicitud, $asunto)
    {
        if (!count($notificables)) {
            return;
        }

        $recipientes = [];
        foreach ($notificables as $key => $notificable) {
            $recipientes[$key] = $notificable->getCorreo();
        }

        $message = (new \Swift_Message($asunto));
        $message->setFrom('noresponder@novonautica.com');
        $message->setTo($recipientes);

        $message->setBody(
            $this->renderView('mail/compra.html.twig', [
                'notificacion' => $notificables[0],
                'solicitud' => $solicitud,
                'asunto' => $asunto
            ]),
            'text/html'
        );
        $mailer->send($message);
    }

    /**
     * @param \Swift_Mailer $mailer
     * @param $correo
     * @param $solicitud
     * @param $idproveedor
     * @return void
     */
    private function enviaCorreoProveedor($mailer,$correo,$solicitud,$idproveedor)
    {
        if (is_null($correo)) {
            return;
        }
        $message = (new \Swift_Message('Solicitud de productos'));
        $message->setFrom('noresponder@novonautica.com');
        $message->setTo($correo);
        $message->setBody(
            $this->renderView('mail/compra-proveedor.html.twig', [
                'solicitud' => $solicitud,
                'idproveedor' => $idproveedor
            ]),
            'text/html'
        );
        $mailer->send($message);

    }
}