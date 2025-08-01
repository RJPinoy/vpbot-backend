<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateDto
{
    #[Assert\Type('string')]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $firstName = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $lastName = null;

    #[Assert\Type('string')]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        message: 'The email must be a valid format.'
    )]
    public ?string $email = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $username = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $img = null;

    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Choice(choices: ['ROLE_USER', 'ROLE_ADMIN'], message: 'Each role must be either ROLE_USER or ROLE_ADMIN.'),
    ])]
    public ?array $roles = null;
}