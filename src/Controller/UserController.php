<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\DTO\UserUpdateDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    private function isAdmin($user): bool {
        return in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPER_ADMIN', $user->getRoles());
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'img' => $user->getPicture(),
            'username' => $user->getUsername(),
        ]);
    }

    #[Route('/api/users/{user_id}', name: 'modify_user', methods: ['PUT'])]
    public function modifyUser(
        int $user_id,
        Request $request,
        UserRepository $userRepository,
        ValidatorInterface $validatorInterface,
        SerializerInterface $serializerInterface,
        EntityManagerInterface $em
    ): JsonResponse {
        $currentUser = $this->getUser();

        if (!$currentUser) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->isAdmin($currentUser) && $currentUser->getId() !== $user_id) {
            return new JsonResponse(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $user = $userRepository->find($user_id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $dto = $serializerInterface->deserialize($request->getContent(), UserUpdateDto::class, 'json');
        $errors = $validatorInterface->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $violation) {
                $errorMessages[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        if ($dto->firstName !== null) {
            $user->setFirstName($dto->firstName);
        }
        if ($dto->lastName !== null) {
            $user->setLastName($dto->lastName);
        }
        if ($dto->email !== null) {
            $user->setEmail($dto->email);
        }
        if ($dto->username !== null) {
            $user->setUsername($dto->username);
        }
        if ($dto->img !== null) {
            $user->setPicture($dto->img);
        }

        $em->flush();

        return new JsonResponse([
            'message' => 'User updated successfully',
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'img' => $user->getPicture(),
            'username' => $user->getUsername(),
        ]);
    }
}