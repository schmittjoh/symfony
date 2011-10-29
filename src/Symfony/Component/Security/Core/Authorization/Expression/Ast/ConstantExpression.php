<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Ast;

class ConstantExpression implements ExpressionInterface
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}