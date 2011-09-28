<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Component\Security\Core\Exception;

/**
 * This exception is thrown when the csrf token is invalid.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class InvalidCsrfTokenException extends AuthenticationException
{
    public function getMessageTemplate()
    {
    	/** @Desc("The CSRF Token was invalid.") */
        return 'security.authentication_error.invalid_csrf_token';
    }
}
