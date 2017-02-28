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
 * Cross-site request forgery token tools.
 */
class CsrfToken
{
	/**
	 *
	 * A cryptographically-secure random value generator.
	 *
	 * @var RandvalInterface
	 *
	 */
	protected $randval;

	/**
	 *
	 * Session segment for values in this class.
	 *
	 * @var Segment
	 *
	 */
	protected $segment;

	/**
	 *
	 * Constructor.
	 *
	 * @param Segment          $segment A segment for values in this class.
	 *
	 * @param RandvalInterface $randval A cryptographically-secure random
	 *                                  value generator.
	 *
	 */
	public function __construct(Segment $segment, RandvalInterface $randval)
	{
		$this->segment = $segment;
		$this->randval = $randval;
		if (!isset($this->segment->value))
		{
			$this->regenerateValue();
		}
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
		return $value === $this->getValue();
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
		return $this->segment->value;
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
		$this->segment->value = hash('sha512', $this->randval->generate());
	}
}
