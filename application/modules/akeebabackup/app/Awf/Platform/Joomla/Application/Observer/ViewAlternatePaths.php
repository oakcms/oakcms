<?php
/**
 * @package        awf
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\Application\Observer;

use Awf\Event\Observer;
use Awf\Event\Dispatcher;
use Awf\Inflector\Inflector;
use Awf\Platform\Joomla\Helper\Helper;

/**
 * A Joomla!-specific observer adding the alternate paths for template overrides
 *
 * @package Awf\Platform\Joomla\Application\Observer
 */
class ViewAlternatePaths extends Observer
{

	/** @var   Dispatcher  The object to observe */
	protected $subject = null;

	/**
	 * Returns the alternate view template paths (a.k.a. template overrides in Joomla!-speak) for the given View name
	 *
	 * @param string $viewName The name of the view triggering this event
	 *
	 * @return array The template override paths
	 */
	public function onGetViewTemplatePaths($viewName)
	{
		$container = $this->subject->getContainer();
		$application_name = $container->application_name;
		$component_name = 'com_' . strtolower($application_name);

		// That's the path in the site's current template containing template overrides
		$overridePath = Helper::getTemplateOverridePath($component_name, true);

		// The alternative view name (pluralised if the view is singular, singularised if the view is plural)
		$altViewName = Inflector::isPlural($viewName) ? Inflector::singularize($viewName) : Inflector::pluralize($viewName);

		// Remember, each path is pushed to the TOP of the path stack. This means that the least important directory
		// must go FIRST so that it ends add being added LAST.
		return array(
			$overridePath . '/' . $altViewName,
			$overridePath . '/' . $viewName,
		);
	}
} 