<?php

namespace Symfony\Component\Security\Core\Event;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * This is a general purpose authentication event.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AuthenticationEvent extends Event
{
    private $authenticationToken;

    public function __construct(TokenInterface $token)
    {
        $this->authenticationToken = $token;
    }

    public function getAuthenticationToken()
    {
        return $this->authenticationToken;
    }
}