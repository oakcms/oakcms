<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;


use Awf\Application\Application;
use Awf\Input\Input;
use Awf\Mvc\Controller;

class Dbfilters extends ControllerDefault
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Register the two additional tasks
		$this->registerTask('normal', 'main');
		$this->registerTask('tabular', 'main');
	}

	/**
	 * Default task
	 *
	 * @return  void
	 */
	public function main()
	{
		$task = $this->input->getCmd('task', 'normal');

		if ($task == 'main')
		{
			$task = 'normal';
		}

		$this->getModel()->setState('browse_task', $task);

		$this->display();
	}

	/**
	 * AJAX proxy method
	 *
	 * @return  void
	 */
	public function ajax()
	{
		// Parse the JSON data and reset the action query param to the resulting array
		$action_json = $this->input->get('akaction', '', 'raw');
		$action = json_decode($action_json);

		/** @var \Solo\Model\Dbfilters $model */
		$model = $this->getModel();
		$model->setState('action', $action);

		$ret = $model->doAjax();

		@ob_end_clean();
		echo '###' . json_encode($ret) . '###';
		flush();

		$this->container->application->close();
	}
} 