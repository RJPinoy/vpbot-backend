<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ThreadService extends OpenaiService
{
    public function __construct(EntityManagerInterface $em, HttpClientInterface $client)
    {
        parent::__construct($em, $client);
    }

    public function createThread(string $apiKey): array
    {
        $response = $this->client->request('POST', $this->base_url . '/threads', [
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