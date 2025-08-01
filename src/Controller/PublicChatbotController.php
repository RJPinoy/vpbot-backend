<?php

namespace App\Controller;

use App\Dto\PublicChatbotUpdateDto;
use App\Service\SecurityService;
use App\Repository\PublicChatbotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PublicChatbotController extends AbstractController
{
    #[Route('/api/public_chatbot', name: 'get_public_chatbot', methods: ['GET'])]
    public function public_chatbot(
        PublicChatbotRepository $publicChatbotRepository,
        SerializerInterface $serializer,
    ): JsonResponse {
        $chatbot = $publicChatbotRepository->findOneBy([]);

        if (!$chatbot) {
            return new JsonResponse(['error' => 'No public chatbot found'], 404);
        }

        $json = $serializer->serialize($chatbot, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/public_chatbot', name: 'modify_public_chatbot', methods: ['PUT'])]
    public function modify(
        Request $request,
        PublicChatbotRepository $publicChatbotRepository,
        ValidatorInterface $validatorInterface,
        SerializerInterface $serializerInterface,
        EntityManagerInterface $em,
        SecurityService $securityService,
    ): JsonResponse {
        $currentUser = $this->getUser();
        
        if (!$securityService->isAdmin($currentUser)) {
            return new JsonResponse(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        
        $chatbot = $publicChatbotRepository->findOneBy([]);

        if (!$chatbot) {
            return new JsonResponse(['error' => 'No public chatbot found'], 404);
        }

        $dto = $serializerInterface->deserialize($request->getContent(), PublicChatbotUpdateDto::class, 'json');
        $errors = $validatorInterface->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $violation) {
                $errorMessages[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        
        if ($dto->apiKey !== null) {
            $chatbot->setApiKey($dto->apiKey);
        }
        if ($dto->assistantId !== null) {
            $chatbot->setAssistantId($dto->assistantId);
        }
        if ($dto->model !== null) {
            $chatbot->setModel($dto->model);
        }
        if ($dto->name !== null) {
            $chatbot->setName($dto->name);
        }
        if ($dto->iconUrl !== null) {
            $chatbot->setIconUrl($dto->iconUrl);
        }
        if ($dto->fontColor1 !== null) {
            $chatbot->setFontColor1($dto->fontColor1);
        }
        if ($dto->fontColor2 !== null) {
            $chatbot->setFontColor2($dto->fontColor2);
        }
        if ($dto->mainColor !== null) {
            $chatbot->setMainColor($dto->mainColor);
        }
        if ($dto->secondaryColor !== null) {
            $chatbot->setSecondaryColor($dto->secondaryColor);
        }
        if ($dto->renderEveryPages !== null) {
            $chatbot->setRenderEveryPages($dto->renderEveryPages);
        }
        if ($dto->position !== null) {
            $chatbot->setPosition($dto->position);
        }
        if ($dto->welcomeMessage !== null) {
            $chatbot->setWelcomeMessage($dto->welcomeMessage);
        }
        if ($dto->promptMessage !== null) {
            $chatbot->setPromptMessage($dto->promptMessage);
        }
        if ($dto->showDesktop !== null) {
            $chatbot->setShowDesktop($dto->showDesktop);
        }
        if ($dto->showTablet !== null) {
            $chatbot->setShowTablet($dto->showTablet);
        }
        if ($dto->showMobile !== null) {
            $chatbot->setShowMobile($dto->showMobile);
        }

        $em->flush();

        return new JsonResponse([
            'message' => 'Public Chatbot updated successfully',
            'apiKey' => $chatbot->getApiKey(),
            'assistantId' => $chatbot->getAssistantId(),
            'model' => $chatbot->getModel(),
            'name' => $chatbot->getName(),
            'iconUrl' => $chatbot->getIconUrl(),
            'fontColor1' => $chatbot->getFontColor1(),
            'fontColor2' => $chatbot->getFontColor2(),
            'mainColor' => $chatbot->getMainColor(),
            'secondaryColor' => $chatbot->getSecondaryColor(),
            'renderEveryPages' => $chatbot->isRenderEveryPages(),
            'position' => $chatbot->getPosition(),
            'welcomeMessage' => $chatbot->getWelcomeMessage(),
            'promptMessage' => $chatbot->getPromptMessage(),
            'showDesktop' => $chatbot->isShowDesktop(),
            'showTablet' => $chatbot->isShowTablet(),
            'showMobile' => $chatbot->isShowMobile(),
        ]);
    }
}