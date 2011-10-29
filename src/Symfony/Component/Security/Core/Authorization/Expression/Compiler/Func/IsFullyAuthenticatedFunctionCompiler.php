<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler\Func;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\VariableExpression;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\FunctionExpression;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;

class IsFullyAuthenticatedFunctionCompiler extends AuthenticationTrustFunctionCompiler
{
    public function getName()
    {
        return 'isFullyAuthenticated';
    }

    public function compile(ExpressionCompiler $compiler, FunctionExpression $function)
    {
        $compiler
            ->compileInternal(new VariableExpression('trust_resolver'))
            ->write("->isFullFledged(\$context['token'])");
    }
}