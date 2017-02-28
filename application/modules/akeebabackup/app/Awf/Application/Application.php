<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Application;

use Awf\Container\Container;
use Awf\Dispatcher\Dispatcher;
use Awf\Document\Document;
use Awf\Exception;
use Awf\Input\Input;
use Awf\Registry\Registry;
use Awf\Router\Router;
use Awf\Session;
use Awf\Session\Manager;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\User;
use Awf\Event;


/**
 * Class Application
 *
 * A generic, simple web application implementation
 *
 * @package Awf\Application
 */
abstract class Application
{
	/** @var   array  An array of application instances */
	protected static $instances = array();

	/** @var   array  The application message queue */
	public $messageQueue = array();

	/** @var   Container  The DI container for this application */
	protected $container = null;

	/** @var   string  The name (alias) of the application */
	protected $name = null;

	/** @var   string  The name of the template's directory */
	protected $template = null;

	/** @var   string  The base path to the application's root */
	protected $basePath = null;

	/** @var   float  The start time (with decimal microseconds) the application object was created */
	private $startTime = 0;

	/**
	 * Public constructor
	 *
	 * @param Container $container Configuration parameters
	 *
	 * @return Application
	 */
	public function __construct(Container $container = null)
	{
		// Start keeping time
		$this->startTime = microtime(true);

		// Create or attach the DI container
		if (!is_object($container) || !($container instanceof Container))
		{
			$container = new Container();
		}

		$this->container = $container;

		// Set the application name
		if (empty($container['application_name']))
		{
			$container->application_name = $this->getName();
		}

		$this->name = $container->application_name;

		// Set up the filesystem path
		if (empty($container['filesystemBase']))
		{
			$container->filesystemBase = APATH_BASE;
		}

		// Set up the base path
		if (empty($container['basePath']))
		{
			$container->basePath = (defined('APATH_BASE') ? APATH_BASE : $container->filesystemBase) . '/' . ucfirst($this->name);
		}

		// Set up the template path
		if (empty($container['templatePath']))
		{
			$container->templatePath = defined('APATH_THEMES') ? APATH_THEMES : $container->filesystemBase . '/templates';
		}

		// Set up the temporary path
		if (empty($container['temporaryPath']))
		{
			$container->temporaryPath = defined('APATH_TMP') ? APATH_TMP . '/tmp' : $container->filesystemBase . '/tmp';
		}

		// Set up the language path
		if (empty($container['languagePath']))
		{
			$container->languagePath = defined('APATH_TRANSLATION') ? APATH_TRANSLATION : $container->filesystemBase . '/languages';
		}

		// Set up the language path
		if (empty($container['sqlPath']))
		{
			$container->sqlPath = defined('APATH_ROOT') ? (APATH_ROOT . '/installation/sql') : $container->filesystemBase . '/installation/sql';
		}

		// Start the session
		$this->container->session->start();

		// Forcibly create the session segment
		$segment = $this->container->segment;

		// Set up the template
		$this->setTemplate();

		// Load the translation strings
		Text::addIniProcessCallback(array($this, 'processLanguageIniFile'));
		$languagePath = $container->languagePath;
		Text::loadLanguage(null, $this->name, '.ini', true, $languagePath);
		Text::loadLanguage('en-GB', $this->name, '.ini', false, $languagePath);
	}

