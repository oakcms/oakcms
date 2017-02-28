<?php
/**
 * @package        awf
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\Application\Observer;

use Awf\Event\Dispatcher;
use Awf\Event\Observer;
use Awf\Inflector\Inflector;
use Awf\Mvc\Controller;
use Awf\Mvc\DataController;
use Awf\Mvc\DataModel;
use Awf\Platform\Joomla\Helper\Helper;

/**
 * A Joomla!-specific observer which applies ACL (access control) to the default tasks of DataController.
 *
 * @package Awf\Platform\Joomla\Application\Observer
 */
class ControllerAcl extends Observer
{

	/** @var   Dispatcher  The object to observe */
	protected $subject = null;

	/**
	 * Controller event handler for the ACL checks in the read task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeRead($controllerName)
	{
		return true;
	}

	/**
	 * Controller event handler for the ACL checks in the add task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeAdd($controllerName)
	{
		return $this->checkACL($controllerName, 'core.create');
	}

	/**
	 * Controller event handler for the ACL checks in the edit task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeEdit($controllerName)
	{
		return $this->checkACL($controllerName, 'core.edit');
	}

	/**
	 * Controller event handler for the ACL checks in the apply task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeApply($controllerName)
	{
		// Get the controller, model and table
		$container = $this->subject->getContainer();
		$application_name = $container->application_name;
		$component_name = 'com_' . strtolower($application_name);
		/** @var DataController $controller */
		$controller = Controller::getInstance($application_name, $controllerName, $container);
		$model = $controller->getModel();

		$controller->getIDsFromRequest($model);
		$id = $model->getId();

		if(!$id)
		{
			$defaultPrivilege = 'core.create';
		}
		else
		{
			$defaultPrivilege = 'core.edit';
		}

		return $this->checkACL($controllerName, $defaultPrivilege);
	}

	/**
	 * Controller event handler for the ACL checks in the copy task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeCopy($controllerName)
	{
		return $this->onControllerBeforeApply($controllerName);
	}

	/**
	 * Controller event handler for the ACL checks in the save task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeSave($controllerName)
	{
		return $this->onControllerBeforeApply($controllerName);
	}

	/**
	 * Controller event handler for the ACL checks in the savenew task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeSavenew($controllerName)
	{
		return $this->onControllerBeforeApply($controllerName) && $this->onControllerBeforeAdd($controllerName);
	}

	/**
	 * Controller event handler for the ACL checks in the cancel task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeCancel($controllerName)
	{
		return $this->onControllerBeforeApply($controllerName);
	}

	/**
	 * Controller event handler for the ACL checks in the publish task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforePublish($controllerName)
	{
		return $this->checkACL($controllerName, 'core.edit.state');
	}

	/**
	 * Controller event handler for the ACL checks in the unpublish task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeUnpublish($controllerName)
	{
		return $this->checkACL($controllerName, 'core.edit.state');
	}

	/**
	 * Controller event handler for the ACL checks in the archive task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeArchive($controllerName)
	{
		return $this->checkACL($controllerName, 'core.edit.state');
	}

	/**
	 * Controller event handler for the ACL checks in the trash task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeTrash($controllerName)
	{
		return $this->checkACL($controllerName, 'core.edit.state');
	}

	/**
	 * Controller event handler for the ACL checks in the saveorder task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeSaveorder($controllerName)
	{
		return $this->checkACL($controllerName, 'core.edit.state');
	}

	/**
	 * Controller event handler for the ACL checks in the orderdown task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeOrderdown($controllerName)
	{
		return $this->checkACL($controllerName, 'core.edit.state');
	}

	/**
	 * Controller event handler for the ACL checks in the orderup task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeOrderup($controllerName)
	{
		return $this->checkACL($controllerName, 'core.edit.state');
	}

	/**
	 * Controller event handler for the ACL checks in the remove task
	 *
	 * @param string $controllerName The name of the controller firing the event
	 *
	 * @return bool True to allow the task to execute
	 */
	public function onControllerBeforeRemove($controllerName)
	{
		return $this->checkACL($controllerName, 'core.delete');
	}

	/**
	 * Checks if the current user has enough privileges for the requested ACL
	 * area.
	 *
	 * @param string $area The ACL area, e.g. core.manage.
	 *
	 * @return bool True if the user has the ACL privilege specified
	 */
	protected function checkACL($controller_name, $area)
	{
		// If the area is one of false, 0, no or 403 we cancel the action
		if (in_array(strtolower($area), array('false', '0', 'no', '403')))
		{
			return false;
		}

		// If the area is one of true, 1, yes we proceed with the action
		if (in_array(strtolower($area), array('true', '1', 'yes')))
		{
			return true;
		}

		// If no ACL area is specified we proceed with the action
		if (empty($area))
		{
			return true;
		}

		// Get the controller, model and table
		$container = $this->subject->getContainer();
		$application_name = $container->application_name;
		$component_name = 'com_' . strtolower($application_name);
		/** @var DataController $controller */
		$controller = Controller::getInstance($application_name, $controller_name, $container);
		$model = $controller->getModel();

		// If it's not a data model or it's not assets tracked just perform a regular ACL check on the component
		if (!($model instanceof DataModel) || !$model->getState('_isAssetsTracked', false))
		{
			return Helper::authorise($area, $component_name);
		}

		// Initialise
		$ids = null;

		// Get the IDs in the request
		$ids = $controller->getIDsFromRequest($model, false);

		// If there are no IDs there is no asset tracking, fall back to the generic ACL check
		if (empty($ids))
		{
			return Helper::authorise($area, $component_name);
		}

		// This should never happen unless you screw up overriding getIDsFromRequest in your model...
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Check the asset permissions of each record
		$resource = Inflector::singularize($controller_name);
		$isEditState = ($area == 'core.edit.state');

		foreach ($ids as $id)
		{
			$asset = $component_name . '.' . $resource . '.' . $id;

			// Dedicated permission found, check it!
			if (Helper::authorise($area, $asset))
			{
				return true;
			}

			// No dedicated permissions. Fallback on edit.own.
			if ((!$isEditState) && (Helper::authorise('core.edit.own', $asset)) && $model->hasField('created_by'))
			{
				// Load the record
				$model->find(array('id' => $id));

				// Make sure the record can be loaded
				if ($model->getId())
				{
					// Now test the owner is the user.
					$owner_id = (int)$model->created_by;

					// If I am the owner the test is successful
					if ($owner_id == \JFactory::getUser()->id)
					{
						return true;
					}
				}
			}
		}

		return false;
	}
} 