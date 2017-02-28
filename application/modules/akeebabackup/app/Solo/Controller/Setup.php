<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Filesystem\Factory;
use \Awf\Mvc\Controller;
use \Awf\Application\Application;
use Awf\Router\Router;
use Awf\Session\Exception;
use Awf\Text\Text;
use Awf\Uri\Uri;

class Setup extends ControllerDefault
{
	/**
	 * Pre-execution check. If the application is already configured do not allow access to this view any more.
	 *
	 * @param   string  $task  The task to execute.
	 *
	 * @return  boolean|null
	 */
	public function execute($task)
	{
		// Do not allow setup to run when we are running inside another CMS
		$inCMS = $this->container->segment->get('insideCMS', false);

		if ($inCMS)
		{
			return false;
		}

		// If we're already configured, bail out
		$filePath = $this->container->basePath . '/assets/private/config.php';

		if (file_exists($filePath))
		{
			$user = $this->container->userManager->getUser();

			if (!defined('AKEEBADEBUG') || !$user->getPrivilege('akeeba.configure'))
			{
				return false;
			}
		}

		// Disable the main menu
		Application::getInstance()->getDocument()->getMenu()->disableMenu('main');

		// Finally, execute the task as planned
		return parent::execute($task);
	}

	public function main()
	{
		// If the session save path is not writable,
		$path = $this->container->session->getSavePath();

		if (!@is_dir($path) || !@is_writeable($path))
		{
			$router = $this->container->router;
			$this->setRedirect($router->route('index.php?view=setup&task=session'));

			return;
		}

		$this->display();
	}

	public function session()
	{
		$this->getView()->setLayout('session');

		$this->display();
	}

	public function savesession()
	{
		try
		{
			/** @var \Solo\Model\Setup $model */
			$model = $this->getModel();

			// Apply configuration settings to app config
			$model->setSetupParameters();

			// Try to connect to (S)FTP, if something like that was configured in the previous page. If it fails
			// we get a nice exception to throw us to the previous page.
			$fs = Factory::getAdapter($this->container, false);

			$sessionPath = $this->container->session->getSavePath();
			$this->container->application->createOrUpdateSessionPath($sessionPath, false);
		}
		catch (\Exception $e)
		{
			$errorMessage = base64_encode($e->getMessage());
			$url = Uri::rebase('?view=setup&task=session&error=' . $errorMessage, $this->container);
			$this->setRedirect($url);

			return;
		}

		$url = Uri::rebase('?view=setup', $this->container);
		$this->setRedirect($url);
	}

	/**
	 * Database setup task. This is where we ask the user for the database connection details.
	 *
	 * @return  boolean
	 */
	public function database()
	{
		$this->getView()->setLayout('database');

		$this->display();
	}

	/**
	 * Database installation task. This is where we try to actually install the database. Redirects back to the
	 * database task on error, forwards to the setup task on success.
	 *
	 * @return  boolean
	 */
	public function installdb()
	{
		try
		{
			/** @var \Solo\Model\Setup $model */
			$model = $this->getModel();

			$model->applyDatabaseParameters();
			$model->installDatabase();

			$this->setRedirect(Uri::rebase('?view=setup&task=setup', $this->container));

			return;
		}
		catch (\Exception $e)
		{
			$this->setRedirect(Uri::rebase('?view=setup&task=database', $this->container), $e->getMessage(), 'error');

			return;
		}
	}

	/**
	 * Application setup task.
	 *
	 * @return  boolean
	 */
	public function setup()
	{
		$this->getView()->setLayout('setup');

		$this->display();
	}

	/**
	 * Application setup task. This is where we commit all preferences to disk.
	 *
	 * @return  boolean
	 */
	public function finish()
	{
		try
		{
			/** @var \Solo\Model\Setup $model */
			$model = $this->getModel();

			// Apply database settings to app config
			$model->applyDatabaseParameters();

			// Apply configuration settings to app config
			$model->setSetupParameters();

			// Try to connect to (S)FTP, if something like that was configured in the previous page. If it fails
			// we get a nice exception to throw us to the previous page.
			$fs = Factory::getAdapter($this->container, false);

			// Try to create the new admin user and log them in
			$model->createAdminUser();
		}
		catch (\Exception $e)
		{
			$url = Uri::rebase('?view=setup&task=setup', $this->container);
			$this->setRedirect($url, $e->getMessage(), 'error');

			return;
		}

		try
		{
			// Save the configuration
			$this->container->appConfig->saveConfiguration();

			// Redirect to the Wizard page – we're done here
			$this->setRedirect(Uri::rebase('?view=wizard', $this->container), Text::_('SOLO_SETUP_MSG_DONE'), 'info');
		}
		catch (\Exception $e)
		{
			// We could not save the configuration. Show the page informing the user of the next steps to follow.
			$this->getView()->setLayout('finish');

			parent::display();
		}

		return;
	}
} 