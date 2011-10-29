<?php

namespace Symfony\Component\Security\Core\Authorization\Expression;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class DefaultExpressionHandler implements ExpressionHandlerInterface
{
    private $trustResolver;
    private $roleHierarchy;

    public function __construct(AuthenticationTrustResolverInterface $trustResolver,
        RoleHierarchyInterface $roleHierarchy = null)
    {
        $this->trustResolver = $trustResolver;
        $this->roleHierarchy = $roleHierarchy;
    }

    public function createContext(TokenInterface $token, $object)
    {
        $context = array(
            'token' => $token,
            'object' => $object,
            'trust_resolver' => $this->trustResolver,
        );

        if (null !== $this->roleHierarchy) {
            $context['role_hierarchy'] = $this->roleHierarchy;
        }

        return $context;
    }
}