<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\ExpressionInterface;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;

/**
 * Base Compiler for Binary Operators.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class BinaryExprCompiler implements TypeCompilerInterface
{
    public function compilePreconditions(ExpressionCompiler $compiler, ExpressionInterface $expr)
    {
        $compiler
            ->compilePreconditions($expr->left)
            ->compilePreconditions($expr->right)
        ;
    }

    public function compile(ExpressionCompiler $compiler, ExpressionInterface $expr)
    {
        $compiler
            ->compileInternal($expr->left)
            ->write(" ".$this->getOperator()." ")
            ->compileInternal($expr->right)
        ;
    }

    abstract protected function getOperator();
}