<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler\Func;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\FunctionExpression;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;

class HasRoleFunctionCompiler implements FunctionCompilerInterface
{
    private $rolesExpr;

    public function getName()
    {
        return 'hasRole';
    }

    public function compilePreconditions(ExpressionCompiler $compiler, FunctionExpression $function)
    {
        if (1 !== count($function->args)) {
            throw new \RuntimeException(sprintf('The hasRole() function expects exactly one argument, but got "%s".', var_export($function->args, true)));
        }

        $this->rolesExpr = $compiler->getRolesExpr();
    }

    public function compile(ExpressionCompiler $compiler, FunctionExpression $function)
    {
        $compiler
            ->write("isset({$this->rolesExpr}[")
            ->compileInternal($function->args[0])
            ->write("])")
        ;
    }
}