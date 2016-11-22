<?php

namespace YOOtheme\Framework\User;

interface UserInterface
{
    /**
     * Gets the identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Gets the username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Gets the permissions.
     *
     * @return array
     */
    public function getPermissions();

    /**
     * Check if the user has access for a provided permission identifier.
     *
     * @param  string  $permission
     * @return boolean
     */
    public function hasPermission($permission);
}