	/**
	 * Gets the name of the application by breaking down the application class'
	 * name. For example, FooApplication returns "foo".
	 *
	 * @return  string  The application name, all lowercase
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$class = get_class($this);
			$class = preg_replace('/(\s)+/', '_', $class);
			$class = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $class));
			$class = str_replace('\\', '_', $class);
			$class = explode('_', $class);

			return array_shift($class);
		}
		else
		{
			return $this->name;
		}
	}

	/**
	 * Gets an instance of the application
	 *
	 * @param   string    $name      The name of the application (folder name)
	 * @param   Container $container The DI container to use for the instance (if the instance is not already set)
	 *
	 * @return  Application
	 *
	 * @throws  Exception\App
	 */
	public static function getInstance($name = null, Container $container = null)
	{
		if (empty($name) && !empty(self::$instances))
		{
			$keys = array_keys(self::$instances);
			$name = array_shift($keys);
		}
		elseif (empty($name))
		{
			$name = 'unnamed';
		}

		$name = strtolower($name);

		if (!array_key_exists($name, self::$instances))
		{
			$className = '\\' . ucfirst($name) . '\\Application';

			if (!class_exists($className))
			{
				$filePath = __DIR__ . '/../../' . $name . '/application.php';
				$result = @include_once($filePath);

				if (!class_exists($className, false))
				{
					$className = 'Application';
				}

				if (!class_exists($className, false))
				{
					$result = false;
				}
			}
			else
			{
				$result = true;
			}

			if ($result === false)
			{
				throw new Exception\App("The application '$name' was not found on this server");
			}

			self::$instances[$name] = new $className($container);
		}

		return self::$instances[$name];
	}

	/**
	 * Initialises the application
	 *
	 * @return  void
	 */
	abstract public function initialise();

	/**
	 * Parse the URL through the routing rules. Must be used before dispatch().
	 *
	 * @param   string $url The URL that needs route parsing. Leave null to use the current URL.
	 *
	 * @return  void
	 */
	public function route($url = null)
	{
		$this->container->router->parse($url);
	}

	/**
	 * Dispatches the application
	 *
	 * @return  void
	 */
	public function dispatch()
	{
		@ob_start();

		$dispatcher = $this->container->dispatcher;

		$dispatcher->dispatch();
		$result = @ob_get_clean();

		$document = $this->getDocument();
		$document->setBuffer($result);
	}

	/**
	 * Creates and returns the document object
	 *
	 * @return  Document  The document for this application's output
	 */
	public function getDocument()
	{
		static $instance = null;

		if (is_null($instance))
		{
			$type = $this->getContainer()->input->getCmd('format', 'html');

			$instance = Document::getInstance($type, $this->container);
		}

		return $instance;
	}

	/**
	 * Renders the application
	 *
	 * @return  void
	 */
	public function render()
	{
		$this->getDocument()->render();
	}

	/**
	 * Returns enqueued messages of a specific type
	 *
	 * @param   string $type The message type (info, error)
	 *
	 * @return  array  An array of message strings
	 */
	public function getMessageQueueFor($type = 'info')
	{
		$ret = array();

		$messageQueue = $this->getMessageQueue();

		if (count($messageQueue))
		{
			foreach ($messageQueue as $message)
			{
				if ($message['type'] == $type)
				{
					$ret[] = $message['message'];
				}
			}
		}

		return $ret;
	}

	/**
	 * Get the system message queue.
	 *
	 * @return  array  The system message queue.
	 */
	public function getMessageQueue()
	{
		// For empty queue, if messages exists in the session, enqueue them.
		if (!count($this->messageQueue))
		{
			if ($this->container->segment->hasFlash('application_queue'))
			{
				$this->messageQueue = $this->container->segment->getFlash('application_queue');
			}
		}

		return $this->messageQueue;
	}

	/**
	 * Removes everything from the message queue. Call it after displaying or logging all the queued messages.
	 *
	 * @return  void
	 */
	public function clearMessageQueue()
	{
		$this->getMessageQueue();

		$this->messageQueue = array();
	}

