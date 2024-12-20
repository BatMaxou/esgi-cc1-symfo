<?php

namespace App\Controller\Auth;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

class SecurityController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    #[Route('/forgot-password', name: 'forgot')]
    public function forgot(
        Request $request,
        MailerInterface $mailer,
    ): Response {
        $email = $request->request->get('_email', null);
        $error = true;

        if ($email) {
            $user = $this->userRepository->findOneBy(['email' => $email]);
            if ($user) {
                $token = Uuid::v4();
                $user->setResetToken($token);
                $this->em->flush();

                $template = (new TemplatedEmail())
                    ->to($email)
                    ->from('team@streami.fr')
                    ->subject('Réinitialisation de mot de passe')
                    ->htmlTemplate('email/forgot.html.twig')
                    ->context([
                        'token' => $token,
                        'username' => $user->getUsername(),
                    ]);
                $mailer->send($template);

                $this->addFlash('info', 'Un email vous a été envoyé');
                $error = false;
            }
        }

        if ($error) {
            $this->addFlash('error', 'Utilisateur introuvable');
        }

        return $this->render('auth/forgot.html.twig');
    }

    #[Route('/reset', name: 'reset')]
    public function reset(Request $request): Response
    {
        $token = $request->query->get('token', null);
        if (!$token) {
            return $this->redirectToRoute('home');
        }

        $user = $this->userRepository->findOneBy(['resetToken' => $token]);
        if (!$user) {
            $this->addFlash('error', 'Token invalide');

            return $this->redirectToRoute('home');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('_password', null);
            $confirm = $request->request->get('_confirm', null);
            if ($password && $confirm && $password === $confirm) {
                $user->setPassword($password);
                $user->setResetToken(null);
                $this->em->flush();

                $this->addFlash('info', 'Mot de passe mis à jour');

                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Les mots de passe ne correspondent pas');
        }

        return $this->render('auth/reset.html.twig', [
            'email' => $user->getEmail(),
        ]);
    }

    #[Route('/confirm', name: 'confirm')]
    public function confirm(): Response
    {
        return $this->render('auth/confirm.html.twig');
    }
}
