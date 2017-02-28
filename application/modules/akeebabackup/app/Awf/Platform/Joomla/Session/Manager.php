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

use Awf\Platform\Joomla\Helper\Helper;
use Awf\Session\Manager as SessionManager;
use Awf\Session\Segment;
use Awf\Session\SegmentFactory;
use Awf\Session\CsrfToken;
use Awf\Session\CsrfTokenFactory;

if (!defined('PHP_SESSION_NONE'))
{
	define('PHP_SESSION_NONE', 0);
}
if (!defined('PHP_SESSION_ACTIVE'))
{
	define('PHP_SESSION_ACTIVE', 1);
}

/**
 * A central control point for new session segments, PHP session management
 * values, and CSRF token checking.
 */
class Manager extends SessionManager
{

	/**
	 *
	 * A session segment factory.
	 *
	 * @var SegmentFactory
	 *
	 */
	protected $segment_factory;

	/**
	 *
	 * The CSRF token for this session.
	 *
	 * @var CsrfToken
	 *
	 */
	protected $csrf_token;

	/**
	 *
	 * A CSRF token factory, for lazy-creating the CSRF token.
	 *
	 * @var CsrfTokenFactory
	 *
	 */
	protected $csrf_token_factory;

	/**
	 *
	 * Incoming cookies from the client, typically a copy of the $_COOKIE
	 * superglobal.
	 *
	 * @var array
	 *
	 */
	protected $cookies;

	/**
	 *
	 * Session cookie parameters.
	 *
	 * @var array
	 *
	 */
	protected $cookie_params = array();

	/**
	 * The names of the segments created by this session manager
	 *
	 * @var array
	 */
	protected $segments = array();

	/**
	 *
	 * Constructor
	 *
	 * @param SegmentFactory $segment_factory A session segment factory.
	 *
	 * @param                CsrfTokenFactory A CSRF token factory.
	 *
	 * @param array          $cookies         An arry of cookies from the client, typically a
	 *                                        copy of $_COOKIE. IGNORED IN THIS CLASS.
	 *
	 */
	public function __construct(SegmentFactory $segment_factory, CsrfTokenFactory $csrf_token_factory,
								array $cookies = array()
	)
	{
		$this->segment_factory = $segment_factory;
		$this->csrf_token_factory = $csrf_token_factory;
	}

	/**
	 * Returns a named segment
	 *
	 * @param string $name The name of the session segment
	 *
	 * @return Segment
	 *
	 */
	public function newSegment($name)
	{
		if (!isset($this->segments[$name]))
		{
			$this->segments[$name] = $this->segment_factory->newInstance($this, $name);
		}

		return $this->segments[$name];
	}

	/**
	 *
	 * Tells us if a session is available to be reactivated, but not if it has
	 * started yet.
	 *
	 * @return bool
	 *
	 */
	public function isAvailable()
	{
		return true;
	}

	/**
	 *
	 * Tells us if a session has started.
	 *
	 * @return bool
	 *
	 */
	public function isStarted()
	{
		return \JFactory::getSession()->isActive();
	}

	/**
	 *
	 * Starts a new session, or resumes an existing one.
	 *
	 * @return bool
	 *
	 */
	public function start()
	{
        // Start the session only if we're not in CLI
        if(!Helper::isCli())
        {
            \JFactory::getSession()->start();
        }

		return true;
	}

	/**
	 * Clears all session variables across all segments. NOTE: The Segments must be created through the newSegment
	 * method of this class.
	 *
	 * @return null
	 *
	 */
	public function clear()
	{
		if (empty($this->segments))
		{
			return;
		}

		/**
		 * @var string $name
		 * @var Segment $segment
		 */
		foreach ($this->segments as $name => $segment)
		{
			$segment->clear();
		}

		return;
	}

	/**
	 * Writes session data from all segments and ends the session.
	 *
	 * This is ignored in Joomla!. You need to close the application to close the session.
	 *
	 * @return null
	 *
	 */
	public function commit()
	{
		return;
	}

	/**
	 * Destroys the session entirely.
	 *
	 * This is ignored in Joomla!
	 *
	 * @return bool
	 *
	 */
	public function destroy()
	{
		return false;
	}

	/**
	 *
	 * Returns the CSRF token, creating it if needed (and thereby starting a
	 * session).
	 *
	 * @return CsrfToken
	 *
	 */
	public function getCsrfToken()
	{
		if (!$this->csrf_token)
		{
			$this->csrf_token = $this->csrf_token_factory->newInstance($this);
		}

		return $this->csrf_token;
	}

