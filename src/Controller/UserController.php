<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'app_user_profile')]
    public function userProfile(): Response
    {
        if ($this->getUser()) return $this->render('user/index.html.twig');
        return $this->redirectToRoute('app_login');
    }
}
