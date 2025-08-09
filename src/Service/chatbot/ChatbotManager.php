<?php

namespace App\Service\chatbot;

use App\Entity\PublicChatbot;
use App\Entity\PrivateChatbot;
use App\Repository\PublicChatbotRepository;
use App\Repository\PrivateChatbotRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ChatbotManager
{
    private Security $security;
    private PublicChatbotRepository $publicChatbotRepository;
    private PrivateChatbotRepository $privateChatbotRepository;

    public function __construct(
        Security $security,
        PublicChatbotRepository $publicChatbotRepository,
        PrivateChatbotRepository $privateChatbotRepository
    ) {
        $this->security = $security;
        $this->publicChatbotRepository = $publicChatbotRepository;
        $this->privateChatbotRepository = $privateChatbotRepository;
    }

    public function getUser()
    {
        return $this->security->getUser();
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getChatbot(string $type): PublicChatbot|PrivateChatbot|null
    {
        $user = $this->getUser();
        if (!$user) {
            return null; // Pas d'utilisateur connecté
        }

        if ($type === 'public') {
            $chatbot = $this->publicChatbotRepository->findOneBy([]);
            return $chatbot;
        } elseif ($type === 'private') {
            $chatbot = $this->privateChatbotRepository->findWithAssistantsByUserId($user->getId());
            return $chatbot;
        }

        throw new \InvalidArgumentException('Invalid chatbot type');
    }
}