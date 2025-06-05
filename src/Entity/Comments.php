<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $answer_user_id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?bool $isAnAnswer = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?PublicationsSharing $publication = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswerUserId(): ?int
    {
        return $this->answer_user_id;
    }

    public function setAnswerUserId(?int $answer_user_id): static
    {
        $this->answer_user_id = $answer_user_id;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isAnAnswer(): ?bool
    {
        return $this->isAnAnswer;
    }

    public function setIsAnAnswer(bool $isAnAnswer): static
    {
        $this->isAnAnswer = $isAnAnswer;

        return $this;
    }

    public function getPublication(): ?PublicationsSharing
    {
        return $this->publication;
    }

    public function setPublication(?PublicationsSharing $publication): static
    {
        $this->publication = $publication;

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
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
