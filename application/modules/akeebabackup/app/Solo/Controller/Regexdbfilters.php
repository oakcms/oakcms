<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Controller;


class Regexdbfilters extends ControllerDefault
{
	/**
	 * AJAX proxy.
	 */
	public function ajax()
	{
		// Parse the JSON data and reset the action query param to the resulting array
		$action_json = $this->input->getVar('akaction', '', 'raw');
		$action = json_decode($action_json, true);

		/** @var \Solo\Model\Regexdbfilters $model */
		$model = $this->getModel();
		$model->setState('action', $action);

		$ret = $model->doAjax();
		@ob_end_clean();
		echo '###'.json_encode($ret).'###';
		flush();

		$this->container->application->close();
	}
} 