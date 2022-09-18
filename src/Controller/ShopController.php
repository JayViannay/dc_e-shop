<?php

namespace App\Controller;

use App\Entity\Reference;
use App\Repository\ArticleRepository;
use App\Repository\ReferenceRepository;
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
