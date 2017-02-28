<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * This class is adapted from the Joomla! Framework
 */

namespace Awf\Registry;

/**
 * Abstract Format for Registry
 */
abstract class AbstractRegistryFormat
{
	/**
	 * @var    array  Format instances container.
	 */
	protected static $instances = array();

	/**
	 * Returns a reference to a Format object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $type     The format to load
	 * @param   array   $options  Additional options to configure the object
	 *
	 * @return  AbstractRegistryFormat  Registry format handler
	 *
	 * @throws  \InvalidArgumentException
	 */
	public static function getInstance($type, array $options = array())
	{
		// Sanitize format type.
		$type = strtolower(preg_replace('/[^A-Z0-9_]/i', '', $type));

		// Only instantiate the object if it doesn't already exist.
		if (!isset(self::$instances[$type]))
		{
			$localNamespace = __NAMESPACE__ . '\\Format';
			$namespace      = isset($options['format_namespace']) ? $options['format_namespace'] : $localNamespace;
			$class          = $namespace . '\\' . ucfirst($type);

			if (!class_exists($class))
			{
				// Were we given a custom namespace?  If not, there's nothing else we can do
				if ($namespace === $localNamespace)
				{
					throw new \InvalidArgumentException(sprintf('Unable to load format class for type "%s".', $type), 500);
				}

				$class = $localNamespace . '\\' . ucfirst($type);

				if (!class_exists($class))
				{
					throw new \InvalidArgumentException(sprintf('Unable to load format class for type "%s".', $type), 500);
				}
			}

			self::$instances[$type] = new $class;
		}

		return self::$instances[$type];
	}

	/**
	 * Converts an object into a formatted string.
	 *
	 * @param   object  $object   Data Source Object.
	 * @param   array   $options  An array of options for the formatter.
	 *
	 * @return  string  Formatted string.
	 */
	abstract public function objectToString($object, $options = null);

	/**
	 * Converts a formatted string into an object.
	 *
	 * @param   string  $data     Formatted string
	 * @param   array   $options  An array of options for the formatter.
	 *
	 * @return  object  Data Object
	 */
	abstract public function stringToObject($data, array $options = array());
}
