<?php

namespace Symfony\Bridge\Twig\Extension;

use Symfony\Bundle\FrameworkBundle\HttpKernel;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

class BcExtension extends \Twig_Extension
{
    private $httpKernel;

    public function __construct(HttpKernel $httpKernel)
    {
        $this->httpKernel = $httpKernel;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render', array($this, 'renderFragment'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('controller', array($this, 'controller')),
        );
    }

    /**
     * Renders a fragment.
     *
     * @param string|ControllerReference $uri     A URI as a string or a ControllerReference instance
     * @param array                      $options An array of options
     *
     * @return string The fragment content
     *
     * @see FragmentHandler::render()
     */
    public function renderFragment($uri, $options = array())
    {
        unset($options['strategy']);

        if ( ! $uri instanceof ControllerReference) {
            throw new \LogicException('$uri must be a ControllerReference, but was a string.');
        }

        $options['attributes'] = $uri->attributes;

        return $this->httpKernel->render($uri->controller, $options);
    }

    public function controller($controller, $attributes = array(), $query = array())
    {
        return new ControllerReference($controller, $attributes, $query);
    }

    public function getName()
    {
        return 'symfony_twig_bc';
    }
}