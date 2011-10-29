<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Ast;

class GetItemExpression
{
    public $array;
    public $key;

    public function __construct(ExpressionInterface $array, ExpressionInterface $key)
    {
        $this->array = $array;
        $this->key = $key;
    }
}