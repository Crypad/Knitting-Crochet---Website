<?php

namespace App\Entity;

use App\Repository\ModelsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModelsRepository::class)]
class Models
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'models')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, PublicationsSharing>
     */
    #[ORM\ManyToMany(targetEntity: PublicationsSharing::class, mappedBy: 'models')]
    private Collection $publicationsSharings;

    public function __construct()
    {
        $this->publicationsSharings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, PublicationsSharing>
     */
    public function getPublicationsSharings(): Collection
    {
        return $this->publicationsSharings;
    }

    public function addPublicationsSharing(PublicationsSharing $publicationsSharing): static
    {
        if (!$this->publicationsSharings->contains($publicationsSharing)) {
            $this->publicationsSharings->add($publicationsSharing);
            $publicationsSharing->addModel($this);
        }

        return $this;
    }

    public function removePublicationsSharing(PublicationsSharing $publicationsSharing): static
    {
        if ($this->publicationsSharings->removeElement($publicationsSharing)) {
            $publicationsSharing->removeModel($this);
        }

        return $this;
    }
}