	/**
	 * Redirect to another URL.
	 *
	 * Optionally enqueues a message in the system message queue (which will be displayed
	 * the next time a page is loaded) using the enqueueMessage method. If the headers have
	 * not been sent the redirect will be accomplished using a "301 Moved Permanently"
	 * code in the header pointing to the new location. If the headers have already been
	 * sent this will be accomplished using a JavaScript statement.
	 *
	 * @param   string  $url     The URL to redirect to. Can only be http/https URL
	 * @param   string  $msg     An optional message to display on redirect.
	 * @param   string  $msgType An optional message type. Defaults to message.
	 * @param   boolean $moved   True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
	 *
	 * @return  void  Calls exit().
	 *
	 * @see     Application::enqueueMessage()
	 */
	public function redirect($url, $msg = '', $msgType = 'info', $moved = false)
	{
		// Check for relative internal links.
		if (preg_match('#^index\.php#', $url))
		{
			$url = Uri::base(false, $this->container) . $url;
		}

		// Strip out any line breaks.
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		/*
		 * If we don't start with a http we need to fix this before we proceed.
		 * We could validly start with something else (e.g. ftp), though this would
		 * be unlikely and isn't supported by this API.
		 */
		if (!preg_match('#^http#i', $url))
		{
			$uri = Uri::getInstance();
			$prefix = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

			if ($url[0] == '/')
			{
				// We just need the prefix since we have a path relative to the root.
				$url = $prefix . $url;
			}
			else
			{
				// It's relative to where we are now, so lets add that.
				$parts = explode('/', $uri->toString(array('path')));
				array_pop($parts);
				$path = implode('/', $parts) . '/';
				$url = $prefix . $path . $url;
			}
		}

		// If the message exists, enqueue it.
		if (is_string($msg) && trim($msg))
		{
			$this->enqueueMessage($msg, $msgType);
		}

		// Persist messages if they exist.
		if (count($this->messageQueue))
		{
			$this->container->segment->setFlash('application_queue', $this->messageQueue);
		}

		$this->container->session->commit();

		// If the headers have been sent, then we cannot send an additional location header
		// so we will output a javascript redirect statement.
		if (headers_sent())
		{
			$url = htmlspecialchars($url);
			$url = str_replace('&amp;', '&', $url);
			echo "<script>document.location.href='" . $url . "';</script>\n";
		}
		else
		{
			header($moved ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
			header('Location: ' . $url);
		}

		exit(0);
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param   string $msg  The message to enqueue.
	 * @param   string $type The message type. Default is info.
	 *
	 * @return  void
	 */
	public function enqueueMessage($msg, $type = 'info')
	{
		// For empty queue, if messages exists in the session, enqueue them first.
		$this->getMessageQueue();

		// Enqueue the message.
		$this->messageQueue[] = array('message' => $msg, 'type' => strtolower($type));
	}

	/**
	 * Method to close the application. Automatically commits the session.
	 *
	 * @param   integer $code The exit code (optional; default is 0).
	 *
	 * @return  void
	 */
	public function close($code = 0)
	{
		// Persist messages if they exist.
		if (count($this->messageQueue))
		{
			$this->container->segment->setFlash('application_queue', $this->messageQueue);
		}

		$this->container->session->commit();

		exit($code);
	}

	/**
	 * Returns the template name
	 *
	 * @return  string
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * Sets the template name. It must be a directory inside APATH_THEMES.
	 *
	 * @param   string $template The template name
	 *
	 * @return  void
	 */
	public function setTemplate($template = null)
	{
		if (!empty($template))
		{
			$templatePath = $this->container->templatePath . '/' . $template;

			if (!is_dir($templatePath))
			{
				$template = null;
			}
		}

		if (empty($template))
		{
			$template = $this->getName();
		}

		$this->template = $template;
	}

	/**
	 * Get the time elapsed since the application object's instantiation in decimal seconds
	 *
	 * @return  float
	 */
	public function getTimeElapsed()
	{
		$microtime = microtime(true);

		return $microtime - $this->startTime;
	}

	/**
	 * Language file processing callback. This is added to the Text class, allowing you to perform language string
	 * processing as they are being loaded. For example, you may want to convert _QQ_ to " for language files imported
	 * from a legacy Joomla! project.
	 *
	 * @param   string $filename The full path to the file being loaded
	 * @param   array  $strings  The key/value array of the translations
	 *
	 * @return  boolean|array  False to prevent loading the file, or array of processed language string, or true to
	 *                         ignore this processing callback.
	 */
	public function processLanguageIniFile($filename, $strings)
	{
		return true;
	}

	/**
	 * Returns an instance of the DI container of the application
	 *
	 * @return \Awf\Container\Container
	 */
	public function &getContainer()
	{
		return $this->container;
	}
}