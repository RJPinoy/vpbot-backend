<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AssistantService extends OpenaiService
{
    public function __construct(EntityManagerInterface $em, HttpClientInterface $client)
    {
        parent::__construct($em, $client);
    }

    public function createAssistant(string $apiKey, ?string $name = null, ?string $instructions = null): array
    {
        $response = $this->client->request('POST', $this->base_url . '/assistants', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2',
            ],
            'json' => [
                'name' => $name,
                'instructions' => $instructions,
            ],
        ]);

        $data = $response->toArray(false);

        // Return raw response data as array
        return $data;
    }

    public function listAssistants(string $apiKey): string
    {
        $response = $this->client->request('GET', $this->base_url . '/assistants', [
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

    public function retrieveAssistant(string $apiKey, string $assistantId): string
    {
        $response = $this->client->request('GET', $this->base_url . '/assistants/' . $assistantId, [
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

    public function modifyAssistant(string $apiKey, string $assistantId, ?string $name = null, ?string $instructions = null, ?string $model = null): string
    {
        $response = $this->client->request('POST', $this->base_url . '/assistants/' . $assistantId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2',
            ],
            'json' => [
                'name' => $name,
                'instructions' => $instructions,
                'model' => $model,
            ],
        ]);

        $data = $response->toArray(false);

        // Return raw response data as array
        return $data;
    }

    public function deleteAssistant(string $apiKey, string $assistantId): string
    {
        $response = $this->client->request('DELETE', $this->base_url . '/assistants/' . $assistantId, [
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