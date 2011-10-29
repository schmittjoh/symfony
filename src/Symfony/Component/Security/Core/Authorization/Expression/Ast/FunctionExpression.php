<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Ast;

class FunctionExpression implements ExpressionInterface
{
    /** READ-ONLY */
    public $name;
    public $args;

    public function __construct($name, array $args)
    {
        $this->name = $name;
        $this->args = $args;
    }
}