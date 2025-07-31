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

        if (!$this->isAdmin($currentUser) && $currentUser->getId() !== $user_id) {
            return new JsonResponse(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        if (!$currentUser) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
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
        if ($dto->roles !== null) {
            $user->setRoles($dto->roles);
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

    #[Route('/api/users', name: 'get_users', methods: ['GET'])]
    public function listUser(
        Request $request,
        UserRepository $userRepository,
    ): JsonResponse {
        $currentUser = $this->getUser();

        if (!$currentUser || !$this->isAdmin($currentUser)) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $limit = $request->query->getInt('limit', 20);
        $offset = $request->query->getInt('offset', 0);
        $order = strtolower($request->query->get('order', 'asc'));

        // Validate order parameter
        if (!in_array($order, ['asc', 'desc'])) {
            return new JsonResponse([
                'error' => 'Invalid order parameter. Use "asc" or "desc".'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Fetch users + 1 to determine if there are more
        $users = $userRepository->findBy([], ['created_at' => $order], $limit + 1, $offset);

        $isMore = count($users) > $limit;
        $users = array_slice($users, 0, $limit);

        $result = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'img' => $user->getPicture(),
                'username' => $user->getUsername(),
                'createdAt' => $user->getCreatedAt()->format('d/m/Y H:i'),
                'lastConnected' => $user->getLastConnected()->format('d/m/Y H:i'),
            ];
        }, $users);

        return new JsonResponse([
            'data' => $result,
            'limit' => $limit,
            'offset' => $offset,
            'count' => count($result),
            'order' => $order,
            'hasMore' => $isMore,
        ]);
    }

    #[Route(path:'/api/users/{user_id}', name:'delete_user', methods: ['DELETE'])]
    public function delete(
        int $user_id,
        Request $request, 
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface
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

        $entityManagerInterface->remove($user);
        $entityManagerInterface->flush();

        return new JsonResponse(['message' => 'User ' . $user_id . ' ' . $user->getUsername() . ' deleted'], Response::HTTP_OK);
    }
}