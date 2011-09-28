<?php

namespace Symfony\Component\Security\Http\Authentication;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Default Authentication Failure Handler implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class DefaultAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    private $kernel;
    private $httpUtils;
    private $logger;
    private $failurePath;
    private $failureForward = false;

    public function __construct(HttpKernelInterface $kernel, HttpUtils $utils)
    {
        $this->kernel = $kernel;
        $this->httpUtils = $utils;
    }

    public function setFailurePath($path)
    {
        $this->failurePath = $path;
    }

    public function setFailureForward($bool)
    {
        $this->failureForward = (Boolean) $bool;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if (null === $this->failurePath) {
            throw new HttpException(401);
        }

        if ($this->failureForward) {
            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Forwarding to %s', $this->failurePath));
            }

            $subRequest = $this->httpUtils->createRequest($request, $this->failurePath);
            $subRequest->attributes->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

            return $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Redirecting to %s', $this->failurePath));
        }
        $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

        return $this->httpUtils->createRedirectResponse($request, $this->failurePath);
    }
}