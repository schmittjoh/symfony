<?php

namespace Symfony\Bundle\SecurityBundle\Security\Expression\Compiler;

use Symfony\Component\Security\Core\Authorization\Expression\Ast\ExpressionInterface;
use Symfony\Component\Security\Core\Authorization\Expression\ExpressionCompiler;
use Symfony\Component\Security\Core\Authorization\Expression\Compiler\VariableExpressionCompiler;

class ContainerAwareVariableCompiler extends VariableExpressionCompiler
{
    private $serviceMap = array();
    private $parameterMap = array();

    public function setMaps(array $serviceMap, array $parameterMap)
    {
        $this->serviceMap = $serviceMap;
        $this->parameterMap = $parameterMap;
    }

    public function compile(ExpressionCompiler $compiler, ExpressionInterface $expr)
    {
        if (isset($this->serviceMap[$expr->name])) {
            $compiler->write("\$context['container']->get('{$this->serviceMap[$expr->name]}')");

            return;
        }
        if (isset($this->parameterMap[$expr->name])) {
            $compiler->write("\$context['container']->getParameter('{$this->parameterMap[$expr->name]}')");

            return;
        }

        parent::compile($compiler, $expr);
    }
}