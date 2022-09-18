<?php

namespace App\Controller;

use App\Entity\Reference;
use App\Entity\Wishlist;
use App\Repository\ArticleRepository;
use App\Repository\ReferenceRepository;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    private $referenceRepository;
    private $articleRepository;

    public function __construct(ReferenceRepository $referenceRepository, ArticleRepository $articleRepository)
    {
        $this->referenceRepository = $referenceRepository;
        $this->articleRepository = $articleRepository;
    }

    #[Route('/', name: 'shop_home')]
    public function index(): Response
    {
        return $this->render('shop/index.html.twig', [
            'refs' => $this->referenceRepository->findAll(),
        ]);
    }

    #[Route('/article/{slug}', name: 'shop_show')]
    public function show(Reference $reference): Response
    {
        return $this->render('shop/show.html.twig', [
            'ref' => $reference,
        ]);
    }

    #[Route('/add/wishlist/{id}', name: 'shop_wishlist_add', methods: ['POST'])]
    public function addWishlist(
        Request $request,
        Reference $reference,
        WishlistRepository $wishlistRepository): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        if ($this->isCsrfTokenValid('wishlist_add'.$reference->getId(), $request->request->get('_token'))) {
            $wish = new Wishlist();
            $wish->setUser($user);
            $wish->setReference($reference);
            $wishlistRepository->add($wish, true);
            $this->addFlash('success', 'Article ajouté aux favoris');
        }

        return $this->redirectToRoute('shop_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove/wishlist/{id}', name: 'shop_wishlist_remove', methods: ['POST'])]
    public function removeWishlist(
        Request $request,
        Wishlist $wish,
        WishlistRepository $wishlistRepository): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        if ($this->isCsrfTokenValid('wishlist_delete'.$wish->getId(), $request->request->get('_token'))) {
            $wishlistRepository->remove($wish, true);
            $this->addFlash('success', 'Article supprimé des favoris');
        }

        return $this->redirectToRoute('app_user_wishlist', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/reference/colors', name: 'json_shop_reference_colors')]
    public function referenceColorsBySize(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $articles = $this->articleRepository->findByRefAndSizeField($data["ref_id"], $data['size_id']);

        $colors = [];
        foreach ($articles as $article) {
            if (!in_array($article->getColor()->getId(), $colors)) {
                $colors[$article->getColor()->getId()] = $article->getColor()->getName();
            }
        }

        $response = new Response(json_encode(array('colors' => $colors)));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    #[Route('/api/reference/article-infos', name: 'json_shop_reference_article_infos')]
    public function getArticle(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $article = $this->articleRepository->findOneByParams($data["ref"], $data["size"], $data["color"]);
        $response = new Response(json_encode(array('article_qty' => $article->getQty())));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
