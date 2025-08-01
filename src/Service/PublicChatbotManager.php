<?php

namespace App\Service;

use App\Entity\PublicChatbot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class PublicChatbotManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory
    ) {}

    public function saveWithHashedApiKey(PublicChatbot $bot, string $rawApiKey): void
    {
        $hasher = $this->passwordHasherFactory->getPasswordHasher('public_chatbot');
        $hashed = $hasher->hash($rawApiKey);

        $bot->setHashedApiKey($hashed);

        $this->em->persist($bot);
        $this->em->flush();
    }
}