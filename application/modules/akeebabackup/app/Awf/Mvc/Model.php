<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Mvc;

use Awf\Application\Application;
use Awf\Container\Container;
use Awf\Inflector\Inflector;
use Awf\Input\Filter;
use Awf\Input\Input;
use Awf\Registry\Registry;

/**
 * Class Model
 *
 * A generic MVC model implementation
 *
 * @package Awf\Mvc
 */
class Model
{
	/**
	 * Input variables, passed on from the controller, in an associative array
	 *
	 * @var   array
	 */
	protected $input = array();

	/**
	 * Should I save the model's state in the session?
	 *
	 * @var   boolean
	 */
	protected $_savestate = true;

	/**
	 * Should we ignore request data when trying to get state data not already set in the Model?
	 *
	 * @var bool
	 */
	protected $_ignoreRequest = false;

	/**
	 * The model (base) name
	 *
	 * @var    string
	 */
	protected $name;

	/**
	 * A state object
	 *
	 * @var    string
	 */
	protected $state;

	/**
	 * Are the state variables already set?
	 *
	 * @var   boolean
	 */
	protected $_state_set = false;

	/**
	 * The container attached to the model
	 *
	 * @var \Awf\Container\Container
	 */
	protected $container;

	/**
	 * A copy of the Model's configuration
	 *
	 * @var   array
	 */
	protected $config = array();


	/**
	 * Returns a new model object. Unless overridden by the $config array, it will
	 * try to automatically populate its state from the request variables.
	 *
	 * By default the new model instance is created with persistent state, unless
	 * you pass $config['modelTemporaryInstance'] = false
	 *
	 * @param   string    $appName   The application name
	 * @param   string    $modelName The model name
	 * @param   Container $container Configuration variables to the model
	 *
	 * @return  static
	 *
	 * @throws  \RuntimeException  If the Model is not found
	 */
	public static function getInstance($appName = null, $modelName = '', $container = null)
	{
		if (empty($appName) && !is_object($container))
		{
			$app = Application::getInstance();
			$appName = $app->getName();
			$container = $app->getContainer();
		}
		elseif (empty($appName) && is_object($container))
		{
			$appName = $container->application_name;
		}
		elseif (!empty($appName) && !is_object($container))
		{
			$container = Application::getInstance($appName)->getContainer();
		}

		$config = isset($container['mvc_config']) ? $container['mvc_config'] : array();

		if (empty($modelName))
		{
			$modelName = $container->input->getCmd('view', '');
		}

		// Try to load the Model class
		$classes = array(
			'\\' . ucfirst($appName) . '\\Model\\' . ucfirst($modelName),
			'\\' . ucfirst($appName) . '\\Model\\' . ucfirst(Inflector::pluralize($modelName)), // For data models
			'\\' . ucfirst($appName) . '\\Model\\DefaultModel',
		);

		foreach ($classes as $className)
		{
			if (class_exists($className))
			{
				break;
			}
		}

		if (!class_exists($className))
		{
			throw new \RuntimeException("Model not found (app : model) = $appName : $modelName");
		}

		/** @var Model $result */
		$result = new $className($container);

		if (array_key_exists('modelTemporaryInstance', $config) && $config['modelTemporaryInstance'])
		{
			$result = $result
				->getClone()
				->savestate(0);
		}

		if (array_key_exists('modelClearState', $config) && $config['modelClearState'])
		{
			$result->clearState();
		}

		if (array_key_exists('modelClearInput', $config) && $config['modelClearInput'])
		{
			$result->clearInput();
		}

		return $result;
	}

	/**
	 * Returns a new instance of a model, with the state reset to defaults
	 *
	 * @param   string    $appName   The application name
	 * @param   string    $modelName The model name
	 * @param   Container $container Configuration variables to the model
	 *
	 * @return  static
	 *
	 * @throws  \RuntimeException  If the Model is not found
	 */
	public static function getTmpInstance($appName = '', $modelName = '', $container = null)
	{
		if (empty($appName) && !is_object($container))
		{
			$app = Application::getInstance();
			$appName = $app->getName();
			$container = $app->getContainer();
		}
		elseif (empty($appName) && is_object($container))
		{
			$appName = $container->application_name;
		}
		elseif (!empty($appName) && !is_object($container))
		{
			$container = Application::getInstance($appName)->getContainer();
		}

		$config = isset($container['mvc_config']) ? $container['mvc_config'] : array();

		$config['modelTemporaryInstance'] = true;
		$config['modelClearState'] = true;
		$config['modelClearInput'] = true;

		$container['mvc_config'] = $config;

		$ret = static::getInstance($appName, $modelName, $container);

		return $ret;
	}

