<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * The Session package in Awf is based on the Session package in Aura for PHP. Please consult the LICENSE file in the
 * Awf\Session package for copyright and license information.
 */

namespace Awf\Session;
use Awf\Encrypt\Base32;

/**
 * A session segment; lazy-loads from the session.
 */
class Segment implements SegmentInterface
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
	 * The data in the segment is a reference to a $_SESSION key.
	 *
	 * @var array
	 *
	 */
	protected $data;

	/**
	 * Base32 encoder, in case base64 encoding is not available on this server
	 *
	 * @var Base32
	 */
	protected $encoder;

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
		$this->encoder = new Base32();
	}

	/**
	 *
	 * Checks to see if the segment data has been loaded; if not, checks to
	 * see if a session has already been started or is available, and then
	 * loads the segment data from the session.
	 *
	 * @return bool
	 *
	 */
	protected function isLoaded()
	{
		if ($this->data !== null)
		{
			return true;
		}

		if (!$this->session->isStarted())
		{
			$this->session->start();
		}

		if ($this->session->isAvailable())
		{
			$this->load();

			return true;
		}

		return false;
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
		// is data already loaded?
		if ($this->data !== null)
		{
			// no need to re-load
			return;
		}

		// if the session is not started, start it
		if (!$this->session->isStarted())
		{
			$this->session->start();
		}

		// Intialize data
		$this->data = array();

		// Try loading data from the session
		if (isset($_SESSION[$this->name]) && !empty($_SESSION[$this->name]))
		{
			$data = $_SESSION[$this->name];

			if (function_exists('base64_encode') && function_exists('base64_decode'))
			{
				$data = base64_decode($data);
			}
			else
			{
				$data = $this->encoder->decode($data);
			}

			$this->data = unserialize($data);
		}
	}

	/**
	 * Commit the session data to PHP's session storage using a safely encoded format to prevent PHP session
	 * unserialization attacks.
	 *
	 * @return  void
	 */
	public function save()
	{
		$data = serialize($this->data);

		if (function_exists('base64_encode') && function_exists('base64_decode'))
		{
			$data = base64_encode($data);
		}
		else
		{
			$data = $this->encoder->encode($data);
		}

		$_SESSION[$this->name] = $data;
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
		if ($this->isLoaded())
		{
			return isset($this->data[$key]) ? $this->data[$key] : null;
		}
	}

	/**
	 *
	 * Sets the value of a key in the segment.
	 *
	 * @param string $key The key to set.
	 *
	 * @param mixed  $val The value to set it to.
	 *
	 */
	public function __set($key, $val)
	{
		$this->load();
		$this->data[$key] = $val;
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
		if ($this->isLoaded())
		{
			return isset($this->data[$key]);
		}

		return false;
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
		if ($this->isLoaded())
		{
			unset($this->data[$key]);
		}
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
		if ($this->isLoaded())
		{
			$this->data = array();
		}
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
		$this->load();
		$this->data['__flash'][$key] = $val;
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
		if ($this->isLoaded() && isset($this->data['__flash'][$key]))
		{
			$val = $this->data['__flash'][$key];
			unset($this->data['__flash'][$key]);

			return $val;
		}
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
		if ($this->isLoaded())
		{
			return isset($this->data['__flash'][$key]);
		}

		return false;
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
		if ($this->isLoaded())
		{
			unset($this->data['__flash']);
		}
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
		if ($this->isLoaded())
		{
			return (isset($this->data[$key]));
		}
		else
		{
			return false;
		}
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
		if (!$this->isLoaded())
		{
			$this->load();
		}

		$this->data[$key] = $val;
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
		if ($this->isLoaded())
		{
			if (!isset($this->data[$key]))
			{
				$this->data[$key] = $default;
			}
		}
		else
		{
			$this->set($key, $default);
		}

		return $this->data[$key];
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
		if ($this->isLoaded() && isset($this->data[$key]))
		{
			unset($this->data[$key]);
		}
	}
}
