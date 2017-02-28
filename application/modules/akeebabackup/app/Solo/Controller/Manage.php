<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Input\Input;
use Awf\Router\Router;
use Awf\Text\Text;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;

class Manage extends ControllerDefault
{
	public function main()
	{
		$this->container->segment->set('solo_manage_task', 'main');

		$this->display();
	}

	/**
	 * Shows a list of restore points. Reserved for future use.
	 */
	public function restorePoints()
	{
		$this->container->segment->set('solo_manage_task', 'restorePoints');

		$this->getView()->setLayout('restorepoint');

		$this->display();
	}

	/**
	 * Allows the editing of the backup comment
	 */
	public function showComment()
	{
		$this->csrfProtection();

		// Get the return URL
		$router = $this->container->router;
		$task = $this->container->segment->get('solo_manage_task', 'main');
		$returnUrl = $router->route('index.php?view=manage&task=' . $task);

		$model = $this->getModel();

		// Get the ID
		$id = $model->getState('id', 0);

		$part = $this->input->get('part', -1, 'int');

		$cid = $this->input->get('cid', array(), 'array');

		if (empty($id))
		{
			if (is_array($cid) && !empty($cid))
			{
				$id = $cid[0];
			}
			else
			{
				$id = -1;
			}
		}

		if ($id <= 0)
		{
			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');
		}
		else
		{
			$this->getModel()->setState('id', $id);
		}

		$this->getView()->setLayout('comment');
		$this->display();
	}

