<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Validator;
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
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly Validator $validator,
    ) {}

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
    public function register(Request $request): Response
    {
        $email = $request->request->get('email', null);
        $username = $request->request->get('username', null);

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password', null);
            $confirm = $request->request->get('confirm', null);

            if (
                $this->validator->validatePassword($password, $confirm)
                && $this->validator->validateEmail($email)
            ) {
                $user = (new User())
                    ->setUsername($username)
                    ->setEmail($email)
                    ->setPassword($password);

                $this->em->persist($user);
                $this->em->flush();

                $this->addFlash('success', 'Compte créé');

                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Erreur lors de la création du compte, veuillez réessayer');
        }

        return $this->render('auth/register.html.twig', [
            'email' => $email,
            'username' => $username,
        ]);
    }

    #[Route('/forgot-password', name: 'forgot')]
    public function forgot(
        Request $request,
        MailerInterface $mailer,
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email', null);
            $error = true;

            if ($email) {
                $user = $this->userRepository->findOneBy(['email' => $email]);
                if ($user) {
                    $token = Uuid::v4();
                    $user->setResetToken($token);
                    $this->em->flush();

                    $template = (new TemplatedEmail())
                        ->to($email)
                        ->from('team@tutorials.fr')
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
            $password = $request->request->get('password', null);
            $confirm = $request->request->get('confirm', null);
            if ($this->validator->validatePassword($password, $confirm)) {
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
