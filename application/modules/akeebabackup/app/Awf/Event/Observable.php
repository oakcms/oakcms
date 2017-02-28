<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Event;

/**
 * Interface Observable
 *
 * @package Awf\Event
 *
 * @codeCoverageIgnore
 */
interface Observable
{
	/**
	 * Attaches an observer to the object
	 *
	 * @param   Observer  $observer  The observer to attach
	 *
	 * @return  Observable  Ourselves, for chaining
	 */
	public function attach(Observer $observer);

	/**
	 * Detaches an observer from the object
	 *
	 * @param   Observer  $observer  The observer to detach
	 *
	 * @return  Observable  Ourselves, for chaining
	 */
	public function detach(Observer $observer);

	/**
	 * Triggers an event in the attached observers
	 *
	 * @param   string  $event  The event to attach
	 * @param   array   $args   Arguments to the event handler
	 *
	 * @return  array
	 */
	public function trigger($event, array $args = array());
} 