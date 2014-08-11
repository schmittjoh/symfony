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
 * UsernameNotFoundException is thrown if a User cannot be found by its username.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UsernameNotFoundException extends AuthenticationException
{
    private $username;

    public function __construct($username, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('The username "%s" was not found.', $username), $code, $previous);

        $this->username = $username;
    }

    public function getMessageKey()
    {
        return 'The username "%username%" was not found.';
    }

    public function getMessageParameters()
    {
        return array('%username%' => $this->username);
    }
}
