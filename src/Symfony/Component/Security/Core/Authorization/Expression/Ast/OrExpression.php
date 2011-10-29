<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Ast;

class OrExpression implements ExpressionInterface
{
    public $left;
    public $right;

    public function __construct(ExpressionInterface $left, ExpressionInterface $right)
    {
        $this->left = $left;
        $this->right = $right;
    }
}