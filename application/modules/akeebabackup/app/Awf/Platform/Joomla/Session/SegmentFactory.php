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

use Awf\Session\Manager as SessionManager;

/**
 * A factory to create session segment objects.
 */
class SegmentFactory extends \Awf\Session\SegmentFactory
{
	/**
	 *
	 * Creates a session segment object.
	 *
	 * @param SessionManager $manager
	 * @param string  $name
	 *
	 * @return Segment
	 */
	public function newInstance(SessionManager $manager, $name)
	{
		return new Segment($manager, $name);
	}
}
