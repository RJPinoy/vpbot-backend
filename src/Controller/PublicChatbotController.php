<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class PublicChatbotController extends AbstractController
{
    #[Route('/api/public_chatbot', name: 'modify_public_chatbot', methods: ['PUT'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SharedChatbotController.php',
        ]);
    }
}