	/**
	 * Public class constructor
	 *
	 * You can use the $container['mvc_config'] array to pass some configuration values to the object:
	 * state			stdClass|array. The state variables of the Model.
	 * use_populate		Boolean. When true the model will set its state from populateState() instead of the request.
	 * ignore_request	Boolean. When true getState will now automatically load state data from the request.
	 *
	 * @param   Container $container The configuration variables to this model
	 */
	public function __construct(\Awf\Container\Container $container = null)
	{
		if (!is_object($container))
		{
			$container = Application::getInstance()->getContainer();
		}

		$this->input = $container->input;

		$this->container = $container;

		$this->config = isset($container['mvc_config']) ? $container['mvc_config'] : array();

		// Set the model's name
		$this->name = $this->getName();

		// Set the model state
		if (array_key_exists('state', $this->config))
		{
			if (is_object($this->config['state']))
			{
				$this->state = $this->config['state'];
			}
			elseif (is_array($this->config['state']))
			{
				$this->state = (object)$this->config['state'];
			}
			// Protect vs malformed state
			else
			{
				$this->state = new \stdClass();
			}
		}
		else
		{
			$this->state = new \stdClass();
		}

		// Set the internal state marker
		if (!empty($this->config['use_populate']))
		{
			$this->_state_set = true;
		}

		// Set the internal state marker
		if (!empty($this->config['ignore_request']))
		{
			$this->_ignoreRequest = true;
		}
	}

	/**
	 * Method to get the model name
	 *
	 * The model name. By default parsed using the classname or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the model
	 *
	 * @throws  \RuntimeException  If it's impossible to get the name
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$r = null;

			if (!preg_match('/(.*)\\\\Model\\\\(.*)/i', get_class($this), $r))
			{
				throw new \RuntimeException(\Awf\Text\Text::_('AWF_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}

			$this->name = strtolower($r[2]);
		}

		return $this->name;
	}

	/**
	 * Get a filtered state variable
	 *
	 * @param   string $key         The state variable's name
	 * @param   mixed  $default     The default value to return if it's not already set
	 * @param   string $filter_type The filter type to use
	 *
	 * @return  mixed  The state variable's contents
	 */
	public function getState($key = null, $default = null, $filter_type = 'raw')
	{
		if (empty($key))
		{
			return $this->internal_getState();
		}

		// Get the savestate status
		$value = $this->internal_getState($key);

		if (is_null($value) && !$this->_ignoreRequest)
		{
			$value = $this->getUserStateFromRequest($key, $key, $value, 'none', $this->_savestate);
			if (is_null($value))
			{
				return $default;
			}
		}

		if (strtoupper($filter_type) == 'RAW')
		{
			return $value;
		}
		else
		{
			$filter = new Filter();

			return $filter->clean($value, $filter_type);
		}
	}