	// =======================================================================
	//
	// support and admin methods
	//

	/**
	 *
	 * Sets the session cache expire time. You can't set it in Joomla!, but you'll get the expiration time alright.
	 *
	 * @param int $expire The expiration time in seconds.
	 *
	 * @return int
	 *
	 * @see session_cache_expire()
	 *
	 */
	public function setCacheExpire($expire)
	{
		return \JFactory::getSession()->getExpire();
	}

	/**
	 *
	 * Gets the session cache expire time.
	 *
	 * @return int The cache expiration time in seconds.
	 *
	 * @see session_cache_expire()
	 *
	 */
	public function getCacheExpire()
	{
		return \JFactory::getSession()->getExpire();
	}

	/**
	 *
	 * Sets the session cache limiter value.
	 *
	 * @param string $limiter The limiter value.
	 *
	 * @return string
	 *
	 * @see session_cache_limiter()
	 *
	 */
	public function setCacheLimiter($limiter)
	{
		return 'none';
	}

	/**
	 *
	 * Gets the session cache limiter value.
	 *
	 * @return string The limiter value.
	 *
	 * @see session_cache_limiter()
	 *
	 */
	public function getCacheLimiter()
	{
		return 'none';
	}

	/**
	 *
	 * Sets the session cookie params.  Param array keys are:
	 *
	 * - `lifetime` : Lifetime of the session cookie, defined in seconds.
	 *
	 * - `path` : Path on the domain where the cookie will work.
	 *   Use a single slash ('/') for all paths on the domain.
	 *
	 * - `domain` : Cookie domain, for example 'www.php.net'.
	 *   To make cookies visible on all subdomains then the domain must be
	 *   prefixed with a dot like '.php.net'.
	 *
	 * - `secure` : If TRUE cookie will only be sent over secure connections.
	 *
	 * - `httponly` : If set to TRUE then PHP will attempt to send the httponly
	 *   flag when setting the session cookie.
	 *
	 * @param array $params The array of session cookie param keys and values.
	 *
	 * @return void
	 *
	 * @see session_set_cookie_params()
	 *
	 */
	public function setCookieParams(array $params)
	{
		// This is ignored in Joomla!
	}

	/**
	 *
	 * Gets the session cookie params.
	 *
	 * @return array
	 *
	 */
	public function getCookieParams()
	{
		// This is ignored in Joomla!
		return array();
	}

	/**
	 *
	 * Gets the current session id.
	 *
	 * @return string
	 *
	 */
	public function getId()
	{
		return \JFactory::getSession()->getId();
	}

	/**
	 *
	 * Regenerates and replaces the current session id; also regenerates the
	 * CSRF token value if one exists.
	 *
	 * @return bool True is regeneration worked, false if not.
	 *
	 */
	public function regenerateId()
	{
		return \JFactory::getSession()->fork();
	}

	/**
	 *
	 * Sets the current session name.
	 *
	 * @param string $name The session name to use.
	 *
	 * @return string
	 *
	 * @see session_name()
	 *
	 */
	public function setName($name)
	{
		return false;
	}

	/**
	 *
	 * Returns the current session name.
	 *
	 * @return string
	 *
	 */
	public function getName()
	{
		return \JFactory::getSession()->getName();
	}

	/**
	 *
	 * Sets the session save path.
	 *
	 * @param string $path The new save path.
	 *
	 * @return string
	 *
	 * @see session_save_path()
	 *
	 */
	public function setSavePath($path)
	{
		// This is ignored in Joomla! :(

		return '';
	}

	/**
	 *
	 * Gets the session save path.
	 *
	 * @return string
	 *
	 * @see session_save_path()
	 *
	 */
	public function getSavePath()
	{
		// This is ignored in Joomla! :(

		return '';
	}

	/**
	 *
	 * Returns the current session status:
	 *
	 * - `PHP_SESSION_DISABLED` if sessions are disabled.
	 * - `PHP_SESSION_NONE` if sessions are enabled, but none exists.
	 * - `PHP_SESSION_ACTIVE` if sessions are enabled, and one exists.
	 *
	 * @return int
	 *
	 * @see session_status()
	 *
	 */
	public function getStatus()
	{
		if (\JFactory::getSession()->isActive())
		{
			return PHP_SESSION_ACTIVE;
		}

		return PHP_SESSION_NONE;
	}
}
