<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PrivateChatbotUpdateDto
{
    #[Assert\Type('string')]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $apiKey = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 0, max: 65535)]
    public ?string $instructions = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $model = null;
}