<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * This class is based on the JHtml package of Joomla! 3 but heavily modified
 */

namespace Awf\Html;

use Awf\Application\Application;
use Awf\Uri\Uri;
use Awf\Utils\Template;

/**
 * Javascript behaviours abstraction class
 */
abstract class Behaviour
{
	/**
	 * Array containing information for loaded files
	 *
	 * @var    array
	 */
	protected static $loaded = array();

	/**
	 * Add unobtrusive JavaScript support for a calendar control.
	 *
	 * @param   Application  $app  CSS and JS will be added to the document of the selected application
	 *
	 * @return  void
	 */
	public static function calendar(Application $app = null)
	{
		// Only load once
		if (isset(static::$loaded[__METHOD__]))
		{
			return;
		}

		if (!is_object($app))
		{
			$app = Application::getInstance();
		}

		$document = $app->getDocument();

		Template::addJs('media://js/datepicker/bootstrap-datepicker.js');
		Template::addCss('media://css/datepicker.css');

		static::$loaded[__METHOD__] = true;
	}
} 