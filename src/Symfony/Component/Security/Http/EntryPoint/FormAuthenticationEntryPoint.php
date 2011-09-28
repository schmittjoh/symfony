<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\EntryPoint;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * FormAuthenticationEntryPoint starts an authentication via a login form.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FormAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $httpKernel;
    private $httpUtils;
    private $loginPath = '/login';
    private $useForward = false;

    /**
     * Constructor
     *
     * @param HttpKernelInterface $kernel
     * @param HttpUtils           $httpUtils  An HttpUtils instance
     */
    public function __construct(HttpKernelInterface $kernel, HttpUtils $httpUtils)
    {
        $this->httpKernel = $kernel;
        $this->httpUtils = $httpUtils;
    }

    public function setLoginPath($path)
    {
        $this->loginPath = $path;
    }

    public function setUseForward($bool)
    {
        $this->useForward = (Boolean) $bool;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($this->useForward) {
            $subRequest = $this->httpUtils->createRequest($request, $this->loginPath);

            return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
    }
}
