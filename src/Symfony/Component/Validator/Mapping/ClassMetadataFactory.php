<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Mapping;

use Symfony\Component\Security\Core\Util\ClassUtils;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;
use Symfony\Component\Validator\Mapping\Cache\CacheInterface;

/**
 * Implementation of ClassMetadataFactoryInterface
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony.com>
 */
class ClassMetadataFactory implements ClassMetadataFactoryInterface
{
    /**
     * The loader for loading the class metadata
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * The cache for caching class metadata
     * @var CacheInterface
     */
    protected $cache;

    protected $loadedClasses = array();

    public function __construct(LoaderInterface $loader, CacheInterface $cache = null)
    {
        $this->loader = $loader;
        $this->cache = $cache;
    }

    public function getClassMetadata($class)
    {
        $class = ClassUtils::getRealClass($class);
        $class = ltrim($class, '\\');

        if (null !== $this->cache && false !== ($this->loadedClasses[$class] = $this->cache->read($class))) {
            return $this->loadedClasses[$class];
        }

        $metadata = new ClassMetadata($class);

        // Include constraints from the parent class
        if ($parent = $metadata->getReflectionClass()->getParentClass()) {
            $metadata->mergeConstraints($this->getClassMetadata($parent->getName()));
        }

        // Include constraints from all implemented interfaces
        foreach ($metadata->getReflectionClass()->getInterfaces() as $interface) {
            if ('Symfony\Component\Validator\GroupSequenceProviderInterface' === $interface->getName()) {
                continue;
            }
            $metadata->mergeConstraints($this->getClassMetadata($interface->getName()));
        }

        $this->loader->loadClassMetadata($metadata);

        if ($this->cache !== null) {
            $this->cache->write($metadata);
        }

        return $this->loadedClasses[$class] = $metadata;
    }
}
