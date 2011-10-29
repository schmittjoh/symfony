<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Ast;

class MethodCallExpression implements ExpressionInterface
{
    public $object;
    public $method;
    public $args;

    public function __construct(ExpressionInterface $obj, $method, array $args)
    {
        $this->object = $obj;
        $this->method = $method;
        $this->args = $args;
    }
}