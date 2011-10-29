<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler;

class OrExpressionCompiler extends BinaryExprCompiler
{
    public function getType()
    {
        return 'Symfony\Component\Security\Core\Authorization\Expression\Ast\OrExpression';
    }

    protected function getOperator()
    {
        return '||';
    }
}