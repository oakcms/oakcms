<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * The Session package in Awf is based on the Session package in Aura for PHP. Please consult the LICENSE file in the
 * Awf\Session package for copyright and license information.
 */

namespace Awf\Utils;

/**
 * Intercept calls to PHP functions.
 */
class Phpfunc
{
	/**
	 *
	 * Magic call to intercept any function pass to it.
	 *
	 * @param string $func The function to call.
	 *
	 * @param array  $args Arguments passed to the function.
	 *
	 * @return mixed The result of the function call.
	 *
	 */
	public function __call($func, $args)
	{
		return call_user_func_array($func, $args);
	}
}
