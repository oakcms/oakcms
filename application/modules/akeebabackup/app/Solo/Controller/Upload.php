<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Router\Router;
use Awf\Text\Text;
use Akeeba\Engine\Platform;
use Solo\View\Upload\Html;

class Upload extends ControllerDefault
{
	/**
	 * This controller does not have a default task
	 *
	 * @return  void
	 *
	 * @throws \RuntimeException
	 */
	public function main()
	{
		throw new \RuntimeException('Invalid task', 500);
	}

	/**
	 * This task starts the upload of the archive to the remote server
	 *
	 * @return  void
	 */
	public function start()
	{
		$id = $this->getAndCheckId();

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=upload&tmpl=component&task=cancelled&id=' . $id);

		// Check the backup stat ID
		if ($id === false)
		{
			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_TRANSFER_ERR_INVALIDID'), 'error');

			return;
		}

		// Start by resetting the saved post-processing engine
		$session = $this->container->segment;
		$session->set('postproc_engine', null);

		// Initialise the view
		/** @var Html $view */
		$view = $this->getView();

		$view->done = 0;
		$view->error = 0;

		$view->id = $id;
		$view->setLayout('default');

		$this->display();
	}

	/**
	 * This task steps the upload and displays the results
	 *
	 * @return  void
	 */
	public function upload()
	{
		// Get the parameters
		$id = $this->getAndCheckId();

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=upload&tmpl=component&task=cancelled&id=' . $id);

		$part = $this->input->get('part', 0, 'int');
		$frag = $this->input->get('frag', 0, 'int');

		// Check the backup stat ID
		if ($id === false)
		{
			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_TRANSFER_ERR_INVALIDID'), 'error');

			return;
		}

		// Set the model state
		/** @var \Solo\Model\Upload $model */
		$model = $this->getModel();

		$model->setState('id', $id);
		$model->setState('part', $part);
		$model->setState('frag', $frag);

		// Try uploading
		$result = $model->upload();

		// Get the modified model state
		$id = $model->getState('id');
		$part = $model->getState('part');
		$frag = $model->getState('frag');
		$stat = $model->getState('stat');
		$remote_filename = $model->getState('remotename');

		// Push the state to the view. We assume we have to continue uploading. We only change that if we detect an
		// upload completion or error condition in the if-blocks further below.
		$view = $this->getView();

		$view->setLayout('uploading');
		$view->parts = $stat['multipart'];
		$view->part = $part;
		$view->frag = $frag;
		$view->id = $id;
		$view->done = 0;
		$view->error = 0;

		if (($part >= 0) && ($result === true))
		{
			$view->setLayout('done');
			$view->done = 1;
			$view->error = 0;

			// Also reset the saved post-processing engine
			$session = $this->container->segment;
			$session->set('postproc_engine', null);
		}
		elseif ($result === false)
		{
			// If we have an error we have to display it and stop the upload
			$view->done = 0;
			$view->error = 1;
			$view->errorMessage = $model->getState('errorMessage', '');
			$view->setLayout('error');

			// Also reset the saved post-processing engine
			$session = $this->container->segment;
			$session->set('postproc_engine', null);
		}

		$this->display();
	}

	/**
	 * This task shows the error page when the upload fails for any reason
	 *
	 * @return  void
	 */
	public function cancelled()
	{
		$view = $this->getView();

		$view->setLayout('error');

		$this->display();
	}

	/**
	 * Gets the stats record ID from the request and checks that it does exist
	 *
	 * @return  boolean|integer  False if an invalid ID is found, the numeric ID if it's valid
	 */
	private function getAndCheckId()
	{
		$id = $this->input->get('id', 0, 'int');

		if ($id <= 0)
		{
			return false;
		}

		$statObject = Platform::getInstance()->get_statistics($id);

		if (empty($statObject) || !is_array($statObject))
		{
			return false;
		}

		return $id;
	}
} 