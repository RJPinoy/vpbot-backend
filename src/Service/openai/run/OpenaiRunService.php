<?php

namespace App\Service\openai\run;

use App\Service\openai\OpenaiService;

class OpenaiRunService extends OpenaiService
{
    public function createRun(string $apiKey, string $threadId, string $assistantId, ?string $model = null): array
    {
        $response = $this->client->request('POST', $this->baseUrl . '/threads/' . $threadId . '/runs', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2',
            ],
            'json' => [
                'assistant_id' => $assistantId,
                'model' => $model,
            ],
        ]);

        $data = $response->toArray(false);

        // Return raw response data as array
        return $data;
    }

    public function getRun(string $apiKey, string $runId, string $threadId): array {
        $response = $this->client->request('GET', $this->baseUrl . '/threads/' . $threadId . '/runs/' . $runId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'OpenAI-Beta' => 'assistants=v2',
            ],
        ]);

        $data = $response->toArray(false);

        // Return raw response data as array
        return $data;
    }
}