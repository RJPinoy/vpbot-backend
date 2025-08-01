<?php

namespace App\Controller;

use App\Repository\PublicChatbotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class PublicChatbotController extends AbstractController
{
    #[Route('/api/public_chatbot', name: 'get_public_chatbot', methods: ['GET'])]
    public function public_chatbot(
        PublicChatbotRepository $publicChatbotRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $chatbot = $publicChatbotRepository->findOneBy([]);

        if (!$chatbot) {
            return new JsonResponse(['error' => 'No public chatbot found'], 404);
        }

        $json = $serializer->serialize($chatbot, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    // #[Route('/api/public_chatbot', name: 'modify_public_chatbot', methods: ['PUT'])]
    // public function modify(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/SharedChatbotController.php',
    //     ]);
    // }
}