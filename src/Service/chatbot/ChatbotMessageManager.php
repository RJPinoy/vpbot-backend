<?php

namespace App\Service\chatbot;

use App\Service\MessagesService;
use App\Service\openai\message\OpenaiMessageService;
use App\Entity\User;
use App\Entity\PublicChatbot;
use App\Entity\PrivateChatbot;
class ChatbotMessageManager
{
    public function __construct(
        private OpenaiMessageService $openaiMessageService,
        private MessagesService $messagesService
    ) {}

    /**
     * Send a user message to the chatbot and return the assistant's response.
     *
     * @param User $user
     * @param PublicChatbot|PrivateChatbot $chatbot
     * @param string $apiKey
     * @param string $threadId
     * @param string $userMessage
     *
     * @return string
     */
    public function handleUserMessage(
        User $user,
        object $chatbot,
        string $apiKey,
        string $threadId,
        string $userMessage
    ): string {
        // 1. Sauvegarder le message utilisateur (logique métier)
        $this->messagesService->saveMessage($user, $chatbot, 'user', $userMessage);

        // 2. Envoyer le message à OpenAI via service infra
        $responseData = $this->openaiMessageService->createMessage($apiKey, $threadId, $userMessage);

        // 3. Récupérer la réponse assistant dans la réponse API (supposons qu’elle est à un endroit précis)
        $assistantMessage = $responseData['choices'][0]['message']['content'] ?? '';

        // 4. Sauvegarder la réponse assistant
        $this->messagesService->saveMessage($user, $chatbot, 'assistant', $assistantMessage);

        // 5. Retourner la réponse à l’appelant
        return $assistantMessage;
    }
}