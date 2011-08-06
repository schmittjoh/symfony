<?php

namespace Symfony\Component\Security\Core\Authentication\RememberMe;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Interface for TokenProviders
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface TokenProviderInterface
{
    /**
     * Loads the active token for the given series
     *
     * @throws TokenNotFoundException if the token is not found
     *
     * @param string $series
     * @return PersistentTokenInterface
     */
    function loadTokenBySeries($series);

    /**
     * Deletes all tokens belonging to series
     * @param string $series
     * @return void
     */
    function deleteTokenBySeries($series);

    /**
     * Updates the token according to this data
     *
     * @param string   $series
     * @param string   $tokenValue
     * @param DateTime $lastUsed
     * @return void
     */
    function updateToken($series, $tokenValue, \DateTime $lastUsed);

    /**
     * Creates a new token.
     *
     * @param string   $userClass
     * @param string   $username
     * @param string   $series
     * @param string   $token
     * @param DateTime $lastUsed
     * @return void
     */
    function createNewToken($userClass, $username, $series, $token, \DateTime $lastUsed);
}
