<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler\Func;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\VariableExpression;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\FunctionExpression;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;

class IsRememberMeFunctionCompiler extends AuthenticationTrustFunctionCompiler
{
    public function getName()
    {
        return 'isRememberMe';
    }

    public function compile(ExpressionCompiler $compiler, FunctionExpression $function)
    {
        $compiler
            ->compileInternal(new VariableExpression('trust_resolver'))
            ->write("->isRememberMe(\$context['token'])");
    }
}