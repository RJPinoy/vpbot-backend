<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthenticationSuccessListener
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
    * @param AuthenticationSuccessEvent $event
    */
   public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $user = $event->getUser();
        $token = $event->getData()['token'];

        // Update last connected time
        if (method_exists($user, 'setLastConnected')) {
            $user->setLastConnected(new \DateTime());
            $this->em->flush();
        }

        // Add custom fields to the response data
        $data = $event->getData();
        $data['code'] = 200;
        $data['token'] = $token;
        $data['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ];
        $event->setData($data);

        // Set token as HTTP-only cookie
        $event->getResponse()->headers->setCookie(
            Cookie::create('JWT', $token, new \DateTime('+1 hour'), '/', null, false, true, false, 'Lax')
        );
    }
}