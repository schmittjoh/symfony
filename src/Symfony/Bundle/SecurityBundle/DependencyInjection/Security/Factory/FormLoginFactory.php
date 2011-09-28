<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * FormLoginFactory creates services for form login authentication.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class FormLoginFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->addOption('username_parameter', '_username');
        $this->addOption('password_parameter', '_password');
        $this->addOption('csrf_parameter', '_csrf_token');
        $this->addOption('use_forward', false);
        $this->addOption('intention', 'authenticate');
        $this->addOption('post_only', true);
    }

    public function getPosition()
    {
        return 'form';
    }

    public function getKey()
    {
        return 'form-login';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        $node
            ->children()
                ->scalarNode('csrf_provider')->cannotBeEmpty()->end()
            ->end()
        ;
    }

    protected function getListenerId()
    {
        return 'security.authentication.listener.form';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = 'security.authentication.provider.dao.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('security.authentication.provider.dao'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $id)
        ;

        return $provider;
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        $def = $container->getDefinition($listenerId);

        if ($this->options['username_parameter'] !== $config['username_parameter']) {
            $def->addMethodCall('setUsernameParameter', array($config['username_parameter']));
        }
        if ($this->options['password_parameter'] !== $config['password_parameter']) {
            $def->addMethodCall('setPasswordParameter', array($config['password_parameter']));
        }
        if ($this->options['csrf_parameter'] !== $config['csrf_parameter']) {
            $def->addMethodCall('setCsrfParameter', array($config['csrf_parameter']));
        }
        if ($this->options['intention'] !== $config['intention']) {
            $def->addMethodCall('setIntention', array($config['intention']));
        }
        if ($this->options['post_only'] !== $config['post_only']) {
            $def->addMethodCall('setPostOnly', array($config['post_only']));
        }

        if (isset($config['csrf_provider'])) {
            $def->addMethodCall('setCsrfProvider', array(new Reference($config['csrf_provider'])));
        }

        return $listenerId;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'security.authentication.form_entry_point.'.$id;
        $def = $container
            ->setDefinition($entryPointId, new DefinitionDecorator('security.authentication.form_entry_point'))
            ->addArgument(new Reference('security.http_utils'))
        ;

        if ($this->options['login_path'] !== $config['login_path']) {
            $def->addMethodCall('setLoginPath', array($config['login_path']));
        }
        if ($this->options['use_forward'] !== $config['use_forward']) {
            $def->addMethodCall('setUseForward', array($config['use_forward']));
        }

        return $entryPointId;
    }
}
