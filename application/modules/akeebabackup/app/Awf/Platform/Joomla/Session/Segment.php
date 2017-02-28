<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * The Session package in Awf is based on the Session package in Aura for PHP. Please consult the LICENSE file in the
 * Awf\Session package for copyright and license information.
 */

namespace Awf\Platform\Joomla\Session;

use Awf\Session\SegmentInterface;

/**
 * A session segment; lazy-loads from the session.
 */
class Segment extends \Awf\Session\Segment implements SegmentInterface
{
	/**
	 *
	 * The session manager.
	 *
	 * @var Manager
	 *
	 */
	protected $session;

	/**
	 *
	 * The segment name.
	 *
	 * @var string
	 *
	 */
	protected $name;

	/**
	 *
	 * Constructor.
	 *
	 * @param Manager $session The session manager.
	 *
	 * @param string  $name    The segment name.
	 *
	 */
	public function __construct(Manager $session, $name)
	{
		$this->session = $session;
		$this->name = $name;
	}

	/**
	 * Checks to see if the segment data has been loaded; if not, checks to
	 * see if a session has already been started or is available, and then
	 * loads the segment data from the session.
	 *
	 * @return bool
	 */
	protected function isLoaded()
	{
		if (!$this->session->isStarted())
		{
			$this->session->start();
		}

		return true;
	}

	/**
	 *
	 * Forces a session start (or reactivation) and loads the segment data
	 * from the session.
	 *
	 * @return void
	 *
	 */
	protected function load()
	{
		// Nothing to do. Joomla! handles this for us.
	}

	/**
	 *
	 * Returns the value of a key in the segment.
	 *
	 * @param string $key The key in the segment.
	 *
	 * @return mixed
	 *
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	/**
	 * Sets the value of a key in the segment.
	 *
	 * @param string $key The key to set.
	 * @param mixed  $val The value to set it to.
	 *
	 * @return mixed The old value
	 */
	public function __set($key, $val)
	{
		return \JFactory::getSession()->set($key, $val, 'awf.' . $this->name);
	}

	/**
	 *
	 * Check whether a key is set in the segment.
	 *
	 * @param string $key The key to check.
	 *
	 * @return bool
	 *
	 */
	public function __isset($key)
	{
		return $this->has($key);
	}

	/**
	 *
	 * Unsets a key in the segment.
	 *
	 * @param string $key The key to unset.
	 *
	 * @return void
	 *
	 */
	public function __unset($key)
	{
		$this->remove($key);
	}

	/**
	 *
	 * Clear all data from the segment.
	 *
	 * @return void
	 *
	 */
	public function clear()
	{
		$namespace = '__' . 'awf.' . $this->name;
		unset($_SESSION[$namespace]);
	}

	/**
	 *
	 * Gets the segment name.
	 *
	 * @return string
	 *
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 *
	 * Sets a read-once flash value on the segment.
	 *
	 * @param string $key The key for the flash value.
	 *
	 * @param mixed  $val The flash value itself.
	 *
	 */
	public function setFlash($key, $val)
	{
		$flash = $this->get('__flash', array());

		if (empty($flash))
		{
			$flash = array();
		}

		$flash[$key] = $val;

		$this->set('__flash', $flash);
	}

	/**
	 *
	 * Reads the flash value for a key, thereby removing it from the session.
	 *
	 * @param string $key The key for the flash value.
	 *
	 * @return mixed The flash value itself.
	 *
	 */
	public function getFlash($key)
	{
		$flash = $this->get('__flash', array());

		if (empty($flash))
		{
			$flash = array();
		}

		$ret = null;

		if (isset($flash[$key]))
		{
			$ret = $flash[$key];
			unset($flash[$key]);
		}

		$this->set('__flash', $flash);

		return $ret;
	}

	/**
	 *
	 * Checks whether a flash key is set, without reading it.
	 *
	 * @param string $key The flash key to check.
	 *
	 * @return bool True if it is set, false if not.
	 *
	 */
	public function hasFlash($key)
	{
		$flash = $this->get('__flash', array());

		if (empty($flash))
		{
			$flash = array();
		}

		return isset($flash[$key]);
	}

	/**
	 *
	 * Clears all flash values.
	 *
	 * @return void
	 *
	 */
	public function clearFlash()
	{
		$this->set('__flash', array());
	}

	/**
	 * Does this segment have the specified session variable?
	 *
	 * @param   string $key The session variable's key (name)
	 *
	 * @return  boolean  True if the session variable exists
	 */
	public function has($key)
	{
		return \JFactory::getSession()->has($key, 'awf' . $this->getName());
	}

	/**
	 * Set a session variable
	 *
	 * @param   string $key The name of the session variable
	 * @param   mixed  $val The value to set it to
	 *
	 * @return  void
	 */
	public function set($key, $val)
	{
		\JFactory::getSession()->set($key, $val, 'awf.' . $this->name);
	}

	/**
	 * Get the value of a session variable. If the session variable does not exist it is initialised with $default
	 *
	 * @param   string $key     The session variable's name
	 * @param   mixed  $default [Optional] The defualt value to use if the session variable doesn't exist
	 *
	 * @return  mixed  The value of the session variable
	 */
	public function get($key, $default = null)
	{
		return \JFactory::getSession()->get($key, $default, 'awf.' . $this->name);
	}

	/**
	 * Removes a session variable
	 *
	 * @param   string $key The session variable's name
	 *
	 * @return  void
	 */
	public function remove($key)
	{
		\JFactory::getSession()->clear($key, 'awf.' . $this->name);
	}
}
