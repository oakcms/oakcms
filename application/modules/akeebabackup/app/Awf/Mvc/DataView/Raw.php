<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Mvc\DataView;

use Awf\Mvc\DataModel;
use Awf\Mvc\View;
use Awf\Pagination\Pagination;

/**
 * View for a raw data-driven view
 *
 * @property-read \Awf\Mvc\DataModel\Collection $items      The records loaded
 * @property-read int                           $itemsCount The total number of items in the model (more than those loaded)
 * @property-read \Awf\Pagination\Pagination    Pagination  object
 *
 * @package Awf\Mvc\DataView
 */
class Raw extends View
{
	/** @var   array  Data lists */
	protected $lists = null;

	/**
	 * Executes before rendering the page for the Browse task.
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	public function onBeforeBrowse()
	{
		// Create the lists object
		$this->lists = new \stdClass();

		// Load the model
		/** @var \Awf\Mvc\DataModel $model */
		$model = $this->getModel();

		// We want to persist the state in the session
		$model->savestate(1);

		// Ordering information
		$this->lists->order		 = $model->getState('filter_order', $model->getIdFieldName(), 'cmd');
		$this->lists->order_Dir	 = $model->getState('filter_order_Dir', 'DESC', 'cmd');

		// Display limits
		$this->lists->limitStart = $model->getState('limitstart', 0, 'int');
		$this->lists->limit      = $model->getState('limit', 0, 'int');

		// Assign items to the view
		$this->items      = $model->get();
		$this->itemsCount = $model->count();

		// Pagination
		$displayedLinks = 10;
		$this->pagination = new Pagination($this->itemsCount, $this->lists->limitStart, $this->lists->limit, $displayedLinks, $this->container->application);

		return true;
	}
} 