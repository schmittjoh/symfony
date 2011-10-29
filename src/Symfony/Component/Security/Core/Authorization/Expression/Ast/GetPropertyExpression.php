<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Ast;

class GetPropertyExpression implements ExpressionInterface
{
    public $object;
    public $name;

    public function __construct(ExpressionInterface $obj, $name)
    {
        $this->object = $obj;
        $this->name = $name;
    }
}