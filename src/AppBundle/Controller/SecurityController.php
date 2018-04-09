<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     *
     * @param AuthenticationUtils $authUtils
     *
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authUtils)
    {
       return $this->render(':security:login.html.twig', [
          'error' => $authUtils->getLastAuthenticationError(),
          'last_username' => $authUtils->getLastUsername()
       ]);
    }

    /**
     * @Route("clients/login", name="clients_login")
     *
     * @param AuthenticationUtils $authUtils
     *
     * @return Response
     */
    public function clientsLoginAction(AuthenticationUtils $authUtils)
    {
       return $this->render(':security:cliente-login.html.twig', [
          'error' => $authUtils->getLastAuthenticationError(),
          'last_username' => $authUtils->getLastUsername()
       ]);
    }

    /**
     * @Route("/forgot", name="password_forgot")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function passwordForgotAction(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createFormBuilder([])
            ->add('account', TextType::class, ['label' => 'Correo o nombre de usuario'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $username = $form->getData()['account'];
            $usuario = $em->getRepository('AppBundle:Usuario')->loadUserByUsername($username);

            if ($usuario instanceof Usuario) {
                $expiracion = new \DateTimeImmutable();
                $usuario->setRecoveryPasswordToken(bin2hex(random_bytes(20)));
                $usuario->setPasswordTokenExpiration($expiracion->add(new \DateInterval('PT1H')));
                $em->flush();

                $message = (new \Swift_Message('Solicitaste recuperar tu contraseña de Novonautica'))
                    ->setFrom('noresponder@novonautica.com')
                    ->setTo($usuario->getCorreo())
                    ->setBody(
                        $this->renderView(':mail:recover.html.twig', ['usuario' => $usuario]),
                        'text/html'
                    );
                $mailer->send($message);
            }

            $this->addFlash('warning', 'Te hemos mandado un email, por favor verifica tu bandeja de spam');

            return $this->redirectToRoute('login');
        }

        return $this->render(':security:forgot-password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset/{token}", name="password_reset")
     *
     * @param Request $request
     * @param string $token
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function passwordRecoverAction(Request $request, $token)
    {
        if (!$token) {
            $this->addFlash('warning', 'El token es invalido o ha expirado.');
            return $this->redirectToRoute('login');
        }

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('AppBundle:Usuario')->getUserByToken($token);

        if (null === $usuario) {
            $this->addFlash('warning', 'El token es invalido o ha expirado.');
            return $this->redirectToRoute('login');
        }

        $form = $this->createFormBuilder([])
            ->add('password', PasswordType::class, ['label' => 'Nueva contraseña'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->getData()['password'];

            $usuario->setPlainPassword($newPassword);
            $usuario->setRecoveryPasswordToken(null);
            $usuario->setPasswordTokenExpiration(null);

            $em->flush();

            $this->addFlash('notice', 'Tu contraseña se ha actualizado satisfactoriamente.');

            return $this->redirectToRoute('login');
        }

        return $this->render(':security:forgot-password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}