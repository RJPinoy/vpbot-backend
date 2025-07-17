<?php

namespace App\Service;

use App\Entity\SharedChatbot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class SharedChatbotManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory
    ) {}

    public function saveWithHashedApiKey(SharedChatbot $bot, string $rawApiKey): void
    {
        $hasher = $this->passwordHasherFactory->getPasswordHasher('shared_chatbot');
        $hashed = $hasher->hash($rawApiKey);

        $bot->setHashedApiKey($hashed);

        $this->em->persist($bot);
        $this->em->flush();
    }
}