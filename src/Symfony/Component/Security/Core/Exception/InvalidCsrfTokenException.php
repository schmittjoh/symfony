<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Exception;

/**
 * This exception is thrown when the csrf token is invalid.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class InvalidCsrfTokenException extends AuthenticationException
{
    public function getMessageKey()
    {
    	/** @Desc("The CSRF Token was invalid.") */
        return 'security.authentication_error.invalid_csrf_token';
    }
}
