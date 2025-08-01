<?php

namespace App\Entity;

use App\Repository\AssistantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssistantRepository::class)]
class Assistant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'Assistant')]
    private ?PrivateChatbot $privateChatbot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrivateChatbot(): ?PrivateChatbot
    {
        return $this->privateChatbot;
    }

    public function setPrivateChatbot(?PrivateChatbot $privateChatbot): static
    {
        $this->privateChatbot = $privateChatbot;

        return $this;
    }
}
