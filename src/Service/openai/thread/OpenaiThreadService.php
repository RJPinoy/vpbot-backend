<?php

namespace App\Service\openai\thread;

use App\Service\openai\OpenaiService;

class OpenaiThreadService extends OpenaiService
{
    public function createThread(string $apiKey): array
    {
        $response = $this->client->request('POST', $this->baseUrl . '/threads', [
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