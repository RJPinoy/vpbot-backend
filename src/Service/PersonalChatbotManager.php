<?php

namespace App\Service;

use App\Entity\PersonalChatbot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class PersonalChatbotManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory
    ) {}

    public function saveWithHashedApiKey(PersonalChatbot $bot, string $rawApiKey): void
    {
        $hasher = $this->passwordHasherFactory->getPasswordHasher('personal_chatbot');
        $hashed = $hasher->hash($rawApiKey);

        $bot->setHashedApiKey($hashed);

        $this->em->persist($bot);
        $this->em->flush();
    }
}