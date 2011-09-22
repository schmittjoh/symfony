<?php

namespace Symfony\Tests\Component\Routing\Fixtures\AnnotatedClasses;

use Symfony\Component\Routing\Annotation as R;

abstract class AbstractClassAsController
{
    /**
     * @R\Route("/foo")
     */
    public function indexAction()
    {
    }
}