<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenaiService
{
    protected $base_url = '';
    protected EntityManagerInterface $em;
    protected HttpClientInterface $client;

    public function __construct(EntityManagerInterface $em, HttpClientInterface $client)
    {
        $this->base_url = 'https://api.openai.com/v1';
        $this->em = $em;
        $this->client = $client;
    }
}