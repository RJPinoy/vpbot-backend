<?php

namespace App\Service\openai;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenaiService
{
    protected string $baseUrl;
    protected EntityManagerInterface $em;
    protected HttpClientInterface $client;

    public function __construct(EntityManagerInterface $em, HttpClientInterface $client, string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->em = $em;
        $this->client = $client;
    }
}