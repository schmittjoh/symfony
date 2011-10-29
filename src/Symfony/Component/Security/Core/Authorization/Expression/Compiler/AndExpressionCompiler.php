<?php

namespace Symfony\Component\Security\Core\Authorization\Expression\Compiler;

class AndExpressionCompiler extends BinaryExprCompiler
{
    public function getType()
    {
        return 'Symfony\Component\Security\Core\Authorization\Expression\Ast\AndExpression';
    }

    protected function getOperator()
    {
        return '&&';
    }
}