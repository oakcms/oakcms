<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Application;


use Awf\Container\Container;

abstract class Cli extends Application
{
	/**
	 * Public constructor
	 *
	 * @param   Container $container  The container attached to this application
	 *
	 * @return   Cli
	 */
	public function __construct(Container $container = null)
	{
		// Close the application if we are not executed from the command line, Akeeba style (allow for PHP CGI)
		if (array_key_exists('REQUEST_METHOD', $_SERVER))
		{
			die('You are not supposed to access this script from the web. You have to run it from the command line.');
		}

		if (empty($container['application_name']))
		{
			$container->application_name = 'cli';
			$this->name = 'cli';
		}

		parent::__construct($container);

		if (!($container->input instanceof \Awf\Input\Cli))
		{
			// Create an input object
			$cgiMode = false;

			if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
			{
				$cgiMode = true;
			}

			if ($cgiMode)
			{
				$query = "";

				if (!empty($_GET))
				{
					foreach ($_GET as $k => $v)
					{
						$query .= " $k";
						if ($v != "")
						{
							$query .= "=$v";
						}
					}
				}
				$query	 = ltrim($query);
				$argv	 = explode(' ', $query);
				$argc	 = count($argv);

				$_SERVER['argv'] = $argv;
			}

			unset($container['input']);
			$container['input'] = new \Awf\Input\Cli();

			try {

			} catch (\Exception $e)
			{

			}
		}

		self::$instances['cli'] = $this;
	}

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function execute()
	{
		// Trigger the onBeforeExecute event.
		if (method_exists($this, 'onBeforeExecute'))
		{
			$this->onBeforeExecute();
		}

		$this->container->eventDispatcher->trigger('onBeforeExecute', array(&$this));

		// Perform application routines.
		$this->doExecute();

		// Trigger the onAfterExecute event.
		if (method_exists($this, 'onAfterExecute'))
		{
			$this->onAfterExecute();
		}

		$this->container->eventDispatcher->trigger('onAfterExecute', array(&$this));
	}

	/**
	 * Write a string to standard output.
	 *
	 * @param   string   $text  The text to display.
	 * @param   boolean  $nl    True (default) to append a new line at the end of the output string.
	 *
	 * @return  Cli  Instance of $this to allow chaining.
	 */
	public function out($text = '', $nl = true)
	{
		fwrite(STDOUT, $text . ($nl ? "\n" : null));

		return $this;
	}

	/**
	 * Get a value from standard input.
	 *
	 * @return  string  The input string from standard input.
	 */
	public function in()
	{
		return rtrim(fread(STDIN, 8192), "\n");
	}

	/**
	 * Method to run the application routines.  Most likely you will want to instantiate a controller
	 * and execute it, or perform some sort of task directly.
	 *
	 * @return  void
	 */
	abstract protected function doExecute();
} 