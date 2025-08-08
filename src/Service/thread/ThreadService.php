<?php

namespace App\Service\thread;

use App\Service\OpenaiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ThreadService extends OpenaiService
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