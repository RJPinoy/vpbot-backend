<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTLoginSuccessHandler implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $user = $event->getUser();
        $data = $event->getData(); // contains the JWT token already under 'token'

        if (method_exists($user, 'setLastConnected')) {
            $user->setLastConnected(new \DateTime());
            $this->em->flush();
        }

        // Customize the response data:
        $data['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ];

        $event->setData($data);
    }
}