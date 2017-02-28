<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\Application;


use Awf\Container\Container;
use Awf\Platform\Joomla\Application\Observer\ControllerAcl;
use Awf\Platform\Joomla\Application\Observer\ViewAlternatePaths;
use Awf\Platform\Joomla\Helper\Helper;
use Awf\Text\Text;

class Application extends \Awf\Application\Application
{
	/**
	 * Gets an instance of the application
	 *
	 * @param   string    $name      The name of the application (folder name)
	 * @param   Container $container The DI container to use for the instance (if the instance is not already set)
	 *
	 * @return  Application
	 *
	 * @throws  \Awf\Exception\App
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
			$name = $container->input->get('option', null);
		}

		$name = strtolower($name);

		if (!array_key_exists($name, self::$instances))
		{
			$className = '\\' . ucfirst($name) . '\\Application';

			if (!class_exists($className))
			{
				$filePath = (Helper::isBackend() ? JPATH_ADMINISTRATOR : JPATH_SITE) . '/components/com_'
					. strtolower($name) . '/' . $name . '/application.php';
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
				throw new \Awf\Exception\App("The application '$name' was not found on this server");
			}

			self::$instances[$name] = new $className($container);
		}

		return self::$instances[$name];
	}

	public function initialise()
	{
		// Put a small marker to indicate that we run inside another CMS
		$this->container->segment->set('insideCMS', true);

		// Load the configuration
		$this->container->appConfig->loadConfiguration();

		// Attach the Joomla!-specific observer for Controller ACL checks
		$this->container->eventDispatcher->attach(new ControllerAcl($this->container->eventDispatcher));

		// Attach the Joomla!-specific observer for template override support
		$this->container->eventDispatcher->attach(new ViewAlternatePaths($this->container->eventDispatcher));

		// Set up the template (theme) to use – different for front-end and back-end
		if (empty($this->template) || ($this->template == $this->container->extension_name))
		{
			$template = Helper::isBackend() ? 'backend' : 'frontend';
			$this->setTemplate($template);
		}

		// Load the extra language files
		$appName = $this->container->extension_name;
		$appNameLower = strtolower($appName);
		$languageTag = \JFactory::getLanguage()->getTag();
		Text::loadLanguage('en-GB', $appName, '.com_' . $appNameLower . '.ini', false, $this->container->languagePath);
		Text::loadLanguage($languageTag, $appName, '.com_' . $appNameLower . '.ini', true, $this->container->languagePath);
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
		if (($msgType == 'info') && version_compare(JVERSION, '3.2.0', 'ge'))
		{
			$msgType = 'message';
		}

		\JFactory::getApplication()->enqueueMessage($msg, $msgType);
		\JFactory::getApplication()->redirect($url, $moved);
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
		if (($type == 'info') && version_compare(JVERSION, '3.2.0', 'ge'))
		{
			$type = 'message';
		}

		\JFactory::getApplication()->enqueueMessage($msg, $type);
	}

	/**
	 * Get the system message queue.
	 *
	 * @return  array  The system message queue.
	 */
	public function getMessageQueue()
	{
		return \JFactory::getApplication()->getMessageQueue();
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
		\JFactory::getApplication()->close($code);
	}
}