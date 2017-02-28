<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Router\Router;
use Awf\Text\Text;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Remotefiles extends ControllerDefault
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
	 * Lists the available remote storage actions for a specific backup entry
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function listActions()
	{
		// List available actions
		$id = $this->getAndCheckId();
		$model = $this->getModel();
		$model->setState('id', $id);

		if ($id === false)
		{
			throw new \RuntimeException('Invalid ID', 500);
		}

		$this->display();
	}


	/**
	 * Fetches a complete backup set from a remote storage location to the local (server)
	 * storage so that the user can download or restore it.
	 *
	 * @return  void
	 */
	public function downloadToServer()
	{
		// Get the parameters
		$id = $this->getAndCheckId();
		$part = $this->input->get('part', -1, 'int');
		$frag = $this->input->get('frag', -1, 'int');

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=remotefiles&tmpl=component&task=listActions&id=' . $id);

		// Check the ID
		if ($id === false)
		{

			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_REMOTEFILES_ERR_INVALIDID'), 'error');

			return;
		}

        /** @var \Solo\Model\Remotefiles $model */
		$model = $this->getModel();
		$model->setState('id', $id);
		$model->setState('part', $part);
		$model->setState('frag', $frag);

		$result = $model->downloadToServer();

		if ($result['finished'])
		{
			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_REMOTEFILES_LBL_JUSTFINISHED'));

			return;
		}
		elseif ($result['error'])
		{
			$this->setRedirect($returnUrl, $result['error'], 'error');

			return;
		}
		else
		{
			$this->getView()->setLayout('dlprogress');
			$this->display();
		}
	}

	/**
	 * Downloads a file from the remote storage to the user's browsers
	 *
	 * @return  void
	 */
	public function downloadFromRemote()
	{
		$id = $this->getAndCheckId();
		$part = $this->input->get('part', 0, 'int');

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=remotefiles&tmpl=component&task=listActions&id=' . $id);

		if ($id === false)
		{
			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_REMOTEFILES_ERR_INVALIDID'), 'error');

			return;
		}

		$stat = Platform::getInstance()->get_statistics($id);
		$remoteFileNameRaw = $stat['remote_filename'];
		$remoteFileNameParts = explode('://', $remoteFileNameRaw);
		$engine = Factory::getPostprocEngine($remoteFileNameParts[0]);
		$remoteFileName = $remoteFileNameParts[1];

		$basename = basename($remoteFileName);
		$extension = strtolower(str_replace(".", "", strrchr($basename, ".")));

		if ($part > 0)
		{
			$newExtension = substr($extension, 0, 1) . sprintf('%02u', $part);
		}
		else
		{
			$newExtension = $extension;
		}

		$fileName = $basename . '.' . $newExtension;
		$remoteFileName = substr($remoteFileName, 0, -strlen($extension)) . $newExtension;

		if ($engine->downloads_to_browser_inline)
		{
			@ob_end_clean();
			@clearstatcache();
			// Send MIME headers
			header('MIME-Version: 1.0');
			header('Content-Disposition: attachment; filename="' . $fileName . '"');
			header('Content-Transfer-Encoding: binary');
			switch ($extension)
			{
				case 'zip':
					// ZIP MIME type
					header('Content-Type: application/zip');
					break;

				default:
					// Generic binary data MIME type
					header('Content-Type: application/octet-stream');
					break;
			}
			// Disable caching
			header('Expires: Mon, 20 Dec 1998 01:00:00 GMT');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
		}

		Platform::getInstance()->load_configuration($stat['profile_id']);
		$result = $engine->downloadToBrowser($remoteFileName);

		if (is_string($result) && ($result !== true) && $result !== false)
		{
			// We have to redirect
			$result = str_replace('://%2F', '://', $result);
			@ob_end_clean();
			header('Location: ' . $result);
			flush();

			$this->container->application->close();
		}
		elseif ($result === false)
		{
			// Failed to download
			$this->setRedirect($returnUrl, $engine->getWarning(), 'error');
		}
	}


	/**
	 * Deletes a file from the remote storage
	 *
	 * @return  void
	 */
	public function delete()
	{
		// Get the parameters
		$id = $this->getAndCheckId();
		$part = $this->input->get('part', -1, 'int');

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=remotefiles&tmpl=component&task=listActions&id=' . $id);

		// Check the ID
		if ($id === false)
		{
			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_REMOTEFILES_ERR_INVALIDID'), 'error');

			return;
		}

        /** @var \Solo\Model\Remotefiles $model */
		$model = $this->getModel();
		$model->setState('id', $id);
		$model->setState('part', $part);

		$result = $model->deleteRemoteFiles();

		if ($result['finished'])
		{
			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_REMOTEFILES_LBL_JUSTFINISHEDELETING'));
		}
		elseif ($result['error'])
		{
			$this->setRedirect($returnUrl, $result['error'], 'error');
		}
		else
		{
			$url = $router->route('index.php?view=remotefiles&tmpl=component&task=delete&id=' . $result['id'] . '&part=' . $result['part']);
			$this->setRedirect($url);
		}
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

        // Load the correct backup profile. The post-processing engine could rely on the active profile (ie OneDrive).
        define('AKEEBA_PROFILE', $statObject['profile_id']);
        Platform::getInstance()->load_configuration($statObject['profile_id']);
        $config = Factory::getConfiguration();

		return $id;
	}
}