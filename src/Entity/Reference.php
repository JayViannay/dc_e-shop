<?php

namespace App\Entity;

use App\Repository\ReferenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReferenceRepository::class)]
class Reference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Price $price = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'reference', targetEntity: Article::class)]
    private Collection $articles;

    private $sizes = [];

    private $colors = [];

    #[ORM\OneToMany(mappedBy: 'reference', targetEntity: Wishlist::class)]
    private $likes;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setReference($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getReference() === $this) {
                $article->setReference(null);
            }
        }

        return $this;
    }

    public function getSizes(): array
    {
        $articles = $this->articles;
        $sizes = [];
        foreach ($articles as $article) {
            if (!in_array($article->getSize()->getId(), $sizes) && $article->getQty() > 0) {
                $sizes[$article->getSize()->getId()] = $article->getSize()->getName();
            }
        }
        $this->sizes = $sizes;
        return $this->sizes;
    }

    public function getColors(): array
    {
        $articles = $this->articles;
        $colors = [];
        foreach ($articles as $article) {
            if (!in_array($article->getColor()->getId(), $colors) && $article->getQty() > 0) {
                $colors[$article->getColor()->getId()] = $article->getColor()->getName();
            }
        }
        $this->colors = $colors;
        return $this->colors;
    }

    public function addLike(Wishlist $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setReference($this);
        }
        return $this;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function removeLike(Wishlist $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            if ($like->getReference() === $this) {
                $like->setReference(null);
            }
        }

        return $this;
    }

    public function isLikedByUser(User $user) : bool
    {
        foreach($this->likes as $like){
            if($like->getUser() === $user) return true;
        }
        return false;
    }
    
}
