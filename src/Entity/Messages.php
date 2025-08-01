<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $response = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'Message')]
    private ?User $userMessages = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?PublicChatbot $PublicChatbot = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?PrivateChatbot $PrivateChatbot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): static
    {
        $this->response = $response;

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

    public function getUserMessages(): ?User
    {
        return $this->userMessages;
    }

    public function setUserMessages(?User $userMessages): static
    {
        $this->userMessages = $userMessages;

        return $this;
    }

    public function getPublicChatbot(): ?PublicChatbot
    {
        return $this->PublicChatbot;
    }

    public function setPublicChatbot(?PublicChatbot $PublicChatbot): static
    {
        $this->PublicChatbot = $PublicChatbot;

        return $this;
    }

    public function getPrivateChatbot(): ?PrivateChatbot
    {
        return $this->PrivateChatbot;
    }

    public function setPrivateChatbot(?PrivateChatbot $PrivateChatbot): static
    {
        $this->PrivateChatbot = $PrivateChatbot;

        return $this;
    }
}
