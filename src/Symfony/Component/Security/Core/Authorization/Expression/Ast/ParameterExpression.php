<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Ast;

class ParameterExpression implements ExpressionInterface
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}