<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/paiement-securise', name: 'app_payment')]
    public function index(): Response
    {
        if (!$this->getUser()) return $this->redirectToRoute('app_login');
        if (count($this->getUser()->getAddresses()) === 0) return $this->redirectToRoute('app_user_profile');
        
        return $this->render('payment/index.html.twig');
    }
}
