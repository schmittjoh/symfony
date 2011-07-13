<?php

namespace Symfony\Component\Security\Core\Event;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * This event is dispatched on authentication failure.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AuthenticationFailureEvent extends AuthenticationEvent
{
    private $authenticationException;

    public function __construct(TokenInterface $token, AuthenticationException $ex)
    {
        parent::__construct($token);

        $this->authenticationException = $ex;
    }

    public function getAuthenticationException()
    {
        return $this->authenticationException;
    }
}