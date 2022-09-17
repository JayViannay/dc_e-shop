<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\UserOrder;
use App\Form\PaymentFormType;
use App\Repository\ArticleRepository;
use App\Repository\ReferenceRepository;
use App\Repository\TicketRepository;
use App\Repository\UserOrderRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/cart', name: 'shop_cart')]
    public function index(
        Request $request,
        UserOrderRepository $userOrderRepository,
        CartService $cartService,
        TicketRepository $ticketRepository): Response
    {
        $order = new UserOrder();
        $order->setUser($this->getUser());

        $orderForm = $this->createForm(PaymentFormType::class, $order);
        $orderForm->handleRequest($request);

        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            $order->setTotal($cartService->getTotalCart());
            $userOrderRepository->add($order, true);
            
            $articles = $cartService->getCartItems();
            foreach ($articles as $article) {
                $ticket = new Ticket();
                $ticket->setReferenceOrder($order)->setArticle($article['article'])->setQty($article['quantity']);
                $ticketRepository->add($ticket, true);
            }
            
            $this->addFlash('success', 'Félicitations, votre commande est validée !');
            $cartService->cleanCart();

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('cart/index.html.twig', [
            'items' => $this->cartService->getCartItems(),
            'total' => $this->cartService->getTotalCart(),
            'orderForm' => $orderForm->createView(),
        ]);
    }

    #[Route('/cart/add', name: 'shop_cart_add')]
    public function add(Request $request, ArticleRepository $articleRepository, ReferenceRepository $referenceRepository)
    {
        $ref = $referenceRepository->find($request->get('ref'));
        $size = $request->get('size');
        $color = $request->get('color');

        if (!$size) $this->addFlash('danger', 'Size are required !');
        if (!$color) $this->addFlash('danger', 'Color are required !');
        
        $article = $articleRepository->findOneByParams($ref->getId(), $size, $color);
        if ($article && $article->getQty() > 0) {
            $this->cartService->add($article->getId());
            $this->addFlash('success', 'Article added to cart!');
            return $this->redirectToRoute('shop_show', ['slug' => $ref->getSlug()]);
        } else {
            $this->addFlash('danger', 'Article not available anymore!');
        }
  
        return $this->redirectToRoute('shop_show', ['slug' => $ref->getSlug()]);
    }

    #[Route('/cart/remove/{id}', name: 'shop_cart_remove')]
    public function remove(int $id)
    {
        $this->cartService->remove($id);
        return $this->redirectToRoute('shop_cart');
    }
}
