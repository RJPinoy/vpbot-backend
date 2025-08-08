<?php

namespace App\Controller;

use App\Dto\PrivateChatbotUpdateDto;
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
            return new JsonResponse(['error' => 'No personal chatbot found'], Response::HTTP_NOT_FOUND);
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

    #[Route('/api/private_chatbot/{user_id}', name: 'update_private_chatbot', methods: ['PUT'])]
    public function updatePrivateChatbot(
        int $user_id,
        Request $request,
        PrivateChatbotRepository $privateChatbotRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ): JsonResponse {
        $chatbot = $privateChatbotRepository->findWithAssistantsByUserId($user_id);

        if (!$chatbot) {
            return new JsonResponse(['error' => 'Private chatbot not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $dto = new PrivateChatbotUpdateDto();

        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Apply changes to entity
        if ($dto->apiKey !== null) $chatbot->setApiKey($dto->apiKey);
        if ($dto->instructions !== null) $chatbot->setInstructions($dto->instructions);
        if ($dto->model !== null) $chatbot->setModel($dto->model);

        $em->flush();

        $responseData = [
            'message' => 'Private chatbot updated successfully.',
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

        return new JsonResponse($responseData, 200);
    }
}