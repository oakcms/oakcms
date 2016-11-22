<?php

namespace YOOtheme\Framework\User;

class User implements UserInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var array
     */
    protected $permissions = array();

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $vars = get_object_vars($this);

        foreach (array_intersect_key($data, $vars) as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the identifier.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the username.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Sets the permissions.
     *
     * @param array $permissions
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions);
    }
}
