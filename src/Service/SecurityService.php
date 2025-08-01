<?php

namespace App\Service;

use Symfony\Component\Security\Core\User\UserInterface;

class SecurityService
{
    public function isAdmin(?UserInterface $user): bool
    {
        if (!$user) {
            return false;
        }

        return in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPER_ADMIN', $user->getRoles());
    }
}