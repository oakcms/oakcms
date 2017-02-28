<?php
/**
 * @package        awf
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\Container;

use Awf\Database\Driver;
use Awf\Platform\Joomla\Application\Application;
use Awf\Platform\Joomla\Application\ComponentConfig;
use Awf\Platform\Joomla\Application\Configuration;
use Awf\Platform\Joomla\Event\Dispatcher;
use Awf\Platform\Joomla\Helper\Helper;
use Awf\Platform\Joomla\Router\Router;
use Awf\Platform\Joomla\User\Manager;

/**
 * A Container suitable for Joomla! integration
 *
 * @package Awf\Platform\Joomla\Container
 *
 * @property  string                                                $extension_name        The name of the extension
 *
 * @property-read  \Awf\Platform\Joomla\Application\Application		$application           The application instance
 * @property-read  \Awf\Platform\Joomla\Application\Configuration   $appConfig             The application configuration registry
 * @property-read  \Awf\Platform\Joomla\Event\Dispatcher            $eventDispatcher       The global event dispatcher
 * @property-read  \JMail                                           $mailer                The email sender. Note: this is a factory method
 * @property-read  \Awf\Platform\Joomla\Router\Router               $router                The URL router
 * @property-read  \Awf\Platform\Joomla\Session\Segment             $segment               The session segment, where values are stored
 * @property-read  \Awf\Platform\Joomla\Session\Manager             $session               The session manager
 * @property-read  \Awf\Platform\Joomla\User\Manager				$userManager           The user manager object
 */
class Container extends \Awf\Container\Container
{
	public function __construct(array $values = array())
	{
        $this->extension_name = '';

        // If we don't pass an extension name, let's deduct it from the application name
        // This allows us to have an extension named com_foobar with Dummy\Whatever as namespace
        if (empty($values['extension_name']))
        {
            $values['extension_name'] = $values['application_name'];
        }

		// Set up the filesystem path
		if (empty($values['filesystemBase']))
		{
			$values['filesystemBase'] = JPATH_ROOT;
		}

		// Set up the base path
		if (empty($values['basePath']))
		{
			$basePath = '/components/com_' . strtolower($values['extension_name']) . '/' . $values['extension_name'];
			$values['basePath'] = (Helper::isBackend() ? JPATH_ADMINISTRATOR : JPATH_ROOT) . $basePath;
		}

		// Set up the template path
		if (empty($values['templatePath']))
		{
			$values['templatePath'] = __DIR__ . '/../templates';
		}

		// Set up the temporary path
		if (empty($values['temporaryPath']))
		{
            // Since this code could be invoked in VERY EARLY STAGES of the application (ie CLI), let's be 100% sure to load
            // the correct file, otherwise Joomla will fallback to the empty file in the libraries folder and things will break
			$values['temporaryPath'] = \JFactory::getConfig(JPATH_CONFIGURATION.'/configuration.php')->get('tmp_path', sys_get_temp_dir());
		}

		// Set up the language path
		if (empty($values['languagePath']))
		{
			$values['languagePath'] = (Helper::isBackend() ? JPATH_ADMINISTRATOR : JPATH_ROOT) . '/language';
		}

		// Set up the SQL files path
		if (empty($values['sqlPath']))
		{
			$values['sqlPath'] = JPATH_ADMINISTRATOR . '/components/com_' . strtolower($values['extension_name'])
				. '/sql/xml';
		}

		// Application service
		if (!isset($this['application']))
		{
			$this['application'] = function (Container $c)
			{
				return Application::getInstance($c->application_name, $c);
			};
		}

		// Session Manager service
		if (!isset($this['session']))
		{
			$this['session'] = function ()
			{
				return new \Awf\Platform\Joomla\Session\Manager(
					new \Awf\Platform\Joomla\Session\SegmentFactory,
					new \Awf\Platform\Joomla\Session\CsrfTokenFactory()
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
					$c->session_segment_name = $c->application_name;
				}

				return $c->session->newSegment($c->session_segment_name);
			};
		}

		// Database Driver service
		if (!isset($this['db']))
		{
			$this['db'] = function (Container $c)
			{
				$db = \JFactory::getDbo();

				$options = array(
					'connection' => $db->getConnection(),
					'prefix'     => $db->getPrefix(),
					'driver'     => 'mysqli',
				);

				switch ($db->name)
				{
					case 'mysql':
						$options['driver'] = 'Mysql';
						break;

					default:
					case 'mysqli':
						$options['driver'] = 'Mysqli';
						break;

					case 'sqlsrv':
					case 'mssql':
					case 'sqlazure':
						$options['driver'] = 'Sqlsrv';
						break;

					case 'postgresql':
						$options['driver'] = 'Postgresql';
						break;

					case 'pdo':
						$options['driver'] = 'Pdo';
						break;

					case 'sqlite':
						$options['driver'] = 'Sqlite';
						break;
				}

				return Driver::getInstance($options);
			};
		}

		// Application Event Dispatcher service
		if (!isset($this['eventDispatcher']))
		{
			$this['eventDispatcher'] = function (Container $c)
			{
				return new Dispatcher($c);
			};
		}

		// Application Configuration service
		if (!isset($values['appConfig']))
		{
			$values['appConfig'] = function (Container $c)
			{
				return new Configuration($c);
			};
		}

		// Application Router service
		if (!isset($values['router']))
		{
			$values['router'] = function (Container $c)
			{
				return new Router($c);
			};
		}

		// User Manager service
		if (!isset($values['userManager']))
		{
			$values['userManager'] = function (Container $c)
			{
				return new Manager($c);
			};
		}

		parent::__construct($values);

		// Mailer Object service – returns a Joomla! JMail object
		// IMPORTANT! It has to appear AFTER the parent __construct method
		$this['mailer'] = $this->factory(function (Container $c)
		{
			return \JFactory::getMailer();
		});

	}
}