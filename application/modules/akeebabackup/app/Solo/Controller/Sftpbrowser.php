<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Mvc\Controller;

/**
 * The controller for FTP browser
 */
class Sftpbrowser extends ControllerDefault
{
	public function execute($task)
	{
		// If we are running inside a CMS but there is no active user we have to throw a 403
		$inCMS = $this->container->segment->get('insideCMS', false);

		if ($inCMS && !$this->container->userManager->getUser()->getId())
		{
			return false;
		}

		return parent::execute($task);
	}


	public function main()
	{
		/** @var \Solo\Model\Sftpbrowser $model */
		$model = $this->getModel();

		// Grab the data and push them to the model
		$directory = $this->input->getRaw('directory', '');
		$directory = '/' . ltrim($directory, '/');

		$model->setState('host',		$this->input->getString('host', ''));
		$model->setState('port',		$this->input->getInt('port', 22));
		$model->setState('username',	$this->input->getRaw('username', ''));
		$model->setState('password',	$this->input->getRaw('password', ''));
		$model->setState('directory', $directory);
		$model->setState('privKey',		$this->input->getRaw('privkey', ''));
		$model->setState('pubKey',		$this->input->getRaw('pubkey', ''));

		$ret = $model->doBrowse();

		@ob_end_clean();

		echo '###'.json_encode($ret).'###';

		flush();

		$this->container->application->close();
	}
} 