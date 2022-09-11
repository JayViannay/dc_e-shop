<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\OneToMany(mappedBy: 'price', targetEntity: Reference::class)]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Collection<int, Reference>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Reference $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setPrice($this);
        }

        return $this;
    }

    public function removeArticle(Reference $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getPrice() === $this) {
                $article->setPrice(null);
            }
        }

        return $this;
    }
}
