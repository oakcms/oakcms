<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Mvc\Controller;

class Json extends ControllerDefault
{
	/**
	 * Always execute the 'json' task
	 *
	 * @param   string $task
	 *
	 * @return  boolean|null
	 */
	public function execute($task)
	{
		$this->input->set('task', 'json');
		$task = 'json';

		return parent::execute($task);
	}

	/**
	 * Handles API calls
	 */
	public function json()
	{
		// Use the model to parse the JSON message
		if (function_exists('ob_start'))
		{
			@ob_start();
		}
		$sourceJSON = $this->input->get('json', null, 'raw');

		// On some !@#$%^& servers where magic_quotes_gpc is On we might get extra slashes added
		if (function_exists('get_magic_quotes_gpc'))
		{
			if (get_magic_quotes_gpc())
			{
				$sourceJSON = stripslashes($sourceJSON);
			}
		}

		/** @var \Solo\Model\Json $model */
		$model = $this->getModel();
		$json = $model->execute($sourceJSON);

		if (function_exists('ob_end_clean'))
		{
			@ob_end_clean();
		}

		// Just dump the JSON and tear down the application, without plugins executing
		header('Content-type: text/plain');
		header('Connection: close');
		echo $json;

		$this->container->application->close();
	}
} 