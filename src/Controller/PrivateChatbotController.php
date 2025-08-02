<?php

namespace App\Controller;

use App\Repository\PrivateChatbotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class PrivateChatbotController extends AbstractController
{
    #[Route('/api/private_chatbot/{user_id}', name: 'get_private_chatbot', methods: ['GET'])]
    public function private_chatbot(
        int $user_id,
        PrivateChatbotRepository $privateChatbotRepository
    ): JsonResponse {
        $chatbot = $privateChatbotRepository->findWithAssistantsByUserId($user_id);

        if (!$chatbot) {
            return new JsonResponse(['error' => 'No personal chatbot found'], 404);
        }

        $data = [
            'id' => $chatbot->getId(),
            'apiKey' => $chatbot->getApiKey(),
            'instructions' => $chatbot->getInstructions(),
            'model' => $chatbot->getModel(),
            'assistants' => array_map(function ($assistant) {
                return [
                    'id' => $assistant->getId(),
                    'name' => $assistant->getName(),
                    'description' => $assistant->getDescription(),
                    // Add more fields if needed
                ];
            }, $chatbot->getAssistant()->toArray()),
        ];

        return new JsonResponse($data, 200);
    }
}