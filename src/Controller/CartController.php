<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\ReferenceRepository;
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
