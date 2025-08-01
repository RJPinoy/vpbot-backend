<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PublicChatbotUpdateDto
{
    #[Assert\Type('int')]
    public ?int $id = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $apiKey = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $assistantId = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $model = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 255)]
    public ?string $name = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 255)]
    public ?string $iconUrl = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 255)]
    public ?string $fontColor1 = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 255)]
    public ?string $fontColor2 = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 255)]
    public ?string $mainColor = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 255)]
    public ?string $secondaryColor = null;

    #[Assert\Type('bool')]
    public ?bool $renderEveryPages = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $position = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 255)]
    public ?string $welcomeMessage = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 255)]
    public ?string $promptMessage = null;

    #[Assert\Type('bool')]
    public ?bool $showDesktop = null;

    #[Assert\Type('bool')]
    public ?bool $showTablet = null;

    #[Assert\Type('bool')]
    public ?bool $showMobile = null;

    public function __construct(object $publicChatbot)
    {
        $this->id = $publicChatbot->getId();
        $this->apiKey = $publicChatbot->getApiKey();
        $this->assistantId = $publicChatbot->getAssistantId();
        $this->model = $publicChatbot->getModel();
        $this->name = $publicChatbot->getName();
        $this->iconUrl = $publicChatbot->getIconUrl();
        $this->fontColor1 = $publicChatbot->getFontColor1();
        $this->fontColor2 = $publicChatbot->getFontColor2();
        $this->mainColor = $publicChatbot->getMainColor();
        $this->secondaryColor = $publicChatbot->getSecondaryColor();
        $this->renderEveryPages = $publicChatbot->isRenderEveryPages();
        $this->position = $publicChatbot->getPosition();
        $this->welcomeMessage = $publicChatbot->getWelcomeMessage();
        $this->promptMessage = $publicChatbot->getPromptMessage();
        $this->showDesktop = $publicChatbot->isShowDesktop();
        $this->showTablet = $publicChatbot->isShowTablet();
        $this->showMobile = $publicChatbot->isShowMobile();
    }
}