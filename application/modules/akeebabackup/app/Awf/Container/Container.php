<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Awf\Container;

use Awf\Application\Application;
use Awf\Database\Driver;
use Awf\Pimple\Pimple;
use Awf\Session;
use Awf\Utils\Phpfunc;

/**
 * Dependency injection container for Awf's Application
 *
 * @property  string                                         $application_name      The name of the application
 * @property  string                                         $session_segment_name  The name of the session segment
 * @property  string                                         $basePath              The path to your application's PHP files
 * @property  string                                         $templatePath          The base path of all your template folders
 * @property  string                                         $languagePath          The base path of all your language folders
 * @property  string                                         $temporaryPath         The temporary directory of your application
 * @property  string                                         $filesystemBase        The base path of your web root (for use by Awf\Filesystem)
 * @property  string                                         $sqlPath               The path to the SQL files restored by Awf\Database\Restore
 * @property  string                                         $mediaQueryKey         The query string parameter to append to media added through the Template class
 *
 * @property-read  \Awf\Application\Application              $application           The application instance
 * @property-read  \Awf\Application\Configuration            $appConfig             The application configuration registry
 * @property-read  \Awf\Database\Driver                      $db                    The global database connection object
 * @property-read  \Awf\Dispatcher\Dispatcher                $dispatcher            The application dispatcher
 * @property-read  \Awf\Event\Dispatcher                     $eventDispatcher       The global event dispatched
 * @property-read  \Awf\Filesystem\FilesystemInterface       $fileSystem            The filesystem manager, created in hybrid mode
 * @property-read  \Awf\Input\Input                          $input                 The global application input object
 * @property-read  \Awf\Mailer\Mailer                        $mailer                The email sender. Note: this is a factory method
 * @property-read  \Awf\Router\Router                        $router                The URL router
 * @property-read  \Awf\Session\Segment                      $segment               The session segment, where values are stored
 * @property-read  \Awf\Session\Manager                      $session               The session manager
 * @property-read  \Awf\User\ManagerInterface                $userManager           The user manager object
 */
class Container extends Pimple
{
	public function __construct(array $values = array())
	{
		$this->application_name = '';
		$this->session_segment_name = null;
		$this->basePath = null;
		$this->templatePath = null;
		$this->languagePath = null;
		$this->temporaryPath = null;
		$this->filesystemBase = null;
		$this->sqlPath = null;
		$this->mediaQueryKey = null;

		parent::__construct($values);

		// Application service
		if (!isset($this['application']))
		{
			$this['application'] = function (Container $c)
			{
				return Application::getInstance($c->application_name, $c);
			};
		}

		// Application Configuration service
		if (!isset($this['appConfig']))
		{
			$this['appConfig'] = function (Container $c)
			{
				return new \Awf\Application\Configuration($c);
			};
		}

		// Database Driver service
		if (!isset($this['db']))
		{
			$this['db'] = function (Container $c)
			{
				return Driver::getInstance($c);
			};
		}

		// Application Dispatcher service
		if (!isset($this['dispatcher']))
		{
			$this['dispatcher'] = function (Container $c)
			{
				$className = '\\' . ucfirst($c->application_name) . '\Dispatcher';

				if (!class_exists($className))
				{
					$className = '\Awf\Dispatcher\Dispatcher';
				}

				return new $className($c);
			};
		}

		// Application Event Dispatcher service
		if (!isset($this['eventDispatcher']))
		{
			$this['eventDispatcher'] = function (Container $c)
			{
				return new \Awf\Event\Dispatcher($c);
			};
		}

		// Filesystem Abstraction Layer service
		if (!isset($this['fileSystem']))
		{
			$this['fileSystem'] = function (Container $c)
			{
				return \Awf\Filesystem\Factory::getAdapter($c, true);
			};
		}

		// Input Access service
		if (!isset($this['input']))
		{
			$this['input'] = function (Container $c)
			{
				return new \Awf\Input\Input();
			};
		}

		// Mailer Object service
		if (!isset($this['mailer']))
		{
			$this['mailer'] = $this->factory(function (Container $c)
			{
				return new \Awf\Mailer\Mailer($c);
			});
		}

		// Application Router service
		if (!isset($this['router']))
		{
			$this['router'] = function (Container $c)
			{
				return new \Awf\Router\Router($c);
			};
		}

		// Session Manager service
		if (!isset($this['session']))
		{
			$this['session'] = function ()
			{
				return new Session\Manager(
					new Session\SegmentFactory,
					new Session\CsrfTokenFactory(
						new Session\Randval(
							new Phpfunc()
						)
					),
					$_COOKIE
				);
			};
		}

		// Application Session Segment service
		if (!isset($this['segment']))
		{
			$this['segment'] = function (Container $c)
			{
				if (empty($c->session_segment_name))
				{
					$c->session_segment_name = 'Akeeba\\Awf\\' . $c->application_name;
				}

				return $c->session->newSegment($c->session_segment_name);
			};
		}

		// User Manager service
		if (!isset($this['userManager']))
		{
			$this['userManager'] = function (Container $c)
			{
				return new \Awf\User\Manager($c);
			};
		}
	}
}