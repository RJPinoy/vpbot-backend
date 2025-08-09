<?php

namespace App\Controller;

use App\Repository\PrivateChatbotRepository;
use App\Service\chatbot\ChatbotManager;
use App\Service\chatbot\ChatbotRunManager;
use App\Service\MessagesService;
use App\Service\openai\assistant\OpenaiAssistantService;
use App\Service\openai\message\OpenaiMessageService;
use App\Service\openai\thread\OpenaiThreadService;
use App\Service\openai\run\OpenaiRunService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class OpenaiController extends AbstractController
{
    #[Route('/api/assistants', name: 'get_assistants', methods: ['GET'])]
    public function listAssistants(
        OpenaiAssistantService $openaiAssistantService,
        PrivateChatbotRepository $privateChatbotRepository,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getId();

        $chatbot = $privateChatbotRepository->findWithAssistantsByUserId($userId);
        if (!$chatbot) {
            return new JsonResponse(['error' => 'No personal chatbot found'], Response::HTTP_NOT_FOUND);
        }

        $apiKey = $chatbot->getApiKey();
        if (!$apiKey) {
            return new JsonResponse(['error'=> 'No API Key set.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $result = $openaiAssistantService->listAssistants($apiKey);
            return new JsonResponse($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/messages/list', name: 'list_messages', methods: ['POST'])]
    public function listMessages(
        Request $request,
        OpenaiMessageService $openaiMessageService,
        ChatbotManager $chatbotManager,
        CsrfTokenManagerInterface $csrfTokenManagerInterface
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        if (!$csrfTokenManagerInterface->isTokenValid(new CsrfToken('list_messages', $csrfToken))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        // Use query param for GET
        $data = json_decode($request->getContent(), true);
        $threadId = $data['threadId'] ?? null;
        if (null === $threadId || '' === trim((string)$threadId)) {
            return new JsonResponse(['error' => 'Missing threadId'], Response::HTTP_NOT_FOUND);
        }

        $type = $data['type'] ?? '';
        $chatbot = $chatbotManager->getChatbot($type);

        if ($chatbot === null) {
            return new JsonResponse(['error' => 'User not authenticated or chatbot not found'], Response::HTTP_UNAUTHORIZED);
        }

        $apiKey = $chatbot->getApiKey();

        if (!$apiKey) {
            return new JsonResponse(['error'=> 'No API Key set.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $result = $openaiMessageService->listMessages($apiKey, $threadId);
            return new JsonResponse($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/run', name:'poll_run_status', methods: ['POST'])]
    public function pollRunStatus(
        Request $request,
        ChatbotRunManager $chatbotRunManager,
        ChatbotManager $chatbotManager,
        CsrfTokenManagerInterface $csrfTokenManagerInterface,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        if (!$csrfTokenManagerInterface->isTokenValid(new CsrfToken('poll_run_status', $csrfToken))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $threadId = $data['threadId'] ?? '';
        $runId = $data['runId'] ?? '';
        $type = $data['type'] ?? '';
        $chatbot = $chatbotManager->getChatbot($type);

        $apiKey = $chatbot->getApiKey();
        if (!$apiKey) {
            return new JsonResponse(['error'=> 'No API Key set.'], Response::HTTP_NOT_FOUND);
        }

        if (!$threadId) {
            return new JsonResponse(['error'=> 'Thread ID is missing.'], Response::HTTP_NOT_FOUND);
        }

        if (!$runId) {
            return new JsonResponse(['error'=> 'Run ID is missing.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $result = $chatbotRunManager->pollRun($user, $chatbot, $apiKey, $runId, $threadId);

            if ($result['status'] === 'completed') {
                return new JsonResponse([
                    'success' => true,
                    'status'=> 'completed',
                    'messages'=> $result['messages'],
                ], Response::HTTP_OK);
            } else {
                return new JsonResponse([
                    'success' => true,
                    'status'=> $result['status'],
                ], Response::HTTP_PARTIAL_CONTENT);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/messages/send', name:'send_messages', methods: ['POST'])]
    public function sendMessages(
        Request $request,
        OpenaiThreadService $openaiThreadService,
        OpenaiMessageService $openaiMessageService,
        MessagesService $messagesService,
        OpenaiRunService $openaiRunService,
        ChatbotManager $chatbotManager,
        CsrfTokenManagerInterface $csrfTokenManagerInterface,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        if (!$csrfTokenManagerInterface->isTokenValid(new CsrfToken('send_messages', $csrfToken))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $assistantId = $data['assistantId'] ?? '';
        $threadId = $data['threadId'] ?? '';
        $message = $data['message'] ?? '';
        $type = $data['type'] ?? '';
        $chatbot = $chatbotManager->getChatbot($type);

        if (empty($assistantId)) {
            return new JsonResponse(['error'=> 'Assistant ID not found'], Response::HTTP_NOT_FOUND);
        }

        if (empty($message)) {
            return new JsonResponse(['error'=> 'No message provided.'], Response::HTTP_NOT_FOUND);
        }

        if ($chatbot === null) {
            return new JsonResponse(['error' => 'User not authenticated or chatbot not found'], Response::HTTP_UNAUTHORIZED);
        }

        $model = $chatbot->getModel();
        $apiKey = $chatbot->getApiKey();

        if (!$apiKey) {
            return new JsonResponse(['error'=> 'No API Key set.'], Response::HTTP_NOT_FOUND);
        }

        try {
            if (empty($threadId)) {
                $thread = $openaiThreadService->createThread($apiKey)['id'];
            } else {
                $thread = $threadId;
            }

            $openaiMessageService->createMessage($apiKey, $thread, $message);
            $run = $openaiRunService->createRun($apiKey, $thread, $assistantId, $model);

            $messagesService->saveMessage($user, $chatbot, 'user', $message);

            return new JsonResponse([
                'success' => true,
                'thread_id'=> $thread,
                'run_id'=> $run['id'],
                'run_status'=> $run['status'],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}