	/**
	 * Downloads the backup file of a specific backup attempt, if it's available on the server
	 *
	 * @return  void
	 */
	public function download()
	{
		$router = $this->container->router;
		$model = $this->getModel();
		$id = $model->getState('id', 0);

		$part = $this->input->get('part', -1, 'int');

		$cid = $this->input->get('cid', array(), 'array');

		if (empty($id))
		{
			if (is_array($cid) && !empty($cid))
			{
				$id = $cid[0];
			}
			else
			{
				$id = -1;
			}
		}

		if ($id <= 0)
		{
			$url = $router->route('index.php?view=manage');

			$this->setRedirect($url, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');

			return;
		}

		$stat = Platform::getInstance()->get_statistics($id);
		$allFileNames = Factory::getStatistics()->get_all_filenames($stat);

		// Check single part files
		if ((count($allFileNames) == 1) && ($part == -1))
		{
			$fileName = array_shift($allFileNames);
		}
		elseif ((count($allFileNames) > 0) && (count($allFileNames) > $part) && ($part >= 0))
		{
			$fileName = $allFileNames[$part];
		}
		else
		{
			$fileName = null;
		}

		if (is_null($fileName) || empty($fileName) || !@file_exists($fileName))
		{
			$url = $router->route('index.php?view=manage');

			$this->setRedirect($url, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDDOWNLOAD'), 'error');

			return;
		}
		else
		{
			// For a certain unmentionable browser
			if (function_exists('ini_get') && function_exists('ini_set'))
			{
				if (@ini_get('zlib.output_compression'))
				{
					@ini_set('zlib.output_compression', 'Off');
				}
			}

			// Remove PHP's time limit (is this actually still applicable in 2014?)
			if (function_exists('ini_get') && function_exists('set_time_limit'))
			{
				if (!@ini_get('safe_mode'))
				{
					@set_time_limit(0);
				}
			}

			$basename = @basename($fileName);
			$fileSize = @filesize($fileName);
			$extension = strtolower(str_replace(".", "", strrchr($fileName, ".")));

			while (@ob_end_clean())
			{
				;
			}
			@clearstatcache();

			// Send MIME headers
			header('MIME-Version: 1.0');
			header('Content-Disposition: attachment; filename="' . $basename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');

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

			// Notify of the file size, if this info is available
			if ($fileSize > 0)
			{
				header('Content-Length: ' . @filesize($fileName));
			}

			// Disable caching
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Expires: 0");
			header('Pragma: no-cache');

			flush();

			if ($fileSize > 0)
			{
				// If the filesize is reported, use 1M chunks for echoing the data to the browser
				$blocksize = 1048576; //1M chunks
				$handle = @fopen($fileName, "r");

				// Now we need to loop through the file and echo out chunks of file data
				if ($handle !== false)
				{
					while (!@feof($handle))
					{
						echo @fread($handle, $blocksize);
						@ob_flush();
						flush();
					}
				}

				if ($handle !== false)
				{
					@fclose($handle);
				}
			}
			else
			{
				// If the file size is not reported, hope that readfile works
				@readfile($fileName);
			}

			exit(0);
		}
	}

	/**
	 * Deletes one or several backup statistics records and their associated backup files
	 *
	 * @return  void
	 */
	public function remove()
	{
		// CSRF prevention
		$this->csrfProtection();

		// Get the return URL
		$router = $this->container->router;
		$task = $this->container->segment->get('solo_manage_task', 'main');
		$returnUrl = $router->route('index.php?view=manage&task=' . $task);

		// Get the ID
		$cid = $this->input->get('cid', array(), 'array');
		$id = $this->input->get('id', 0, 'int');

		if (empty($id))
		{
			if (!empty($cid) && is_array($cid))
			{
				foreach ($cid as $id)
				{
					$result = $this->_remove($id);

					if (!$result)
					{
						$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');

						return;
					}
				}
			}
			else
			{
				$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');

				return;
			}
		}
		else
		{
			$result = $this->_remove($id);

			if (!$result)
			{
				$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');
			}
		}

		$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_MSG_DELETED'));
	}

	/**
	 * Deletes backup files associated to one or several backup statistics records
	 *
	 * @return  void
	 */
	public function deleteFiles()
	{
		// CSRF prevention
		$this->csrfProtection();

		// Get the return URL
		$router = $this->container->router;
		$task = $this->container->segment->get('solo_manage_task', 'main');
		$returnUrl = $router->route('index.php?view=manage&task=' . $task);

		// Get the ID
		$cid = $this->input->get('cid', array(), 'array');
		$id = $this->input->get('id', 0, 'int');

		if (empty($id))
		{
			if (!empty($cid) && is_array($cid))
			{
				foreach ($cid as $id)
				{
					$result = $this->_removeFiles($id);

					if (!$result)
					{
						$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');

						return;
					}
				}
			}
			else
			{
				$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');

				return;
			}
		}
		else
		{
			$result = $this->_remove($id);

			if (!$result)
			{
				$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');
			}
		}

		$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_MSG_DELETEDFILE'));
	}

	/**
	 * Removes the backup file linked to a statistics entry and the entry itself
	 *
	 * @param   integer  $id  The ID of the backup record
	 *
	 * @return  boolean  True on success
	 */
	private function _remove($id)
	{
		// Get the return URL
		$router = $this->container->router;
		$task = $this->container->segment->get('solo_manage_task', 'main');
		$returnUrl = $router->route('index.php?view=manage&task=' . $task);

		if ($id <= 0)
		{
			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');

			return true;
		}

		/** @var \Solo\Model\Manage $model */
		$model = $this->getModel();

		$model->setState('id', $id);

		try
		{
			$model->delete();

			return true;
		}
		catch (\RuntimeException $e)
		{
			return false;
		}
	}

	/**
	 * Removes only the backup file linked to a statistics entry
	 *
	 * @param   integer  $id  The ID of the backup record
	 *
	 * @return  boolean  True on success
	 */
	private function _removeFiles($id)
	{
		// Get the return URL
		$router = $this->container->router;
		$task = $this->container->segment->get('solo_manage_task', 'main');
		$returnUrl = $router->route('index.php?view=manage&task=' . $task);

		if ($id <= 0)
		{
			$this->setRedirect($returnUrl, Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 'error');

			return true;
		}

		/** @var \Solo\Model\Manage $model */
		$model = $this->getModel();

		$model->setState('id', $id);

		try
		{
			$model->deleteFile();

			return true;
		}
		catch (\RuntimeException $e)
		{
			return false;
		}
	}

	/**
	 * Save an edited backup record
	 *
	 * @return  void
	 */
	public function save()
	{
		// CSRF prevention
		$this->csrfProtection();

		// Get the return URL
		$router = $this->container->router;
		$task = $this->container->segment->get('solo_manage_task', 'main');
		$returnUrl = $router->route('index.php?view=manage&task=' . $task);

		$id = $this->input->get('id', 0, 'int');
		$description = $this->input->get('description', '', 'string');
		$comment = $this->input->get('comment', null, 'string', 4);

		$statistic = Platform::getInstance()->get_statistics($id);
		$statistic['description'] = $description;
		$statistic['comment'] = $comment;

		$dummy = null;
		$result = Platform::getInstance()->set_or_update_statistics($id, $statistic, $dummy);

		if ($result !== false)
		{
			$message = Text::_('COM_AKEEBA_BUADMIN_LOG_SAVEDOK');
			$type = 'message';
		}
		else
		{
			$message = Text::_('COM_AKEEBA_BUADMIN_LOG_SAVEERROR');
			$type = 'error';
		}

		$this->setRedirect($returnUrl, $message, $type);
	}

	/**
	 * Redirect to the restoration page for this backup record
	 *
	 * @return  void
	 */
	public function restore()
	{
		// CSRF prevention
		$this->csrfProtection();

		$router = $this->container->router;

		$id = null;
		$cid = $this->input->get('cid', array(), 'array');

		if (!empty($cid))
		{
			$id = intval($cid[0]);

			if ($id <= 0)
			{
				$id = null;
			}
		}

		if (empty($id))
		{
			$id = $this->input->get('id', -1, 'int');
		}

		if ($id <= 0)
		{
			$id = null;
		}

		$url = $router->route('index.php?view=restore&id=' . $id);
		$this->setRedirect($url);

		return;
	}

	/**
	 * Cancel the editing operation
	 *
	 * @return  void
	 */
	public function cancel()
	{
		// CSRF prevention
		$this->csrfProtection();

		// Get the return URL
		$router = $this->container->router;
		$task = $this->container->segment->get('solo_manage_task', 'main');
		$returnUrl = $router->route('index.php?view=manage&task=' . $task);

		$this->setRedirect($returnUrl);
	}

	public function hideModal()
	{
		/** @var \Solo\Model\Manage $model */
		$model = $this->getModel();
		$model->hideRestorationInstructionsModal();

		// Get the return URL
		$router = $this->container->router;
		$task = $this->container->segment->get('solo_manage_task', 'main');
		$returnUrl = $router->route('index.php?view=manage&task=' . $task);

		$this->setRedirect($returnUrl);
	}
}