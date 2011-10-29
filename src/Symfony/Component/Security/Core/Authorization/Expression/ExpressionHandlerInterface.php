<?php

namespace Symfony\Component\Security\Core\Authorization\Expression;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface ExpressionHandlerInterface
{
    function createContext(TokenInterface $token, $object);
}