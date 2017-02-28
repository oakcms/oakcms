<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\Event;

use Awf\Platform\Joomla\Helper\Helper;

/**
 * A Joomla!-specific events dispatcher. It will trigger both Joomla! plugin events and AWF Observers for every event.
 * Joomla! plugins have precedence over AWF Observers.
 *
 * @package Awf\Platform\Joomla\Event
 */
class Dispatcher extends \Awf\Event\Dispatcher
{
	/**
	 * Triggers an event in the Joomla! plugins system and the attached observers
	 *
	 * @param   string  $event  The event to attach
	 * @param   array   $args   Arguments to the event handler
	 *
	 * @return  array
	 */
	public function trigger($event, array $args = array())
	{
		$resultsJoomla = Helper::runPlugins($event, $args);
		$resultsAwf = parent::trigger($event, $args);

		return array_merge($resultsJoomla, $resultsAwf);
	}

	/**
	 * Asks each observer to handle an event based on the provided arguments. The first observer to return a non-null
	 * result wins. This is a *very* simplistic implementation of the Chain of Command pattern.
	 *
	 * Please note that in this implementation the Joomla! plugins are queried first. If any of them handled the event
	 * no Observer will run. However, since Joomla! doesn't implement a chain handler, ALL Joomla! plugins handling
	 * $event will fire, even if the first plugin to fire did handle the event successfully (non-null result).
	 *
	 * @param   string  $event  The event name to handle
	 * @param   array   $args   The arguments to the event
	 *
	 * @return  mixed  Null if the event can't be handled by any observer
	 */
	public function chainHandle($event, $args = array())
	{
		// First try using Joomla! plugins
		$resultsJoomla = Helper::runPlugins($event, $args);

		if (!empty($resultsJoomla))
		{
			foreach ($resultsJoomla as $result)
			{
				if (!is_null($result))
				{
					return $result;
				}
			}
		}

		// Finally use the AWF Observers
		return parent::chainHandle($event, $args);
	}
} 