	/**
	 * Returns a unique hash for each view, used to prefix the state variables
	 * to allow us to retrieve them from the state later on.
	 *
	 * @return  string
	 */
	public function getHash()
	{
		static $hash = null;

		if (is_null($hash))
		{
			$hash = ucfirst($this->container->application->getName()) . '.' . $this->getName() . '.';
		}

		return $hash;
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 * @param    string  $key          The key of the user state variable.
	 * @param    string  $request      The name of the variable passed in a request.
	 * @param    string  $default      The default value for the variable if not found. Optional.
	 * @param    string  $type         Filter for the variable, for valid values see {@link Filter::clean()}. Optional.
	 * @param    boolean $setUserState Should I save the variable in the user state? Default: true. Optional.
	 *
	 * @return   mixed The request user state.
	 */
	protected function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $setUserState = true)
	{
		$session = $this->container->segment;
		$hash = $this->getHash();

		$old_state = $session->{$hash . $key};
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = $this->input->get($request, null, $type);

		// Save the new value only if it was set in this request
		if ($setUserState)
		{
			if ($new_state !== null)
			{
				$session->{$hash . $key} = $new_state;
			}
			else
			{
				$new_state = $cur_state;
			}
		}
		elseif (is_null($new_state))
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * Method to get model state variables
	 *
	 * @param   string $property Optional parameter name
	 * @param   mixed  $default  Optional default value
	 *
	 * @return  object  The property where specified, the state object where omitted
	 */
	private function internal_getState($property = null, $default = null)
	{
		if (!$this->_state_set)
		{
			// Protected method to auto-populate the model state.
			$this->populateState();

			// Set the model state set flag to true.
			$this->_state_set = true;
		}

		if (is_null($property))
		{
			return $this->state;
		}
		else
		{
			if (property_exists($this->state, $property))
			{
				return $this->state->$property;
			}
			else
			{
				return $default;
			}
		}
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 */
	protected function populateState()
	{
	}

	/**
	 * Method to set model state variables
	 *
	 * @param   string $property The name of the property.
	 * @param   mixed  $value    The value of the property to set or null.
	 *
	 * @return  mixed  The previous value of the property or null if not set.
	 */
	public function setState($property, $value = null)
	{
		if (is_null($this->state))
		{
			$this->state = new \stdClass();
		}

		return $this->state->$property = $value;
	}

	/**
	 * Clears the model state, but doesn't touch the internal lists of records,
	 * record tables or record id variables. To clear these values, please use
	 * reset().
	 *
	 * @return  static
	 */
	public function clearState()
	{
		$this->state = new \stdClass();

		return $this;
	}

	/**
	 * Clears the input array.
	 *
	 * @return  static
	 */
	public function clearInput()
	{
		$this->input = new Input(array());

		return $this;
	}

	/**
	 * Clones the model object and returns the clone
	 *
	 * @return  $this for chaining
	 */
	public function getClone()
	{
		$clone = clone($this);

		return $clone;
	}

	/**
	 * Magic getter; allows to use the name of model state keys as properties
	 *
	 * @param   string $name The state variable key
	 *
	 * @return  static
	 */
	public function __get($name)
	{
		return $this->getState($name);
	}

	/**
	 * Magic setter; allows to use the name of model state keys as properties
	 *
	 * @param   string $name  The state variable key
	 * @param   mixed  $value The state variable value
	 *
	 * @return  static
	 */
	public function __set($name, $value)
	{
		return $this->setState($name, $value);
	}

	/**
	 * Magic caller; allows to use the name of model state keys as methods to
	 * set their values.
	 *
	 * @param   string $name      The state variable key
	 * @param   mixed  $arguments The state variable contents
	 *
	 * @return  static
	 */
	public function __call($name, $arguments)
	{
		$arg1 = array_shift($arguments);
		$this->setState($name, $arg1);

		return $this;
	}

	/**
	 * Sets the model state auto-save status. By default the model is set up to
	 * save its state to the session.
	 *
	 * @param  boolean $newState True to save the state, false to not save it.
	 *
	 * @return  static
	 */
	public function savestate($newState)
	{
		$this->_savestate = $newState ? true : false;

		return $this;
	}

	/**
	 * Public setter for the _savestate variable. Set it to true to save the state
	 * of the Model in the session.
	 *
	 * @return  static
	 */
	public function populateSavestate()
	{
		if (is_null($this->_savestate))
		{
			$savestate = $this->input->getInt('savestate', -999);
			if ($savestate == -999)
			{
				$savestate = true;
			}
			$this->savestate($savestate);
		}
	}

	/**
	 * Sets the ignore request flag. When false, getState() will try to populate state variables not already set from
	 * same-named state variables in the request.
	 *
	 * @param boolean $ignoreRequest
	 *
	 * @return  $this  for chaining
	 */
	public function setIgnoreRequest($ignoreRequest)
	{
		$this->_ignoreRequest = $ignoreRequest;

		return $this;
	}

	/**
	 * Gets the ignore request flag. When false, getState() will try to populate state variables not already set from
	 * same-named state variables in the request.
	 *
	 * @return boolean
	 */
	public function getIgnoreRequest()
	{
		return $this->_ignoreRequest;
	}
}