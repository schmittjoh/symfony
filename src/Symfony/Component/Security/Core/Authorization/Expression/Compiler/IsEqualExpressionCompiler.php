<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler;

class IsEqualExpressionCompiler extends BinaryExprCompiler
{
    public function getType()
    {
        return 'Symfony\Component\Security\Core\Authorization\Expression\Ast\IsEqualExpression';
    }

    protected function getOperator()
    {
        return '===';
    }
}