<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Hal\Render;

/**
 * Interface for HAL document renderers
 *
 * @see http://stateless.co/hal_specification.html
 *
 * @codeCoverageIgnore
 */
interface RenderInterface
{
	/**
	 * Render a HAL document into a representation suitable for consumption.
	 *
	 * @param   array  $options  Renderer-specific options
	 *
	 * @return  string
	 */
	public function render($options = array());
}
