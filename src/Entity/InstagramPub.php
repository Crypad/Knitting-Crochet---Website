<?php

namespace App\Entity;

use App\Repository\InstagramPubRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstagramPubRepository::class)]
class InstagramPub
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $instagramPublications = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstagramPublications(): ?string
    {
        return $this->instagramPublications;
    }

    public function setInstagramPublications(string $instagramPublications): static
    {
        $this->instagramPublications = $instagramPublications;

        return $this;
    }
}
