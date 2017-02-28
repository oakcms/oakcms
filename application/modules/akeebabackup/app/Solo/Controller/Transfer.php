<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Controller;


use Awf\Session\Exception;
use Solo\Model\Exception\TransferFatalError;
use Solo\Model\Exception\TransferIgnorableError;
use Solo\Model\Transfers;

class Transfer extends ControllerDefault
{
	/** @var   array  The tasks this controller is allowed to use */
	private $allowedTasks = array('wizard', 'checkUrl', 'applyConnection', 'initialiseUpload', 'upload', 'reset');

	/**
	 * Override execute() to only allow specific tasks to run.
	 *
	 * @param   string   $task  The task we are asked to run.
	 *
	 * @return  bool|null
	 * @throws  \Exception
	 */
	public function execute($task)
	{
		if (!in_array($task, $this->allowedTasks))
		{
			$task = $this->allowedTasks[0];
		}

		return parent::execute($task);
	}

	/**
	 * Default task, shows the wizard interface
	 */
	public function wizard()
	{
		parent::display();
	}

	/**
	 * Reset the wizard
	 *
	 * @return  void
	 */
	public function reset()
	{
		$session = $this->container->segment;
		$session->set('transfer', null);
		$session->set('transfer.url', null);
		$session->set('transfer.url_status', null);
		$session->set('transfer.ftpsupport', null);

		/** @var Transfers $model */
		$model = $this->getModel();
		$model->resetUpload();

		$this->setRedirect($this->container->router->route('index.php?view=transfer'));
	}

	/**
	 * Cleans and checks the validity of the new site's URL
	 */
	public function checkUrl()
	{
		$url = $this->input->get('url', '', 'raw');

		/** @var Transfers $model */
		$model = $this->getModel(null, array('savestate' => 1));
		$result = $model->checkAndCleanUrl($url);

		$session = $this->container->segment;
		$session->set('transfer.url', $result['url']);
		$session->set('transfer.url_status', $result['status']);

		@ob_end_clean();
		echo '###' . json_encode($result) . '###';
        $this->container->application->close();
	}

	/**
	 * Applies the FTP/SFTP connection information and makes some preliminary validation
	 */
	public function applyConnection()
	{
		$result = (object)array(
			'status'    => true,
			'message'   => '',
			'ignorable' => false,
		);

		// Get the parameters from the request
		$transferOption = $this->input->getCmd('method', 'ftp');
		$force          = $this->input->getInt('force', 0);
		$ftpHost        = $this->input->get('host', '', 'raw');
		$ftpPort        = $this->input->getInt('port', null);
		$ftpUsername    = $this->input->get('username', '', 'raw');
		$ftpPassword    = $this->input->get('password', '', 'raw');
		$ftpPubKey      = $this->input->get('pubKey', '', 'raw');
		$ftpPrivateKey  = $this->input->get('privKey', '', 'raw');
		$ftpPassive     = $this->input->getInt('passive', 1);
		$ftpPassiveFix  = $this->input->getInt('passive_fix', 1);
		$ftpDirectory   = $this->input->get('directory', '', 'raw');

		// Fix the port if it's missing
		if (empty($ftpPort))
		{
			switch ($transferOption)
			{
				case 'ftp':
				case 'ftpcurl':
					$ftpPort = 21;
					break;

				case 'ftps':
				case 'ftpscurl':
					$ftpPort = 990;
					break;

				case 'sftp':
				case 'sftpcurl':
					$ftpPort = 22;
					break;
			}
		}

		// Store everything in the session
		$session = $this->container->segment;

		$session->set('transfer.transferOption', $transferOption);
		$session->set('transfer.force', $force);
		$session->set('transfer.ftpHost', $ftpHost);
		$session->set('transfer.ftpPort', $ftpPort);
		$session->set('transfer.ftpUsername', $ftpUsername);
		$session->set('transfer.ftpPassword', $ftpPassword);
		$session->set('transfer.ftpPubKey', $ftpPubKey);
		$session->set('transfer.ftpPrivateKey', $ftpPrivateKey);
		$session->set('transfer.ftpDirectory', $ftpDirectory);
		$session->set('transfer.ftpPassive', $ftpPassive ? 1 : 0);
		$session->set('transfer.ftpPassiveFix', $ftpPassiveFix ? 1 : 0);

		/** @var Transfers $model */
		$model = $this->getModel();

		try
		{
			$config = $model->getFtpConfig();
			$model->testConnection($config);
		}
		catch (TransferIgnorableError $e)
		{
			$result = (object)array(
				'status'    => false,
				'message'   => $e->getMessage(),
				'ignorable' => true,
			);
		}
		catch (Exception $e)
		{
			$result = (object)array(
				'status'    => false,
				'message'   => $e->getMessage(),
				'ignorable' => false,
			);
		}

		@ob_end_clean();
		echo '###' . json_encode($result) . '###';
		$this->container->application->close();
	}

	/**
	 * Initialise the upload: sends Kickstart and our add-on script to the remote server
	 */
	public function initialiseUpload()
	{
		$result = (object)array(
			'status'    => true,
			'message'   => '',
		);

		/** @var Transfers $model */
		$model = $this->getModel();

		try
		{
			$config = $model->getFtpConfig();
			$model->initialiseUpload($config);
		}
		catch (\Exception $e)
		{
			$result = (object)array(
				'status'    => false,
				'message'   => $e->getMessage(),
			);
		}

		@ob_end_clean();
		echo '###' . json_encode($result) . '###';
        $this->container->application->close();
	}

	/**
	 * Perform an upload step. Pass start=1 to reset the upload and start over.
	 */
	public function upload()
	{
		/** @var Transfers $model */
		$model = $this->getModel();

		if ($this->input->getBool('start', false))
		{
			$model->resetUpload();
		}

		try
		{
			$config = $model->getFtpConfig();
			$uploadResult = $model->uploadChunk($config);
		}
		catch (\Exception $e)
		{
			$uploadResult = (object)array(
				'status'    => false,
				'message'   => $e->getMessage(),
				'totalSize' => 0,
				'doneSize'  => 0,
				'done'      => false
			);
		}

		$result = (object)$uploadResult;

		@ob_end_clean();
		echo '###' . json_encode($result) . '###';
        $this->container->application->close();
	}
}