<?php

namespace App\Entity;

use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagsRepository::class)]
class Tags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tag_name = null;

    /**
     * @var Collection<int, PublicationsSharing>
     */
    #[ORM\ManyToMany(targetEntity: PublicationsSharing::class, mappedBy: 'tags')]
    private Collection $publicationsSharings;

    public function __construct()
    {
        $this->publicationsSharings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTagName(): ?string
    {
        return $this->tag_name;
    }

    public function setTagName(string $tag_name): static
    {
        $this->tag_name = $tag_name;

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
            $publicationsSharing->addTag($this);
        }

        return $this;
    }

    public function removePublicationsSharing(PublicationsSharing $publicationsSharing): static
    {
        if ($this->publicationsSharings->removeElement($publicationsSharing)) {
            $publicationsSharing->removeTag($this);
        }

        return $this;
    }
}
