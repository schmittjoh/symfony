<?php

namespace Symfony\Component\Security\Core\User;

/**
 * User Manager Interface.
 *
 * Extends the UserProviderInterface with some additional functionality for
 * managing users.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface UserManagerInterface extends UserProviderInterface
{
    /**
     * Creates a new user with the given details.
     *
     * @param UserInterface $user
     * @return void
     */
    function createUser(UserInterface $user);

    /**
     * Updates the user with the given details.
     *
     * @param UserInterface $user
     * @return void
     */
    function updateUser(UserInterface $user);

    /**
     * Deletes the user.
     *
     * @param string $username
     * @return void
     */
    function deleteUser($username);

    /**
     * Returns whether the given user exists.
     *
     * @param string $username
     * @return Boolean
     */
    function userExists($username);

    /**
     * Updates the password of the currently logged in user.
     *
     * Implementations need to make sure to update the password in the
     * persistence store, and in the current security context.
     *
     * @param string $newPassword
     * @param string $currentPassword for re-authentication, if required;
     *                                may be null
     * @return void
     */
    function changePassword($newPassword, $currentPassword = null);
}