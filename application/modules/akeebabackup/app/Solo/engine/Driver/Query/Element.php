<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Driver\Query;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Query Element Class.
 *
 * Based on Joomla! Platform 11.3
 */
class Element
{
	/**
	 * @var    string  The name of the element.
	 */
	protected $name = null;

	/**
	 * @var    array  An array of elements.
	 */
	protected $elements = null;

	/**
	 * @var    string  Glue piece.
	 */
	protected $glue = null;

	/**
	 * Constructor.
	 *
	 * @param   string $name     The name of the element.
	 * @param   mixed  $elements String or array.
	 * @param   string $glue     The glue for elements.
	 */
	public function __construct($name, $elements, $glue = ',')
	{
		$this->elements = array();
		$this->name = $name;
		$this->glue = $glue;

		$this->append($elements);
	}

	/**
	 * Magic function to convert the query element to a string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		if (substr($this->name, -2) == '()')
		{
			return PHP_EOL . substr($this->name, 0, -2) . '(' . implode($this->glue, $this->elements) . ')';
		}
		else
		{
			return PHP_EOL . $this->name . ' ' . implode($this->glue, $this->elements);
		}
	}

	/**
	 * Appends element parts to the internal list.
	 *
	 * @param   mixed $elements String or array.
	 *
	 * @return  void
	 */
	public function append($elements)
	{
		if (is_array($elements))
		{
			$this->elements = array_merge($this->elements, $elements);
		}
		else
		{
			$this->elements = array_merge($this->elements, array($elements));
		}
	}

	/**
	 * Gets the elements of this element.
	 *
	 * @return  string
	 */
	public function getElements()
	{
		return $this->elements;
	}

	/**
	 * Method to provide deep copy support to nested objects and arrays
	 * when cloning.
	 *
	 * @return  void
	 */
	public function __clone()
	{
		foreach ($this as $k => $v)
		{
			if (is_object($v) || is_array($v))
			{
				$this->{$k} = unserialize(serialize($v));
			}
		}
	}
}