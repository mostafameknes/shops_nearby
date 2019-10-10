<?php
namespace App\Security;

use App\Entity\User;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        // check if the user account is enabled
        if (!$user->getEnabled()) {
            throw new CustomUserMessageAuthenticationException("this member is not active, please contact the web administrator for further information");
        }
    }
}