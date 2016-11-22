<?php

namespace YOOtheme\Framework\Joomla;

use YOOtheme\Framework\User\User;
use YOOtheme\Framework\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var string
     */
    protected $asset;

    /**
     * @var string[]
     */
    protected $permissions;

    /**
     * Constructor.
     *
     * @param string   $asset
     * @param string[] $permissions
     */
    public function __construct($asset, $permissions = array())
    {
        $this->asset       = $asset;
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id = null)
    {
        return $this->loadUserBy('id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getByUsername($username)
    {
        return $this->loadUserBy('username', $username);
    }

    /**
     * Loads a user.
     *
     * @param  string $field
     * @param  string $value
     * @return UserInterface
     */
    protected function loadUserBy($field, $value)
    {
        if (in_array($field, array('id', 'username')) && $user = \JFactory::getUser($value)) {

            $permissions = array();

            foreach($this->permissions as $jpermission => $permission) {
                if ($user->authorise($jpermission, $this->asset)) {
                    $permissions[] = $permission;
                }
            }

            return new User(array('id' => $user->id, 'username' => $user->username, 'email' => $user->email, 'permissions' => $permissions));
        }
    }
}
