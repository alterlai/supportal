<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $user)
    {

        if ($user->isSuspended())
        {
            throw new CustomUserMessageAuthenticationException("Dit account is uitgeschakeld vanwege inactiviteit. Neem contact op met IQSupport om het account te ontgrendelen.");
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $user)
    {
        if ($user->isSuspended())
        {
            throw new CustomUserMessageAuthenticationException("Dit account is uitgeschakeld vanwege inactiviteit. Neem contact op met IQSupport om het account te ontgrendelen.");
        }
    }
}