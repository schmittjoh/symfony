<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler\Func;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\FunctionExpression;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;

class HasAnyRoleFunctionCompiler implements FunctionCompilerInterface
{
    private $rolesExpr;

    public function getName()
    {
        return 'hasAnyRole';
    }

    public function compilePreconditions(ExpressionCompiler $compiler, FunctionExpression $function)
    {
        if (0 === count($function->args)) {
            throw new \RuntimeException('The function hasAnyRole() expects at least one argument, but got none.');
        }

        $this->rolesExpr = $compiler->getRolesExpr();
    }

    public function compile(ExpressionCompiler $compiler, FunctionExpression $function)
    {
        $compiler->write("(");

        $first = true;
        foreach ($function->args as $arg) {
            if (!$first) {
                $compiler->write(" || ");
            }
            $first = false;

            $compiler
                ->write("isset({$this->rolesExpr}[")
                ->compileInternal($arg)
                ->write("])")
            ;
        }

        $compiler->write(")");
    }
}