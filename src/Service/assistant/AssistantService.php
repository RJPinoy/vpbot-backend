<?php

namespace App\Service\assistant;

use App\Service\OpenaiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AssistantService extends OpenaiService
{
    public function createAssistant(string $apiKey, ?string $name = null, ?string $instructions = null): array
    {
        $response = $this->client->request('POST', $this->baseUrl . '/assistants', [
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

    public function listAssistants(string $apiKey): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/assistants', [
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

    public function retrieveAssistant(string $apiKey, string $assistantId): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/assistants/' . $assistantId, [
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

    public function modifyAssistant(string $apiKey, string $assistantId, ?string $name = null, ?string $instructions = null, ?string $model = null): array
    {
        $response = $this->client->request('POST', $this->baseUrl . '/assistants/' . $assistantId, [
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

    public function deleteAssistant(string $apiKey, string $assistantId): array
    {
        $response = $this->client->request('DELETE', $this->baseUrl . '/assistants/' . $assistantId, [
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