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
 * AccountExpiredException is thrown when the user account has expired.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AccountExpiredException extends AccountStatusException
{
    /** @Desc("This account has expired.") */
    public function getMessageTemplate()
    {
        return 'security.authentication_error.account_expired';
    }
}
