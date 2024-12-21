<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home()
    {
        $user = $this->getUser();
        if ($user !== null && !$user instanceof User) {
            throw new \LogicException('User must be null or an instance of User');
        }

        return $this->render('home.html.twig', [
            'banned' => !!$user && $user->isBanned(),
        ]);
    }
}
