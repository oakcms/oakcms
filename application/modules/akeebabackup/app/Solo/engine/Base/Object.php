<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Base;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * The base class of Akeeba Engine objects. Allows for error and warnings logging
 * and propagation. Largely based on the Joomla! 1.5 JObject class.
 */
abstract class Object
{
	/** @var  array  An array of errors */
	private $_errors = array();

	/** @var  array  The queue size of the $_errors array. Set to 0 for infinite size. */
	protected $_errors_queue_size = 0;

	/** @var  array  An array of warnings */
	private $_warnings = array();

	/** @var  array  The queue size of the $_warnings array. Set to 0 for infinite size. */
	protected $_warnings_queue_size = 0;

	/**
	 * This method should be overridden by descendant classes. It is called when the factory is being
	 * serialized and can be used to perform any necessary cleanup steps.
	 *
	 * @return  void
	 */
	public function _onSerialize()
	{
	}

	/**
	 * Get the most recent error message
	 *
	 * @param   integer  $i  Optional error index
	 *
	 * @return  string  Error message
	 */
	public function getError($i = null)
	{
		return $this->getItemFromArray($this->_errors, $i);
	}

	/**
	 * Return all errors, if any
	 *
	 * @return  array  Array of error messages
	 */
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * Add an error message
	 *
	 * @param   string  $error  Error message
	 */
	public function setError($error)
	{
		if ($this->_errors_queue_size > 0)
		{
			if (count($this->_errors) >= $this->_errors_queue_size)
			{
				array_shift($this->_errors);
			}
		}

		array_push($this->_errors, $error);
	}

	/**
	 * Resets all error messages
	 *
	 * @return  void
	 */
	public function resetErrors()
	{
		$this->_errors = array();
	}

	/**
	 * Get the most recent warning message
	 *
	 * @param   integer  $i  Optional warning index
	 *
	 * @return  string  Error message
	 */
	public function getWarning($i = null)
	{
		return $this->getItemFromArray($this->_warnings, $i);
	}

	/**
	 * Return all warnings, if any
	 *
	 * @return  array  Array of error messages
	 */
	public function getWarnings()
	{
		return $this->_warnings;
	}

	/**
	 * Add a warning message
	 *
	 * @param   string  $error  Error message
	 *
	 * @return  void
	 */
	public function setWarning($warning)
	{
		if ($this->_warnings_queue_size > 0)
		{
			if (count($this->_warnings) >= $this->_warnings_queue_size)
			{
				array_shift($this->_warnings);
			}
		}

		array_push($this->_warnings, $warning);
	}

	/**
	 * Resets all warning messages
	 *
	 * @return  void
	 */
	public function resetWarnings()
	{
		$this->_warnings = array();
	}

	/**
	 * Propagates errors and warnings to a foreign object. Propagated items will be removed from our own instance.
	 *
	 * @param   Object  $object  The object to propagate errors and warnings to.
	 *
	 * @return  void
	 */
	public function propagateToObject(&$object)
	{
		// Skip non-objects
		if (!is_object($object))
		{
			return;
		}

		if (method_exists($object, 'setError'))
		{
			if (!empty($this->_errors))
			{
				foreach ($this->_errors as $error)
				{
					$object->setError($error);
				}

				$this->_errors = array();
			}
		}

		if (method_exists($object, 'setWarning'))
		{
			if (!empty($this->_warnings))
			{
				foreach ($this->_warnings as $warning)
				{
					$object->setWarning($warning);
				}

				$this->_warnings = array();
			}
		}
	}

	/**
	 * Propagates errors and warnings from a foreign object. Each propagated list is
	 * then cleared on the foreign object, as long as it implements resetErrors() and/or
	 * resetWarnings() methods.
	 *
	 * @param   Object  $object  The object to propagate errors and warnings from
	 *
	 * @return  void
	 */
	public function propagateFromObject(&$object)
	{
		if (method_exists($object, 'getErrors'))
		{
			$errors = $object->getErrors();

			if (!empty($errors))
			{
				foreach ($errors as $error)
				{
					$this->setError($error);
				}
			}

			if (method_exists($object, 'resetErrors'))
			{
				$object->resetErrors();
			}
		}

		if (method_exists($object, 'getWarnings'))
		{
			$warnings = $object->getWarnings();

			if (!empty($warnings))
			{
				foreach ($warnings as $warning)
				{
					$this->setWarning($warning);
				}
			}

			if (method_exists($object, 'resetWarnings'))
			{
				$object->resetWarnings();
			}
		}
	}

	/**
	 * Sets the size of the error queue (acts like a LIFO buffer)
	 *
	 * @param   int  $newSize  The new queue size. Set to 0 for infinite length.
	 *
	 * @return  void
	 */
	protected function setErrorsQueueSize($newSize = 0)
	{
		$this->_errors_queue_size = (int)$newSize;
	}

	/**
	 * Sets the size of the warnings queue (acts like a LIFO buffer)
	 *
	 * @param   int  $newSize  The new queue size. Set to 0 for infinite length.
	 *
	 * @return  void
	 */
	protected function setWarningsQueueSize($newSize = 0)
	{
		$this->_warnings_queue_size = (int)$newSize;
	}

	/**
	 * Returns the last item of a LIFO string message queue, or a specific item
	 * if so specified.
	 *
	 * @param   array  $array  An array of strings, holding messages
	 * @param   int    $i      Optional message index
	 *
	 * @return  mixed  The message string, or false if the key doesn't exist
	 */
	protected function getItemFromArray($array, $i = null)
	{
		// Find the item
		if ($i === null)
		{
			// Default, return the last item
			$item = end($array);
		}
		elseif (!array_key_exists($i, $array))
		{
			// If $i has been specified but does not exist, return false
			return false;
		}
		else
		{
			$item = $array[$i];
		}

		return $item;
	}
}