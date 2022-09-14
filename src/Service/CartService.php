<?php

namespace App\Service;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;
    private $articleRepository;

    public function __construct(RequestStack $requestStack, ArticleRepository $articleRepository)
    {
        $this->requestStack = $requestStack;
        $this->articleRepository = $articleRepository;
    }

    /**
     * add item in cart
     */
    public function add(int $id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        !empty($cart[$id]) ?  $cart[$id]++ : $cart[$id] = 1;
        $this->requestStack->getSession()->set('cart', $cart);
    }

    /**
     * remove item from cart
     */
    public function remove(int $id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if(!empty($cart[$id])) unset($cart[$id]);
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function getCartItems()
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $cartInfos = [];
        foreach($cart as $id => $quantity){
            $cartInfos[] = [
                'article' => $this->articleRepository->find($id),
                'quantity' => $quantity
            ];
        }
        return $cartInfos;
    }

    public function getTotalCart()
    {
        $total = 0;
        foreach ($this->getCartItems() as $item){
            $total += $item['quantity'] * $item['article']->getReference()->getPrice()->getAmount();
        }
        return $total;
    }

}