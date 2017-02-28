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

/**
 * An interface for session segment objects.
 *
 * @codeCoverageIgnore
 */
interface SegmentInterface
{
	/**
	 *
	 * Returns the value of a key in the segment.
	 *
	 * @param string $key The key in the segment.
	 *
	 * @return mixed
	 *
	 */
	public function __get($key);

	/**
	 *
	 * Sets the value of a key in the segment.
	 *
	 * @param string $key The key to set.
	 *
	 * @param mixed  $val The value to set it to.
	 *
	 */
	public function __set($key, $val);

	/**
	 *
	 * Check whether a key is set in the segment.
	 *
	 * @param string $key The key to check.
	 *
	 * @return bool
	 *
	 */
	public function __isset($key);

	/**
	 *
	 * Unsets a key in the segment.
	 *
	 * @param string $key The key to unset.
	 *
	 * @return void
	 *
	 */
	public function __unset($key);

	/**
	 *
	 * Clear all data from the segment.
	 *
	 * @return void
	 *
	 */
	public function clear();

	/**
	 *
	 * Gets the segment name.
	 *
	 * @return string
	 *
	 */
	public function getName();

	/**
	 *
	 * Sets a read-once flash value on the segment.
	 *
	 * @param string $key The key for the flash value.
	 *
	 * @param mixed  $val The flash value itself.
	 *
	 */
	public function setFlash($key, $val);

	/**
	 *
	 * Reads the flash value for a key, thereby removing it from the session.
	 *
	 * @param string $key The key for the flash value.
	 *
	 * @return mixed The flash value itself.
	 *
	 */
	public function getFlash($key);

	/**
	 *
	 * Checks whether a flash key is set, without reading it.
	 *
	 * @param string $key The flash key to check.
	 *
	 * @return bool True if it is set, false if not.
	 *
	 */
	public function hasFlash($key);

	/**
	 *
	 * Clears all flash values.
	 *
	 * @return void
	 *
	 */
	public function clearFlash();

	/**
	 * Commit the session data to PHP's session storage using a safely encoded format to prevent PHP session
	 * unserialization attacks.
	 *
	 * @return  void
	 */
	public function save();
}
