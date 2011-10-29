<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler\Func;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\FunctionExpression;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;

abstract class AuthenticationTrustFunctionCompiler implements FunctionCompilerInterface
{
    public function compilePreconditions(ExpressionCompiler $compiler, FunctionExpression $function)
    {
        if (!empty($function->args)) {
            throw new \InvalidArgumentException(sprintf('The '.$this->getName().'() function does not accept any arguments, but got "%s".', var_export($function->args, true)));
        }

        $compiler->verifyItem('token', 'Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }
}