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

/**
 * Cross-site request forgery token tools
 */
class CsrfToken extends \Awf\Session\CsrfToken
{
	public function __construct()
	{
	}

	/**
	 *
	 * Checks whether an incoming CSRF token value is valid.
	 *
	 * @param string $value The incoming token value.
	 *
	 * @return bool True if valid, false if not.
	 *
	 */
	public function isValid($value)
	{
		$token = \JFactory::getSession()->getToken();
		$formToken = \JFactory::getSession()->getFormToken();

		return ($value == $token) || ($value == $formToken);
	}

	/**
	 *
	 * Gets the value of the outgoing CSRF token.
	 *
	 * @return string
	 *
	 */
	public function getValue()
	{
		return \JFactory::getSession()->getFormToken();
	}

	/**
	 *
	 * Regenerates the value of the outgoing CSRF token.
	 *
	 * @return void
	 *
	 */
	public function regenerateValue()
	{
		\JFactory::getSession()->getFormToken(true);
		\JFactory::getSession()->getToken(true);
	}
}
