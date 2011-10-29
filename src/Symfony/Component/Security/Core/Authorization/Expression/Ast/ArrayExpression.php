<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Ast;

class ArrayExpression implements ExpressionInterface
{
    public $elements;

    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }
}