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
 *
 * A factory to create CSRF token objects.
 */
class CsrfTokenFactory
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
	 * Constructor.
	 *
	 * @param RandvalInterface $randval A cryptographically-secure random
	 *                                  value generator.
	 *
	 */
	public function __construct(RandvalInterface $randval)
	{
		$this->randval = $randval;
	}

	/**
	 *
	 * Creates a CsrfToken object.
	 *
	 * @param Manager $manager The session manager.
	 *
	 * @return CsrfToken
	 *
	 */
	public function newInstance(Manager $manager)
	{
		$segment = $manager->newSegment('Awf\Session\CsrfToken');

		return new CsrfToken($segment, $this->randval);
	}
}
