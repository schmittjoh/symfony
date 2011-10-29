<?php

namespace Symfony\Bundle\SecurityBundle\Security\Expression;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Security\Core\Authorization\Expression\ExpressionVoter;

class LazyLoadingExpressionVoter extends ExpressionVoter
{
    private $container;
    private $compilerId;

    public function setLazyCompiler(ContainerInterface $container, $id)
    {
        $this->container = $container;
        $this->compilerId = $id;
    }

    protected function getCompiler()
    {
        return $this->container->get($this->compilerId);
    }
}