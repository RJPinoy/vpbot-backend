<?php

namespace App\Controller;

use App\Repository\PrivateChatbotRepository;
use App\Repository\PublicChatbotRepository;
use App\Service\MessagesService;
use App\Service\openai\assistant\OpenaiAssistantService;
use App\Service\openai\message\OpenaiMessageService;
use App\Service\openai\thread\OpenaiThreadService;
use App\Service\openai\run\OpenaiRunService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

    #[Route('/api/public/messages/list', name: 'public_list_messages', methods: ['POST'])]
    public function publicListMessages(
        Request $request,
        OpenaiMessageService $openAiessageService,
        PublicChatbotRepository $publicChatbotRepository,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $chatbot = $publicChatbotRepository->findOneBy([]);
        if (!$chatbot) {
            return new JsonResponse(['error' => 'No chatbot found'], Response::HTTP_NOT_FOUND);
        }

        $apiKey = $chatbot->getApiKey();
        if (!$apiKey) {
            return new JsonResponse(['error'=> 'No API Key set.'], Response::HTTP_NOT_FOUND);
        }

        // Use query param for GET
        $data = json_decode($request->getContent(), true);
        $threadId = $data['threadId'] ?? null;
        if (null === $threadId || '' === trim((string)$threadId)) {
            return new JsonResponse(['error' => 'Missing threadId'], Response::HTTP_NOT_FOUND);
        }

        try {
            $result = $openAiessageService->listMessages($apiKey, $threadId);
            return new JsonResponse($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/public/run', name:'public_poll_run_status', methods: ['POST'])]
    public function publicPollRunStatus(
        Request $request,
        OpenaiRunService $openaiRunService,
        PublicChatbotRepository $publicChatbotRepository,
        OpenaiMessageService $openAiessageService,
        MessagesService $messagesService,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $chatbot = $publicChatbotRepository->findOneBy([]);
        if (!$chatbot) {
            return new JsonResponse(['error' => 'No chatbot found'], Response::HTTP_NOT_FOUND);
        }

        $apiKey = $chatbot->getApiKey();
        if (!$apiKey) {
            return new JsonResponse(['error'=> 'No API Key set.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $threadId = $data['threadId'] ?? '';
        $runId = $data['runId'] ?? '';

        if (!$threadId) {
            return new JsonResponse(['error'=> 'Thread ID is missing.'], Response::HTTP_NOT_FOUND);
        }

        if (!$runId) {
            return new JsonResponse(['error'=> 'Run ID is missing.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $run = $openaiRunService->getRun($apiKey, $runId, $threadId);

            if ($run['status'] === 'completed') {
                $messages = $openAiessageService->listMessages($apiKey, $threadId);

                $assistantReply = null;
                foreach ($messages['data'] as $msg) {
                    if ($msg['role'] === 'assistant' && !empty($msg['content'][0]['text']['value'])) {
                        $assistantReply = $msg['content'][0]['text']['value'];
                        break;
                    }
                }

                if ($assistantReply) {
                    $messagesService->saveMessage($user, $chatbot, 'assistant', $assistantReply);
                }

                return new JsonResponse([
                    'success' => true,
                    'status'=> 'completed',
                    'messages'=> $messages,
                ], Response::HTTP_OK);
            } else {
                return new JsonResponse([
                    'success' => true,
                    'status'=> $run['status'],
                ], Response::HTTP_PARTIAL_CONTENT);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/public/messages/send', name:'public_send_messages', methods: ['POST'])]
    public function publicSendMessages(
        Request $request,
        OpenaiThreadService $openaiThreadService,
        OpenaiMessageService $openAiessageService,
        MessagesService $messagesService,
        OpenaiRunService $openaiRunService,
        PublicChatbotRepository $publicChatbotRepository,
        EntityManagerInterface $entityManagerInterface,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $chatbot = $publicChatbotRepository->findOneBy([]);
        if (!$chatbot) {
            return new JsonResponse(['error' => 'No chatbot found'], Response::HTTP_NOT_FOUND);
        }

        $apiKey = $chatbot->getApiKey();
        if (!$apiKey) {
            return new JsonResponse(['error'=> 'No API Key set.'], Response::HTTP_NOT_FOUND);
        }

        // Use query param for GET
        $data = json_decode($request->getContent(), true);
        $assistantId = $chatbot->getAssistantId();
        $threadId = $data['threadId'] ?? '';
        $model = $chatbot->getModel();
        $message = $data['message'] ?? '';

        try {
            if (empty($assistantId)) {
                return new JsonResponse(['error'=> 'Assistant ID not found'], Response::HTTP_NOT_FOUND);
            }

            if (empty($message)) {
                return new JsonResponse(['error'=> 'No message provided.'], Response::HTTP_NOT_FOUND);
            }

            if (empty($threadId)) {
                $thread = $openaiThreadService->createThread($apiKey)['id'];
            } else {
                $thread = $threadId;
            }

            $openAiessageService->createMessage($apiKey, $thread, $message);
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