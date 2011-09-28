<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Firewall;

use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * UsernamePasswordFormAuthenticationListener is the default implementation of
 * an authentication via a simple form composed of a username and a password.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class UsernamePasswordFormAuthenticationListener extends AbstractAuthenticationListener
{
    private $csrfProvider;
    private $usernameParameter = '_username';
    private $passwordParameter = '_password';
    private $csrfParameter = '_csrf_token';
    private $intention = 'authenticate';
    private $postOnly = true;

    public function setCsrfProvider(CsrfProviderInterface $provider)
    {
        $this->csrfProvider = $provider;
    }

    public function setUsernameParameter($param)
    {
        $this->usernameParameter = $param;
    }

    public function setPasswordParameter($param)
    {
        $this->passwordParameter = $param;
    }

    public function setCsrfParameter($param)
    {
        $this->csrfParameter = $param;
    }

    public function setIntention($intention)
    {
        $this->intention = $intention;
    }

    public function setPostOnly($bool)
    {
        $this->postOnly = (Boolean) $bool;
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        if ($this->postOnly && 'post' !== strtolower($request->getMethod())) {
            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Authentication method not supported: %s.', $request->getMethod()));
            }

            return null;
        }

        if (null !== $this->csrfProvider) {
            $csrfToken = $request->get($this->csrfParameter, null, true);

            if (false === $this->csrfProvider->isCsrfTokenValid($this->intention, $csrfToken)) {
                throw new InvalidCsrfTokenException('Invalid CSRF token.');
            }
        }

        $username = trim($request->get($this->usernameParameter, null, true));
        $password = $request->get($this->passwordParameter, null, true);

        $request->getSession()->set(SecurityContextInterface::LAST_USERNAME, $username);

        $token = new UsernamePasswordToken($username, $password, $this->providerKey);

        if (null !== $this->tokenAttributeSource) {
            $token->setAttributes($this->tokenAttributeSource->buildAttributes($request));
        }

        return $this->authenticationManager->authenticate($token);
    }
}
