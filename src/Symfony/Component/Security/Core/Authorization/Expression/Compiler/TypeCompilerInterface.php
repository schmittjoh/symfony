<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\ExpressionInterface;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;

interface TypeCompilerInterface
{
    function getType();
    function compilePreconditions(ExpressionCompiler $compiler, ExpressionInterface $expr);
    function compile(ExpressionCompiler $compiler, ExpressionInterface $expr);
}