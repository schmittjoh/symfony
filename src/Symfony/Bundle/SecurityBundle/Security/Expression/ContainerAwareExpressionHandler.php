<?php

namespace Symfony\Bundle\SecurityBundle\Security\Expression;

use Symfony\Component\Security\Core\Authorization\Expression\ExpressionHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Lazy-loading container aware expression handler.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ContainerAwareExpressionHandler implements ExpressionHandlerInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createContext(TokenInterface $token, $object)
    {
        return array(
            'container' => $this->container,
            'token'     => $token,
            'object'    => $object,
        );
    }
}