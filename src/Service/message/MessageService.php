<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MessageService extends OpenaiService
{
    public function __construct(EntityManagerInterface $em, HttpClientInterface $client)
    {
        parent::__construct($em, $client);
    }

    public function createMessage(string $apiKey, string $threadId, string $role, string $content): array
    {
        $response = $this->client->request('POST', $this->base_url . '/threads/' . $threadId . '/messages', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2',
            ],
            'json' => [
                'role' => $role,
                'content' => $content,
            ],
        ]);

        $data = $response->toArray(false);

        // Return raw response data as array
        return $data;
    }

    public function listMessages(string $apiKey, string $threadId): array
    {
        $response = $this->client->request('GET', $this->base_url . '/threads/' . $threadId . '/messages', [
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