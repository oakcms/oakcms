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
use Awf\Router\Router;
use Awf\Text\Text;

class Discover extends ControllerDefault
{
	/**
	 * Discovers JPA, JPS and ZIP files in the selected profile's directory and
	 * lets you select them for inclusion in the import process.
	 *
	 * @return  void
	 */
	public function discover()
	{
		$this->csrfProtection();

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=discover');

		$directory = $this->input->get('directory', '', 'string');

		if (empty($directory))
		{
			$msg = Text::_('COM_AKEEBA_DISCOVER_ERROR_NODIRECTORY');
			$this->setRedirect($returnUrl, $msg, 'error');

			return;
		}

		$model = $this->getModel();
		$model->setState('directory', $directory);

		$this->display();
	}

	/**
	 * Performs the actual import
	 *
	 * @return  void
	 */
	public function import()
	{
		$this->csrfProtection();

		$router = $this->container->router;
		$returnUrl = $router->route('index.php?view=discover');

		$directory = $this->input->get('directory', '', 'string');
		$files = $this->input->get('files', array(), 'array');

		if (empty($files))
		{
			$msg = Text::_('COM_AKEEBA_DISCOVER_ERROR_NOFILESSELECTED');
			$this->setRedirect($returnUrl, $msg, 'error');

			return;
		}

		/** @var \Solo\Model\Discover $model */
		$model = $this->getModel();
		$model->setState('directory', $directory);

		foreach ($files as $file)
		{
			$model->import($file);
		}

		$msg = Text::_('COM_AKEEBA_DISCOVER_LABEL_IMPORTDONE');
		$this->setRedirect($returnUrl, $msg);
	}
} 