<?php

namespace App\Entity;

use App\Repository\PersonalChatbotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalChatbotRepository::class)]
class PersonalChatbot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $hashedApiKey = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $assistant = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $instructions = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $model = null;

    #[ORM\OneToOne(mappedBy: 'personalBot', cascade: ['persist', 'remove'])]
    private ?User $userChatbot = null;

    /**
     * @var Collection<int, Messages>
     */
    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'PersonalChatbot')]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHashedApiKey(): ?string
    {
        return $this->hashedApiKey;
    }

    public function setHashedApiKey(string $hashedApiKey): static
    {
        $this->hashedApiKey = $hashedApiKey;

        return $this;
    }

    public function getAssistant(): ?string
    {
        return $this->assistant;
    }

    public function setAssistant(string $assistant): static
    {
        $this->assistant = $assistant;

        return $this;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): static
    {
        $this->instructions = $instructions;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getUserChatbot(): ?User
    {
        return $this->userChatbot;
    }

    public function setUserChatbot(?User $userChatbot): static
    {
        // unset the owning side of the relation if necessary
        if ($userChatbot === null && $this->userChatbot !== null) {
            $this->userChatbot->setPersonalBot(null);
        }

        // set the owning side of the relation if necessary
        if ($userChatbot !== null && $userChatbot->getPersonalBot() !== $this) {
            $userChatbot->setPersonalBot($this);
        }

        $this->userChatbot = $userChatbot;

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setPersonalChatbot($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getPersonalChatbot() === $this) {
                $message->setPersonalChatbot(null);
            }
        }

        return $this;
    }
}
