<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\Correo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="sidebar_expantion")
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
     * @Route("clients/", name="clients_index")
     */
    public function clientsAction()
    {
        return $this->render(':default:index.html.twig', ['title' => 'Bienvenido']);
    }

    /**
     * Confirma la respuesta de un cliente a una cotizacion de marina
     *
     * @Route("clients/cotizacion/{token}", name="clientes_cotizacion")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param string $token
     * @param \Swift_Mailer $mailer
     *
     * @return Response
     * @throws \Exception
     */
    public function repuestaClienteAction(Request $request, $token, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $cotizacion = $em->getRepository('AppBundle:MarinaHumedaCotizacion')
            ->findOneBy([
                'token' => $token,
                'validacliente' => 0
            ])
            ?: $em->getRepository('AppBundle:AstilleroCotizacion')
                ->findOneBy([
                    'token' => $token,
                    'validacliente' => 0
                ])
                ?: $em->getRepository('AppBundle:Combustible')
                    ->findOneBy([
                        'token' => $token,
                        'validacliente' => 0
                    ]);

        if (null === $cotizacion || $cotizacion->getCliente() !== $this->getUser()) {
            throw new NotFoundHttpException('No se encontro la cotización');
        }
        if((new \DateTime('now'))->setTime(0,0,0,0) > $cotizacion->getLimiteValidaCliente()->setTime(0,0,0,0)){
            throw new NotFoundHttpException('Cotización caducada');
        }
        $form = $this->createFormBuilder([])
            ->add('action', ChoiceType::class, [
                'label' => 'Seleccione una opción para continuar:',
                'expanded' => true,
                'data' => true,
                'choices' => [
                    'Aceptar' => true,
                    'Rechazar' => false
                ],
            ])
            ->add('comentarios', TextareaType::class, [
                'required' => false
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $response = $form->getData()['action'];
            $comentarios = $form->getData()['comentarios'];

            $cotizacion->setQuienAcepto($this->getUser()->getNombre());
            $cotizacion->setRegistroValidaCliente(new \DateTimeImmutable());

            if (!$response) {
                $cotizacion->setValidacliente(1);
                $cotizacion->setNotascliente($comentarios);
            } else {
                $cotizacion->setValidacliente(2);
            }

            $em->flush();

            // Buscar correos a notificar
            $notificables = $em->getRepository('AppBundle:Correo\Notificacion')->findBy([
                'evento' => Correo\Notificacion::EVENTO_ACEPTAR,
                'tipo' => $cotizacion instanceof AstilleroCotizacion ? Correo\Notificacion::TIPO_ASTILLERO : Correo\Notificacion::TIPO_MARINA,
            ]);

            $this->enviaCorreoNotificacion($mailer, $notificables, $cotizacion);

            return $this->redirectToRoute('clientes_gracias');
        }

        return $this->render('default/cotizacion.html.twig', [
            'title' => 'Confirmación de cotización',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("clients/gracias", name="clientes_gracias")
     * @Method("GET")
     *
     * @return Response
     */
    public function graciasClienteAction()
    {
        return $this->render('default/gracias.html.twig', [
            'title' => '¡Gracias por su respuesta!'
        ]);
    }

    /**
     * @param Correo\Notificacion[] $notificables
     * @param MarinaHumedaCotizacion|AstilleroCotizacion $cotizacion
     * @param \Swift_Mailer $mailer
     *
     * @return void
     */
    private function enviaCorreoNotificacion($mailer, $notificables, $cotizacion)
    {
        if (!count($notificables)) {
            return;
        }

        $recipientes = [];
        foreach ($notificables as $key => $notificable) {
            $recipientes[$key] = $notificable->getCorreo();
        }

        $message = (new \Swift_Message('¡Cotizacion de servicios Astillero!'));
        $message->setFrom('noresponder@novonautica.com');
        $message->setTo($recipientes);

        $message->setBody(
            $this->renderView('mail/notificacion.html.twig', [
                'notificacion' => $notificables[0],
                'cotizacion' => $cotizacion
            ]),
            'text/html'
        );

        $mailer->send($message);
    }
}
