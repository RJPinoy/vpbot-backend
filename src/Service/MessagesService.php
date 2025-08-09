<?php

namespace App\Service;

use App\Entity\Messages;
use App\Entity\User;
use App\Entity\PublicChatbot;
use App\Entity\PrivateChatbot;
use Doctrine\ORM\EntityManagerInterface;

class MessagesService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Save a message in the database
     *
     * @param User $user
     * @param PublicChatbot|PrivateChatbot $chatbot
     * @param string $role  "user" or "assistant"
     * @param string $message
     */
    public function saveMessage(User $user, object $chatbot, string $role, string $message): void
    {
        $entity = new Messages();
        $entity->setUserMessages($user);

        // Determine chatbot type
        if ($chatbot instanceof PublicChatbot) {
            $entity->setPublicChatbot($chatbot);
        } elseif ($chatbot instanceof PrivateChatbot) {
            $entity->setPrivateChatbot($chatbot);
        } else {
            throw new \InvalidArgumentException('Invalid chatbot type');
        }

        // Assign message or response based on role
        if ($role === 'user') {
            $entity->setMessage($message);
        } elseif ($role === 'assistant') {
            $entity->setResponse($message);
        } else {
            throw new \InvalidArgumentException('Invalid role, must be "user" or "assistant"');
        }

        $entity->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}