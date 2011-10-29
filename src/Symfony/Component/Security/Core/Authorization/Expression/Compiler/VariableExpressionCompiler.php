<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\VariableExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\ExpressionInterface;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;

class VariableExpressionCompiler implements TypeCompilerInterface
{
    public function getType()
    {
        return 'Symfony\Component\Security\Core\Authorization\Expression\Ast\VariableExpression';
    }

    public function compilePreconditions(ExpressionCompiler $compiler, ExpressionInterface $expr)
    {
        if ('user' === $expr->name) {
            $compiler
                ->setAttribute('user_var_name', $name = $compiler->nextName())
                ->write("\$$name = ")
                ->compileInternal(new VariableExpression('token'))
                ->write("->getUser();\n\n")
            ;
        }
    }

    public function compile(ExpressionCompiler $compiler, ExpressionInterface $expr)
    {
        if ('permitAll' === $expr->name) {
            $compiler->write('true');

            return;
        }

        if ('denyAll' === $expr->name) {
            $compiler->write('false');

            return;
        }

        if ('user' === $expr->name) {
            $compiler->write("\${$compiler->attributes['user_var_name']}");

            return;
        }

        $compiler->write("\$context['{$expr->name}']");
    }
}