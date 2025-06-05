<?php

namespace App\Entity;

use App\Repository\PublicationsSharingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicationsSharingRepository::class)]
class PublicationsSharing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $images = null;

    #[ORM\Column(type: Types::JSON)]
    private array $content = [];

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'publicationsSharings')]
    private ?User $user = null;

    /**
     * @var Collection<int, Models>
     */
    #[ORM\ManyToMany(targetEntity: Models::class, inversedBy: 'publicationsSharings')]
    private Collection $models;

    /**
     * @var Collection<int, Tags>
     */
    #[ORM\ManyToMany(targetEntity: Tags::class, inversedBy: 'publicationsSharings')]
    private Collection $tags;

    /**
     * @var Collection<int, Likes>
     */
    #[ORM\OneToMany(targetEntity: Likes::class, mappedBy: 'publication')]
    private Collection $hasLiked;

    /**
     * @var Collection<int, Comments>
     */
    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'publication')]
    private Collection $comments;

    public function __construct()
    {
        $this->models = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->hasLiked = new ArrayCollection();
        $this->comments = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): static
    {
        $this->images = $images ?? [];

        return $this;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): static
    {
        $this->likes = $likes;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Models>
     */
    public function getModels(): Collection
    {
        return $this->models;
    }

    public function addModel(Models $model): static
    {
        if (!$this->models->contains($model)) {
            $this->models->add($model);
        }

        return $this;
    }

    public function removeModel(Models $model): static
    {
        $this->models->removeElement($model);

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tags $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection<int, Likes>
     */
    public function getHasLiked(): Collection
    {
        return $this->hasLiked;
    }

    public function addHasLiked(Likes $hasLiked): static
    {
        if (!$this->hasLiked->contains($hasLiked)) {
            $this->hasLiked->add($hasLiked);
            $hasLiked->setPublication($this);
        }

        return $this;
    }

    public function removeHasLiked(Likes $hasLiked): static
    {
        if ($this->hasLiked->removeElement($hasLiked)) {
            // set the owning side to null (unless already changed)
            if ($hasLiked->getPublication() === $this) {
                $hasLiked->setPublication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPublication($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPublication() === $this) {
                $comment->setPublication(null);
            }
        }

        return $this;
    }

}
