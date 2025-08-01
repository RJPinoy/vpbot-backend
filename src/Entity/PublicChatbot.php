<?php

namespace App\Entity;

use App\Repository\PublicChatbotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicChatbotRepository::class)]
class PublicChatbot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $apiKey = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $assistantId = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $model = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $iconUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fontColor1 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fontColor2 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mainColor = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $secondaryColor = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $renderEveryPages = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $position = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $welcomeMessage = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $promptMessage = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $showDesktop = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $showTablet = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $showMobile = null;

    /**
     * @var Collection<int, Messages>
     */
    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'PublicChatbot')]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getAssistantId(): ?string
    {
        return $this->assistantId;
    }

    public function setAssistantId(string $assistantId): static
    {
        $this->assistantId = $assistantId;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    public function setIconUrl(?string $iconUrl): static
    {
        $this->iconUrl = $iconUrl;

        return $this;
    }

    public function getFontColor1(): ?string
    {
        return $this->fontColor1;
    }

    public function setFontColor1(?string $fontColor1): static
    {
        $this->fontColor1 = $fontColor1;

        return $this;
    }

    public function getFontColor2(): ?string
    {
        return $this->fontColor2;
    }

    public function setFontColor2(?string $fontColor2): static
    {
        $this->fontColor2 = $fontColor2;

        return $this;
    }

    public function getMainColor(): ?string
    {
        return $this->mainColor;
    }

    public function setMainColor(?string $mainColor): static
    {
        $this->mainColor = $mainColor;

        return $this;
    }

    public function getSecondaryColor(): ?string
    {
        return $this->secondaryColor;
    }

    public function setSecondaryColor(?string $secondaryColor): static
    {
        $this->secondaryColor = $secondaryColor;

        return $this;
    }

    public function isRenderEveryPages(): ?bool
    {
        return $this->renderEveryPages;
    }

    public function setRenderEveryPages(bool $renderEveryPages): static
    {
        $this->renderEveryPages = $renderEveryPages;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getWelcomeMessage(): ?string
    {
        return $this->welcomeMessage;
    }

    public function setWelcomeMessage(?string $welcomeMessage): static
    {
        $this->welcomeMessage = $welcomeMessage;

        return $this;
    }

    public function getPromptMessage(): ?string
    {
        return $this->promptMessage;
    }

    public function setPromptMessage(?string $promptMessage): static
    {
        $this->promptMessage = $promptMessage;

        return $this;
    }

    public function isShowDesktop(): ?bool
    {
        return $this->showDesktop;
    }

    public function setShowDesktop(?bool $showDesktop): static
    {
        $this->showDesktop = $showDesktop;

        return $this;
    }

    public function isShowTablet(): ?bool
    {
        return $this->showTablet;
    }

    public function setShowTablet(?bool $showTablet): static
    {
        $this->showTablet = $showTablet;

        return $this;
    }

    public function isShowMobile(): ?bool
    {
        return $this->showMobile;
    }

    public function setShowMobile(?bool $showMobile): static
    {
        $this->showMobile = $showMobile;

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
            $message->setPublicChatbot($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getPublicChatbot() === $this) {
                $message->setPublicChatbot(null);
            }
        }

        return $this;
    }
}
