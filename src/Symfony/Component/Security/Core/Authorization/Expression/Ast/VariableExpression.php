<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Ast;

class VariableExpression implements ExpressionInterface
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}