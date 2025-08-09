<?php

namespace App\Service\chatbot;

use App\Entity\User;
use App\Entity\PublicChatbot;
use App\Entity\PrivateChatbot;
use App\Service\MessagesService;
use App\Service\openai\run\OpenaiRunService;
use App\Service\openai\message\OpenaiMessageService;

class ChatbotRunManager
{
    private OpenaiRunService $runService;
    private OpenaiMessageService $messageService;
    private MessagesService $messagesService;

    public function __construct(
        OpenaiRunService $runService,
        OpenaiMessageService $messageService,
        MessagesService $messagesService,
    ) {
        $this->runService = $runService;
        $this->messageService = $messageService;
        $this->messagesService = $messagesService;
    }

    /**
     * Check run status, save assistant message if completed, return status and messages
     *
     * @param User $user
     * @param PublicChatbot|PrivateChatbot $chatbot
     * @param string $apiKey
     * @param string $runId
     * @param string $threadId
     * @return array
     */
    public function pollRun(User $user, object $chatbot, string $apiKey, string $runId, string $threadId): array
    {
        $run = $this->runService->getRun($apiKey, $runId, $threadId);

        if ($run['status'] === 'completed') {
            $messages = $this->messageService->listMessages($apiKey, $threadId);

            $assistantReply = null;
            foreach ($messages['data'] as $msg) {
                if ($msg['role'] === 'assistant' && !empty($msg['content'][0]['text']['value'])) {
                    $assistantReply = $msg['content'][0]['text']['value'];
                    break;
                }
            }

            if ($assistantReply) {
                $this->messagesService->saveMessage($user, $chatbot, 'assistant', $assistantReply);
            }

            return [
                'status' => 'completed',
                'messages' => $messages,
            ];
        }

        return [
            'status' => $run['status'],
        ];
    }
}