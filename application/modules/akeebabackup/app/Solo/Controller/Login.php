<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Mvc\Controller;
use Awf\Router\Router;
use Awf\Uri\Uri;

class Login extends ControllerDefault
{
	public function execute($task)
	{
		// If we are running inside a CMS but there is no active user we have to throw a 403
		$inCMS = $this->container->segment->get('insideCMS', false);

		if ($inCMS)
		{
			return false;
		}

		return parent::execute($task);
	}


	/**
	 * Database setup task. This is where we ask the user for the database connection details.
	 *
	 * @return  boolean
	 */
	public function login()
	{
		try
		{
			$this->csrfProtection();

			// Get the username and password from the request
			$username = $this->input->get('username', '', 'raw');
			$password = $this->input->get('password', '', 'raw');
			$secret = $this->input->get('secret', '', 'raw');

			// Try to log in the user
			$manager = $this->container->userManager;
			$manager->loginUser($username, $password, array('secret' => $secret));

			// Redirect to the saved return_url or, if none specified, to the application's main page
			$url = $this->container->segment->getFlash('return_url');
			$router = $this->container->router;

			if (empty($url))
			{
				$url = $router->route('index.php?view=main');
			}

			$this->setRedirect($url);
		}
		catch (\Exception $e)
		{
			$router = $this->container->router;

			// Login failed. Go back to the login page and show the error message
			$this->setRedirect($router->route('index.php?view=login'), $e->getMessage(), 'error');
		}

		return true;
	}

	public function logout()
	{
		$router = $this->container->router;
		$manager = $this->container->userManager;
		$manager->logoutUser();

		$this->setRedirect($router->route('index.php?view=main'));
	}
} 