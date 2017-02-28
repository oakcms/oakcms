<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 *
 * Based on the Joomla! Platform and FOF
 */

namespace Awf\Input;

class Cli extends Input
{
	/**
	 * The executable that was called to run the CLI script.
	 *
	 * @var    string
	 */
	public $executable;

	/**
	 * The additional arguments passed to the script that are not associated
	 * with a specific argument name.
	 *
	 * @var    array
	 */
	public $args = array();

	/**
	 * Constructor.
	 *
	 * @param   array  $source   Source data (Optional, default is $_REQUEST)
	 * @param   array  $options  Array of configuration parameters (Optional)
	 */
	public function __construct(array $source = null, array $options = array())
	{
		$this->options = $options;

		if (isset($options['filter']))
		{
			$this->filter = $options['filter'];
		}
		else
		{
			$this->filter = Filter::getInstance();
		}

		// Get the command line options
		$this->parseArguments();

		// Set the options for the class.
		$this->options = $options;
	}

	/**
	 * Method to serialize the input.
	 *
	 * @return  string  The serialized input.
	 */
	public function serialize()
	{
		// Load all of the inputs.
		$this->loadAllInputs();

		// Remove $_ENV and $_SERVER from the inputs.
		$inputs = $this->inputs;
		unset($inputs['env']);
		unset($inputs['server']);

		// Serialize the executable, args, options, data, and inputs.
		return serialize(array($this->executable, $this->args, $this->options, $this->data, $inputs));
	}

	/**
	 * Method to unserialize the input.
	 *
	 * @param   string  $input  The serialized input.
	 *
	 * @return  Input  The input object.
	 */
	public function unserialize($input)
	{
		// Unserialize the executable, args, options, data, and inputs.
		list($this->executable, $this->args, $this->options, $this->data, $this->inputs) = unserialize($input);

		// Load the filter.
		if (isset($this->options['filter']))
		{
			$this->filter = $this->options['filter'];
		}
		else
		{
			$this->filter = Filter::getInstance();
		}
	}

	/**
	 * Initialise the options and arguments
	 *
	 * Not supported: -abc c-value
	 *
	 * @return  void
	 */
	protected function parseArguments()
	{
		$argv = $_SERVER['argv'];

		$this->executable = array_shift($argv);

		$out = array();

		for ($i = 0, $j = count($argv); $i < $j; $i++)
		{
			$arg = $argv[$i];

			// --foo --bar=baz
			if (substr($arg, 0, 2) === '--')
			{
				$eqPos = strpos($arg, '=');

				// --foo
				if ($eqPos === false)
				{
					$key = substr($arg, 2);

					// --foo value
					if ($i + 1 < $j && $argv[$i + 1][0] !== '-')
					{
						$value = $argv[$i + 1];
						$i++;
					}
					else
					{
						$value = isset($out[$key]) ? $out[$key] : true;
					}

					$out[$key] = $value;
				}

				// --bar=baz
				else
				{
					$key = substr($arg, 2, $eqPos - 2);
					$value = substr($arg, $eqPos + 1);
					$out[$key] = $value;
				}
			}
			elseif (substr($arg, 0, 1) === '-')
				// -k=value -abc
			{
				// -k=value
				if (substr($arg, 2, 1) === '=')
				{
					$key = substr($arg, 1, 1);
					$value = substr($arg, 3);
					$out[$key] = $value;
				}
				else
					// -abc
				{
					$chars = str_split(substr($arg, 1));

					foreach ($chars as $char)
					{
						$key = $char;
						$value = isset($out[$key]) ? $out[$key] : true;
						$out[$key] = $value;
					}

					// -a a-value
					if ((count($chars) === 1) && ($i + 1 < $j) && ($argv[$i + 1][0] !== '-'))
					{
						$out[$key] = $argv[$i + 1];
						$i++;
					}
				}
			}
			else
			{
				// Plain-arg
				$this->args[] = $arg;
			}
		}

		$this->data = $out;
	}
} 