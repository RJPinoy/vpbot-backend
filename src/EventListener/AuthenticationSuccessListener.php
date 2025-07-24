<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;

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

        if (method_exists($user, 'setLastConnected')) {
            $user->setLastConnected(new \DateTime());
            $this->em->flush();
        }

        $event->setData([
            'code' => $event->getResponse()->getStatusCode(),
            'token' => $event->getData()['token'],
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
        ]);
   }
}