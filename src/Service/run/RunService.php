<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RunService extends OpenaiService
{
    public function __construct(EntityManagerInterface $em, HttpClientInterface $client)
    {
        parent::__construct($em, $client);
    }

    public function createRun(string $apiKey, string $threadId, string $assistantId): array
    {
        $response = $this->client->request('POST', $this->base_url . '/threads/' . $threadId . '/runs', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2',
            ],
            'json' => [
                'assistant_id' => $assistantId,
            ],
        ]);

        $data = $response->toArray(false);

        // Return raw response data as array
        return $data;
    }
}