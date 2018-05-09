<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/12/18
 * Time: 12:04
 */

namespace AppBundle\Controller\Cliente;

use AppBundle\Entity\Cliente\Notificacion;
use DataTables\DataTablesInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class NotificacionController
 * @package AppBundle\Controller\Cliente
 *
 * @Route("/cliente/notificacion")
 */
class NotificacionController extends AbstractController
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="cliente_notificacion_index")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $notificacion = new Notificacion();
        $notificacion->setUsuario($this->getUser());

        $em = $this->getDoctrine()->getManager();

        $clienteRepository = $em->getRepository('AppBundle:Cliente');
        $cliente = null === ($request->query->get('u')) ? null : $clienteRepository->find($request->query->get('u'));
        $folio = $request->query->get('f');

        $notificacion->setCliente($cliente);
        $notificacion->setFolioCotizacion($folio);

        $form = $this->createForm('AppBundle\Form\Cliente\NotificacionType', $notificacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($notificacion);
            $em->flush();

            $this->sendNotice($notificacion);

            return $this->redirectToRoute('cliente_notificacion_index');
        }

        return $this->render(':cliente/notificacion:index.html.twig', [
            'title' => 'Notificaciones',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/notificaciones", name="cliente_notificacion_index_data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'clienteNotificacion');
            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    private function sendNotice(Notificacion $notificacion)
    {
        $message = new \Swift_Message($notificacion->getNamedTipo());
        $message->setFrom('noresponder@novonautica.com');
        $message->setTo($notificacion->getCliente()->getCorreo());

        $message->setBody(
            $this->renderView('mail/aviso.html.twig', [
                'notificacion' => $notificacion
            ]),
            'text/html'
        );

        $this->mailer->send($message);
    }
}