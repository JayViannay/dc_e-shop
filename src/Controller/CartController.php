<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
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
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $this->cartService->getCartItems(),
            'total' => $this->cartService->getTotalCart()
        ]);
    }

    #[Route('/cart/add', name: 'shop_cart_add')]
    public function add(Request $request, ArticleRepository $articleRepository)
    {
        $article = $articleRepository->findOneByParams($request->get('ref'), $request->get('size'), $request->get('color'));
        if ($article && $article->getQty() > 0) {
            $this->cartService->add($article->getId());
            $this->addFlash('success', 'Article added to cart!');
        } else {
            $this->addFlash('success', 'Article not available anymore!');
        }
  
        return $this->redirectToRoute('app_reference_show', ['slug' => $article->getReference()->getSlug()]);
    }

    #[Route('/cart/remove/{id}', name: 'shop_cart_remove')]
    public function remove(int $id)
    {
        $this->cartService->remove($id);
        return $this->redirectToRoute('shop_cart');
    }
}
