<?php

namespace Symfony\Component\Security\Http\Authentication;

use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Default Authentication Success Handler implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class DefaultAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $httpUtils;
    private $targetPathParameter = '_target_path';
    private $alwaysUseDefaultTargetPath = false;
    private $defaultTargetPath = '/';
    private $useReferer = false;

    public function __construct(HttpUtils $utils)
    {
        $this->httpUtils = $utils;
    }

    public function setTargetPathParameter($param)
    {
        $this->targetPathParameter = $param;
    }

    public function setAlwaysUseDefaultTargetPath($bool)
    {
        $this->alwaysUseDefaultTargetPath = (Boolean) $bool;
    }

    public function setDefaultTargetPath($path)
    {
        $this->defaultTargetPath = $path;
    }

    public function setUseReferer($bool)
    {
        $this->useReferer = (Boolean) $bool;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($this->alwaysUseDefaultTargetPath) {
            return $this->httpUtils->createRedirectResponse($request, $this->defaultTargetPath);
        }

        if ($targetUrl = $request->get($this->targetPathParameter, null, true)) {
            return $this->httpUtils->createRedirectResponse($request, $targetUrl);
        }

        $session = $request->getSession();
        if ($targetUrl = $session->get('_security.target_path')) {
            $session->remove('_security.target_path');

            return $this->httpUtils->createRedirectResponse($request, $targetUrl);
        }

        if ($this->useReferer && $targetUrl = $request->headers->get('Referer')) {
            return $this->httpUtils->createRedirectResponse($request, $targetUrl);
        }

        return $this->httpUtils->createRedirectResponse($request, $this->defaultTargetPath);
    }
}