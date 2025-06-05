<?php

namespace App\Entity;

use App\Repository\PublicationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicationsRepository::class)]
#[ORM\HasLifecycleCallbacks] // Add this annotation
class Publications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $ID_TYPE = null;

    #[ORM\Column]
    private array $images = [];

    #[ORM\Column]
    private array $content = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $tags = null;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIDTYPE(): ?Type
    {
        return $this->ID_TYPE;
    }

    public function setIDTYPE(?Type $ID_TYPE): static
    {
        $this->ID_TYPE = $ID_TYPE;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): static
    {
        $this->images = $images;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    // Prevents modification after creation
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        if ($this->created_at === null) {
            $this->created_at = $created_at;
        }
        return $this;
    }

    #[ORM\PrePersist] // Automatically sets created_at before persisting
    public function setCreatedAtValue(): void
    {
        if ($this->created_at === null) {
            $this->created_at = new \DateTimeImmutable();
        }
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): static
    {
        $this->tags = $tags;

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
}
