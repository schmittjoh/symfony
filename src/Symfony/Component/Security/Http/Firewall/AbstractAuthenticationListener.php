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

use Symfony\Component\Security\Http\Authentication\TokenAttributeSourceInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\SessionUnavailableException;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\SecurityEvents;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * The AbstractAuthenticationListener is the preferred base class for all
 * browser-/HTTP-based authentication requests.
 *
 * Subclasses likely have to implement the following:
 * - an TokenInterface to hold authentication related data
 * - an AuthenticationProvider to perform the actual authentication of the
 *   token, retrieve the UserInterface implementation from a database, and
 *   perform the specific account checks using the UserChecker
 *
 * By default, this listener only is active for a specific path, e.g.
 * /login_check. If you want to change this behavior, you can overwrite the
 * requiresAuthentication() method.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class AbstractAuthenticationListener implements ListenerInterface
{
    protected $logger;
    protected $authenticationManager;
    protected $providerKey;
    protected $tokenAttributeSource;
    protected $httpUtils;

    private $securityContext;
    private $sessionStrategy;
    private $dispatcher;
    private $successHandler;
    private $failureHandler;
    private $rememberMeServices;
    private $checkPath = '/login_check';

    /**
     * Constructor.
     *
     * @param SecurityContextInterface       $securityContext       A SecurityContext instance
     * @param AuthenticationManagerInterface $authenticationManager An AuthenticationManagerInterface instance
     * @param SessionAuthenticationStrategyInterface $sessionStrategy
     * @param HttpUtils $httpUtils An HttpUtilsInterface instance
     * @param string                                 $providerKey
     * @param AuthenticationSuccessHandlerInterface  $successHandler
     * @param AuthenticationFailureHandlerInterface  $failureHandler
     * @param LoggerInterface          $logger     A LoggerInterface instance
     * @param EventDispatcherInterface $dispatcher An EventDispatcherInterface instance
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->sessionStrategy = $sessionStrategy;
        $this->providerKey = $providerKey;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        $this->httpUtils = $httpUtils;
    }

    /**
     * Sets the RememberMeServices implementation to use
     *
     * @param RememberMeServicesInterface $rememberMeServices
     */
    public function setRememberMeServices(RememberMeServicesInterface $rememberMeServices)
    {
        $this->rememberMeServices = $rememberMeServices;
    }

    public function setCheckPath($path)
    {
        $this->checkPath = $path;
    }

    /**
     * Sets the TokenAttribteSource implementation to use for building the
     * default attributes of the token.
     *
     * @param TokenAttributeSourceInterface $source
     */
    public function setTokenAttributeSource(TokenAttributeSourceInterface $source)
    {
        $this->tokenAttributeSource = $source;
    }

    /**
     * Handles form based authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public final function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$this->requiresAuthentication($request)) {
            return;
        }

        if (!$request->hasSession()) {
            throw new \RuntimeException('This authentication method requires a session.');
        }

        try {
            if (!$request->hasPreviousSession()) {
                throw new SessionUnavailableException('Your session has timed out, or you have disabled cookies.');
            }

            if (null === $returnValue = $this->attemptAuthentication($request)) {
                return;
            }

            if ($returnValue instanceof TokenInterface) {
                $this->sessionStrategy->onAuthentication($request, $returnValue);

                if (null !== $this->logger) {
                	$this->logger->info(sprintf('User "%s" has been authenticated successfully', $returnValue->getUsername()));
                }

                $this->securityContext->setToken($returnValue);

                $session = $request->getSession();
                $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
                $session->remove(SecurityContextInterface::LAST_USERNAME);

                if (null !== $this->dispatcher) {
                	$loginEvent = new InteractiveLoginEvent($request, $returnValue);
                	$this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
                }

                $response = $this->successHandler->onAuthenticationSuccess($request, $returnValue);

                if (null !== $this->rememberMeServices) {
                	$this->rememberMeServices->loginSuccess($request, $response, $returnValue);
                }
            } elseif ($returnValue instanceof Response) {
                $response = $returnValue;
            } else {
                throw new \RuntimeException('attemptAuthentication() must either return a Response, an implementation of TokenInterface, or null.');
            }
        } catch (AuthenticationException $e) {
            if (null !== $this->logger) {
                $this->logger->info(sprintf('Authentication request failed: %s', $e->getMessage()));
            }

            $this->securityContext->setToken(null);
            $response = $this->failureHandler->onAuthenticationFailure($request, $e);
        }

        $event->setResponse($response);
    }

    /**
     * Whether this request requires authentication.
     *
     * The default implementation only processed requests to a specific path,
     * but a subclass could change this to only authenticate requests where a
     * certain parameters is present.
     *
     * @param Request $request
     *
     * @return Boolean
     */
    protected function requiresAuthentication(Request $request)
    {
        return $this->httpUtils->checkRequestPath($request, $this->checkPath);
    }

    /**
     * Performs authentication.
     *
     * @param Request $request A Request instance
     *
     * @return TokenInterface The authenticated token, or null if full authentication is not possible
     *
     * @throws AuthenticationException if the authentication fails
     */
    abstract protected function attemptAuthentication(Request $request);
}
