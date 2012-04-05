<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Config\FileLocator;

class AnnotationDirectoryLoaderTest extends AbstractAnnotationLoaderTest
{
    protected $loader;
    protected $reader;

    protected function setUp()
    {
        parent::setUp();

        $this->reader = new AnnotationReader();
        $this->loader = new AnnotationDirectoryLoader(new FileLocator(), $this->getClassLoader($this->reader));
    }

    public function testLoad()
    {
        $col = $this->loader->load(__DIR__.'/../Fixtures/AnnotatedClasses');
        $this->assertEquals(2, count($col->all()));
    }

    /**
     * @covers Symfony\Component\Routing\Loader\AnnotationDirectoryLoader::supports
     */
    public function testSupports()
    {
        $fixturesDir = __DIR__.'/../Fixtures';

        $this->assertTrue($this->loader->supports($fixturesDir), '->supports() returns true if the resource is loadable');
        $this->assertFalse($this->loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');

        $this->assertTrue($this->loader->supports($fixturesDir, 'annotation'), '->supports() checks the resource type if specified');
        $this->assertFalse($this->loader->supports($fixturesDir, 'foo'), '->supports() checks the resource type if specified');
    }
}
