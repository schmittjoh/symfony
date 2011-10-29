<?php

namespace Symfony\Bundle\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AddExpressionCompilersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('security.expressions.compiler')) {
            return;
        }

        $compilerDef = $container->getDefinition('security.expressions.compiler');
        foreach ($container->findTaggedServiceIds('security.expressions.function_compiler')
            as $id => $attr) {
            $compilerDef->addMethodCall('addFunctionCompiler', array(new Reference($id)));
        }

        foreach ($container->findTaggedServiceIds('security.expressions.type_compiler')
            as $id => $attr) {
            $compilerDef->addMethodCall('addTypeCompiler', array(new Reference($id)));
        }

        $serviceMap = $parameterMap = array();
        foreach ($container->findTaggedServiceIds('security.expressions.variable') as $id => $attributes) {
            foreach ($attributes as $attr) {
                if (!isset($attr['variable']) || (!isset($attr['service']) && !isset($attr['parameter']))) {
                    throw new \RuntimeException(sprintf('"variable", and either "service" or "parameter" must be given for tag "security.expressions.variable" for service id "%s".', $id));
                }

                if (isset($attr['service'])) {
                    $serviceMap[$attr['variable']] = $attr['service'];
                    $container
                        ->findDefinition($attr['service'])
                        ->setPublic(true)
                    ;
                } else {
                    $parameterMap[$attr['variable']] = $attr['parameter'];
                }
            }
        }
        $container->getDefinition('security.expressions.variable_compiler')
            ->addMethodCall('setMaps', array($serviceMap, $parameterMap));
    }
}