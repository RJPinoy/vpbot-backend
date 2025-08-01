<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class PrivateChatbotController extends AbstractController
{
    #[Route('/api/private_chatbot', name: 'get_private_chatbot', methods: ['GET'])]
    public function private_chatbot(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PrivateChatbotController.php',
        ]);
    }
}