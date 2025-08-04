<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIService
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client, string $openAiApiKey)
    {
        $this->client = $client;
        $this->apiKey = $openAiApiKey;
    }
}