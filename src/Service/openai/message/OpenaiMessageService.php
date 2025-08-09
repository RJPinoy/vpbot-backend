<?php

namespace App\Service\openai\message;

use App\Service\openai\OpenaiService;

class OpenaiMessageService extends OpenaiService
{
    public function createMessage(string $apiKey, string $threadId, string $content): array
    {
        $response = $this->client->request('POST', $this->baseUrl . '/threads/' . $threadId . '/messages', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2',
            ],
            'json' => [
                'role' => 'user',
                'content' => $content,
            ],
        ]);

        $data = $response->toArray(false);

        // Return raw response data as array
        return $data;
    }

    public function listMessages(string $apiKey, string $threadId): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/threads/' . $threadId . '/messages', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2',
            ],
        ]);

        $data = $response->toArray(false);

        // Return raw response data as array
        return $data;
    }
}