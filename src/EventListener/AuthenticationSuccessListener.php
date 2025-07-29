<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationSuccessListener
{
    private $em;
    private $requestStack;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
    }

    /**
    * @param AuthenticationSuccessEvent $event
    */
   public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $user = $event->getUser();
        $token = $event->getData()['token'];
        $request = $this->requestStack->getCurrentRequest();

        if (str_contains($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $rememberMe = isset($data['rememberMe']) && filter_var($data['rememberMe'], FILTER_VALIDATE_BOOLEAN);
        }
        $expiresAt = $rememberMe ? new \DateTime('+7 days') : 0;

        // Update last connected time
        if (method_exists($user, 'setLastConnected')) {
            $user->setLastConnected(new \DateTime());
            $this->em->flush();
        }

        // Add custom fields to the response data
        // $data = $event->getData();
        $data['code'] = 200;
        $data['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ];
        $data['rememberMe'] = $rememberMe;
        $event->setData($data);

        // Set token as HTTP-only cookie
        $event->getResponse()->headers->setCookie(
            Cookie::create('EXT_JWT', $token, $expiresAt, '/', null, false, true, false, 'Lax')
        );
    }
}