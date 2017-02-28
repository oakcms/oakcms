<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;


use Awf\Router\Router;
use Awf\Text\Text;

class S3import extends ControllerDefault
{
	public function main()
	{
		$s3bucket = $this->input->get('s3bucket', null, 'raw');

		/** @var \Solo\Model\S3import $model */
		$model = $this->getModel();

		$model->getS3Credentials();

		if (!empty($s3bucket))
		{
			$model->setState('s3bucket', $s3bucket);
		}

		$this->display();
	}

	/**
	 * Fetches a complete backup set from a remote storage location to the local (server)
	 * storage so that the user can download or restore it.
	 */
	public function downloadToServer()
	{
		$s3bucket = $this->input->get('s3bucket', null, 'raw');

		/** @var \Solo\Model\S3import $model */
		$model = $this->getModel();

		if ($s3bucket)
		{
			$model->setState('s3bucket', $s3bucket);
		}

		$model->getS3Credentials();
		$model->setS3Credentials(
			$model->getState('s3access'), $model->getState('s3secret')
		);

		// Set up the model's state
		$session = $this->container->segment;

		$part = $this->input->getInt('part', -999);

		if ($part >= -1)
		{
			$model->setState('part', $part);
		}

		$frag = $this->input->getInt('frag', -999);

		if ($frag >= -1)
		{
			$model->setState('frag', $frag);
		}

		$step = $this->input->getInt('step', -999);

		if ($step >= -1)
		{
			$model->setState('step', $step);
		}

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=s3import');

		try
		{
			$result = $model->downloadToServer();

			if ($result === true)
			{
				// Part(s) downloaded successfully. Render the view.
				$this->display();
			}
			else
			{
				// All done. Redirect to intial page with a success message.
				$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_S3IMPORT_MSG_IMPORTCOMPLETE'));
			}
		}
		catch (\Exception $e)
		{
			$this->setRedirect($returnUrl, $e->getMessage(), 'error');
		}
	}
} 