<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Mvc\DataModel;
use Awf\Text\Text;
use RuntimeException;

class Profiles extends DataControllerDefault
{
	/**
	 * Imports an exported profile .json file
	 *
	 * @return  void
	 */
	public function import()
	{
		// CSRF prevention
		$this->csrfProtection();

		// Get the reference to the uploaded file
		$file = $_FILES['importfile'];

		// Get a URL router
		$router = $this->container->router;

		if (!isset($file['name']))
		{
			$this->setRedirect($router->route('index.php?view=profiles'), Text::_('MSG_UPLOAD_INVALID_REQUEST'), 'error');
		}

		/** @var \Solo\Model\Profiles $model */
		$model = $this->getModel();

		// Load the file data
		$data = file_get_contents($file['tmp_name']);
		@unlink($file['tmp_name']);

		// JSON decode
		$data = json_decode($data, true);

		// Import
		$message     = Text::_('COM_AKEEBA_PROFILES_MSG_IMPORT_COMPLETE');
		$messageType = null;

		try
		{
			$model->reset()->import($data);
		}
		catch (RuntimeException $e)
		{
			$message     = $e->getMessage();
			$messageType = 'error';
		}

		// Redirect back to the main page
		$this->setRedirect($router->route('index.php?view=profiles'), $message, $messageType);
	}
}