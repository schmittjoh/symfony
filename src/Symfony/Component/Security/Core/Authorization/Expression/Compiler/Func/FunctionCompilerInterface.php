<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler\Func;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\FunctionExpression;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;

interface FunctionCompilerInterface
{
    function getName();
    function compilePreconditions(ExpressionCompiler $compiler, FunctionExpression $function);
    function compile(ExpressionCompiler $compiler, FunctionExpression $function);
}