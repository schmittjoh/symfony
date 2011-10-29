<?php

namespace Symfony\Component\Security\Acl\Expression;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\VariableExpression;
use Symfony\Component\Security\Core\Authorization\Expression\Ast\FunctionExpression;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;
use Symfony\Component\Security\Core\Authorization\Expression\Compiler\Func\FunctionCompilerInterface;

class HasPermissionFunctionCompiler implements FunctionCompilerInterface
{
    private $hasPermName;
    private $oidRetrievalStrategyLocalName;
    private $sidRetrievalStrategyLocalName;

    public function getName()
    {
        return 'hasPermission';
    }

    public function compilePreconditions(ExpressionCompiler $compiler, FunctionExpression $function)
    {
    }

    public function compile(ExpressionCompiler $compiler, FunctionExpression $function)
    {
        $compiler
            ->compileInternal(new VariableExpression('permission_evaluator'))
            ->write('->hasPermission(')
            ->compileInternal($function->args[0])
            ->write(', ')
            ->compileInternal($function->args[1])
            ->write(')')
        ;
    }
}