<?php

namespace YOOtheme\Framework\User;

interface UserProviderInterface
{
    /**
     * Gets a user by id.
     *
     * @param  string|null $id
     * @return UserInterface
     */
    public function get($id = null);

    /**
     * Gets a user by username.
     *
     * @param  string $username
     * @return UserInterface
     */
    public function getByUsername($username);
}
