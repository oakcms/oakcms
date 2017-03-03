<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace Awf\Mvc;

use Awf\Application\Application;
use Awf\Container\Container;
use Awf\Input\Input;
use Awf\Text\Text;
use Awf\Uri\Uri;

/**
 * Class View
 *
 * A generic MVC view implementation
 *
 * @package Awf\Mvc
 */
class View
{
	/**
	 * The name of the view
	 *
	 * @var    array
	 */
	protected $name = null;

	/**
	 * Registered models
	 *
	 * @var    array
	 */
	protected $modelInstances = array();

	/**
	 * The default model
	 *
	 * @var    string
	 */
	protected $defaultModel = null;

	/**
	 * Layout name
	 *
	 * @var    string
	 */
	protected $layout = 'default';

	/**
	 * Layout template
	 *
	 * @var    string
	 */
	protected $layoutTemplate = '_';

	/**
	 * The set of search directories for view templates
	 *
	 * @var   array
	 */
	protected $templatePaths = array();

	/**
	 * The name of the default template source file.
	 *
	 * @var   string
	 */
	protected $template = null;

	/**
	 * The output of the template script.
	 *
	 * @var   string
	 */
	protected $output = null;

	/**
	 * A cached copy of the configuration
	 *
	 * @var   array
	 */
	protected $config = array();

	/**
	 * The input object
	 *
	 * @var   Input
	 */
	protected $input = null;

	/**
	 * The container attached to this view
	 *
	 * @var   Container
	 */
	protected $container;

	/**
	 * Current or most recently performed task.
	 * Currently public, it should be reduced to protected in the future
	 *
	 * @var  string
	 */
	public $task;

	/**
	 * The mapped task that was performed.
	 * Currently public, it should be reduced to protected in the future
	 *
	 * @var  string
	 */
	public $doTask;

	/**
	 * Returns an instance of a view class
	 *
	 * @param null      $appName   The application name [optional] Default: from container or default app if no container is provided
	 * @param null      $viewName  The view name [optional] Default: the "view" input parameter
	 * @param null      $viewType  The view type [optional] Default: the "format" input parameter or, if not defined, "html"
	 * @param Container $container The container to be attached to the view
	 *
	 * @return mixed
	 */
	public static function &getInstance($appName = null, $viewName = null, $viewType = null, $container = null)
	{
		if (empty($appName) && !is_object($container))
		{
			$app = Application::getInstance();
			$appName = $app->getName();
			$container = $app->getContainer();
		}
		elseif (empty($appName) && is_object($container))
		{
			$appName = $container->application_name;
		}
		elseif (!empty($appName) && !is_object($container))
		{
			$container = Application::getInstance($appName)->getContainer();
		}

		$input = $container->input;

		if (empty($viewName))
		{
			$viewName = $input->getCmd('view', '');
		}

		if (empty($viewType))
		{
			$viewType = $input->getCmd('format', 'html');
		}

		$classNames = array(
			'\\' . ucfirst($appName) . '\\View\\' . ucfirst($viewName) . '\\' . ucfirst($viewType),
			'\\' . ucfirst($appName) . '\\View\\' . ucfirst($viewName) . '\\DefaultView',
			'\\' . ucfirst($appName) . '\\View\\Default\\' . ucfirst($viewType),
			'\\' . ucfirst($appName) . '\\View\\DefaultView'
		);

		foreach ($classNames as $className)
		{
			if (class_exists($className))
			{
				break;
			}
		}

		if (!class_exists($className))
		{
			throw new \RuntimeException("View not found (app : view : type) = $appName : $viewName : $viewType");
		}

		$object = new $className($container);

		return $object;
	}

	/**
	 * Constructor
	 *
	 * @param   Container $container   A named configuration array for object construction.<br/>
	 *                                 Inside it you can have an 'mvc_config' array with the following options:<br/>
	 *                                 name: the name (optional) of the view (defaults to the view class name suffix).<br/>
	 *                                 escape: the name (optional) of the function to use for escaping strings<br/>
	 *                                 template_path: the path (optional) of the layout directory (defaults to base_path + /views/ + view name<br/>
	 *                                 layout: the layout (optional) to use to display the view<br/>
	 *
	 * @return  View
	 */
	public function __construct($container = null)
	{
		// Make sure we have a container
		if (!is_object($container))
		{
			$container = Application::getInstance()->getContainer();
		}

		// Cache some useful references in the class
		$this->input = $container->input;

		$this->container = $container;

		$this->config = isset($container['mvc_config']) ? $container['mvc_config'] : array();

		// Get the view name
		$this->name = $this->getName();

		// Set the default template search path
		if (array_key_exists('template_path', $this->config))
		{
			// User-defined dirs
			$this->setTemplatePath($this->config['template_path']);
		}
		else
		{
			$this->setTemplatePath($this->container->basePath . '/View/' . ucfirst($this->name) . '/tmpl');
		}

		// Set the layout
		if (array_key_exists('layout', $this->config))
		{
			$this->setLayout($this->config['layout']);
		}

		$templatePath = $this->container->templatePath;
		$fallback = $templatePath . '/' . $this->container->application->getTemplate() . '/html/' . ucfirst($this->container->application->getName()) . '/' . $this->name;
		$this->addTemplatePath($fallback);

		// Get extra directories through event dispatchers
		$extraPathsResults = $this->container->eventDispatcher->trigger('onGetViewTemplatePaths', array($this->getName()));

		if (is_array($extraPathsResults) && !empty($extraPathsResults))
		{
			foreach ($extraPathsResults as $somePaths)
			{
				if (!empty($somePaths))
				{
					foreach ($somePaths as $aPath)
					{
						$this->addTemplatePath($aPath);
					}
				}
			}
		}

		$this->baseurl = Uri::base(true, $this->container);
	}

	/**
	 * Sets an entire array of search paths for templates or resources.
	 *
	 * @param   mixed $path The new search path, or an array of search paths.  If null or false, resets to the current directory only.
	 *
	 * @return  void
	 */
	protected function setTemplatePath($path)
	{
		// Clear out the prior search dirs
		$this->templatePaths = array();

		// Actually add the user-specified directories
		$this->addTemplatePath($path);

		// Set the alternative template search dir
		$templatePath = $this->container->templatePath;
		$fallback = $templatePath . '/' . $this->container->application->getTemplate() . '/html/' . strtoupper($this->container->application->getName()) . '/' . $this->getName();
		$this->addTemplatePath($fallback);

		// Get extra directories through event dispatchers
		$extraPathsResults = $this->container->eventDispatcher->trigger('onGetViewTemplatePaths', array($this->getName()));

		if (is_array($extraPathsResults) && !empty($extraPathsResults))
		{
			foreach ($extraPathsResults as $somePaths)
			{
				if (!empty($somePaths))
				{
					foreach ($somePaths as $aPath)
					{
						$this->addTemplatePath($aPath);
					}
				}
			}
		}
	}

	/**
	 * Adds to the search path for templates and resources.
	 *
	 * @param   mixed $path The directory or stream, or an array of either, to search.
	 *
	 * @return  void
	 */
	protected function addTemplatePath($path)
	{
		// Just force to array
		settype($path, 'array');

		// Loop through the path directories
		foreach ($path as $dir)
		{
			// No surrounding spaces allowed!
			$dir = trim($dir);

			// Add trailing separators as needed
			if (substr($dir, -1) != DIRECTORY_SEPARATOR)
			{
				// Directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// Add to the top of the search dirs
			array_unshift($this->templatePaths, $dir);
		}
	}

	/**
	 * Method to get the view name
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the model
	 *
	 * @throws  \Exception
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$r = null;

			if (!preg_match('/(.*)\\\\View\\\\(.*)\\\\(.*)/i', get_class($this), $r))
			{
				throw new \Exception(\Awf\Text\Text::_('AWF_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
			}

			$this->name = strtolower($r[2]);
		}

		return $this->name;
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed $var The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Method to get data from a registered model or a property of the view
	 *
	 * @param   string $property  The name of the method to call on the Model or the property to get
	 * @param   string $default   The default value [optional]
	 * @param   string $modelName The name of the Model to reference [optional]
	 *
	 * @return  mixed  The return value of the method
	 */
	public function get($property, $default = null, $modelName = null)
	{
		// If $model is null we use the default model
		if (is_null($modelName))
		{
			$model = $this->defaultModel;
		}
		else
		{
			$model = strtolower($modelName);
		}

		// First check to make sure the model requested exists
		if (isset($this->modelInstances[$model]))
		{
			// Model exists, let's build the method name
			$method = 'get' . ucfirst($property);

			// Does the method exist?
			if (method_exists($this->modelInstances[$model], $method))
			{
				// The method exists, let's call it and return what we get
				$result = $this->modelInstances[$model]->$method();

				return $result;
			}
			else
			{
				$result = $this->modelInstances[$model]->$property();

				if (is_null($result))
				{
					return $default;
				}

				return $result;
			}
		}
		// If the model doesn't exist, try to fetch a View property
		else
		{
			if (@isset($this->$property))
			{
				return $this->$property;
			}
			else
			{
				return $default;
			}
		}
	}

	/**
	 * Returns a named Model object
	 *
	 * @param   string $name     The Model name. If null we'll use the modelName
	 *                           variable or, if it's empty, the same name as
	 *                           the Controller
	 * @param   array  $config   Configuration parameters to the Model. If skipped
	 *                           we will use $this->config
	 *
	 * @return  Model  The instance of the Model known to this Controller
	 */
	public function getModel($name = null, $config = array())
	{
		if (!empty($name))
		{
			$modelName = strtolower($name);
		}
		elseif (!empty($this->defaultModel))
		{
			$modelName = strtolower($this->defaultModel);
		}
		else
		{
			$modelName = strtolower($this->name);
		}

		if (!array_key_exists($modelName, $this->modelInstances))
		{
			$appName = $this->container->application->getName();

			if (empty($config))
			{
				$config = $this->config;
			}

			$this->container['mvc_config'] = $config;

			$this->modelInstances[$modelName] = Model::getInstance($appName, $modelName, $this->container);
		}

		return $this->modelInstances[$modelName];
	}

	/**
	 * Pushes the default Model to the View
	 *
	 * @param   Model $model The model to push
	 */
	public function setDefaultModel(Model &$model)
	{
		$name = $model->getName();

		$this->setDefaultModelName($name);
		$this->setModel($this->defaultModel, $model);
	}

	/**
	 * Set the name of the Model to be used by this View
	 *
	 * @param   string $modelName The name of the Model
	 *
	 * @return  void
	 */
	public function setDefaultModelName($modelName)
	{
		$this->defaultModel = $modelName;
	}

	/**
	 * Pushes a named model to the View
	 *
	 * @param   string $modelName The name of the Model
	 * @param   Model  $model     The actual Model object to push
	 *
	 * @return  void
	 */
	public function setModel($modelName, Model &$model)
	{
		$this->modelInstances[$modelName] = $model;
	}

	/**
	 * Overrides the default method to execute and display a template script.
	 * Instead of loadTemplate is uses loadAnyTemplate.
	 *
	 * @param   string $tpl The name of the template file to parse
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \Exception  When the layout file is not found
	 */
	public function display($tpl = null)
	{
		$method = 'onBefore' . ucfirst($this->doTask);
		if (method_exists($this, $method))
		{
			$result = $this->$method($tpl);

			if (!$result)
			{
				throw new \Exception(Text::_('AWF_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
			}
		}

		$result = $this->loadTemplate($tpl);

		$method = 'onAfter' . ucfirst($this->doTask);
		if (method_exists($this, $method))
		{
			$result = $this->$method($tpl);

			if (!$result)
			{
				throw new \Exception(Text::_('AWF_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
			}
		}

		if (is_object($result) && ($result instanceof \Exception))
		{
			throw $result;
		}
		else
		{
			echo $result;

			return true;
		}
	}

	/**
	 * Our function uses loadAnyTemplate to provide smarter view template loading.
	 *
	 * @param   string  $tpl    The name of the template file to parse
	 * @param   boolean $strict Should we use strict naming, i.e. force a non-empty $tpl?
	 *
	 * @return  mixed  A string if successful, otherwise an Exception
	 */
	public function loadTemplate($tpl = null, $strict = false)
	{
		$basePath = $this->name . '/';

		if ($strict)
		{
			$paths = array(
				$basePath . $this->getLayout() . ($tpl ? "_$tpl" : ''),
				$basePath . 'default' . ($tpl ? "_$tpl" : ''),
			);
		}
		else
		{
			$paths = array(
				$basePath . $this->getLayout() . ($tpl ? "_$tpl" : ''),
				$basePath . $this->getLayout(),
				$basePath . 'default' . ($tpl ? "_$tpl" : ''),
				$basePath . 'default',
			);
		}

		foreach ($paths as $path)
		{
			try
			{
				$result = $this->loadAnyTemplate($path);
			}
			catch (\Exception $e)
			{
				$result = $e;
			}

			if (!($result instanceof \Exception))
			{
				break;
			}
		}

		return $result;
	}

	/**
	 * Get the layout.
	 *
	 * @return  string  The layout name
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Sets the layout name to use
	 *
	 * @param   string $layout The layout name or a string in format <template>:<layout file>
	 *
	 * @return  string  Previous value.
	 */
	public function setLayout($layout)
	{
		$previous = $this->layout;
		if (strpos($layout, ':') === false)
		{
			$this->layout = $layout;
		}
		else
		{
			// Convert parameter to array based on :
			$temp = explode(':', $layout);
			$this->layout = $temp[1];

			// Set layout template
			$this->layoutTemplate = $temp[0];
		}

		return $previous;
	}

	/**
	 * Loads a template given any path. The path is in the format:
	 * viewname/templatename
	 *
	 * @param   string $path        The template path
	 * @param   array  $forceParams A hash array of variables to be extracted in the local scope of the template file
	 *
	 * @return  string  The output of the template
	 *
	 * @throws  \Exception  When the layout file is not found
	 */
	public function loadAnyTemplate($path = '', $forceParams = array())
	{
		$template = \Awf\Application\Application::getInstance()->getTemplate();
		$layoutTemplate = $this->getLayoutTemplate();

		// Parse the path
		$templateParts = $this->parseTemplatePath($path);

		// Get the default paths
		$templatePath = $this->container->templatePath;
		$paths = array();
		$paths[] = $templatePath . '/' . $template . '/html/' . $this->input->getCmd('option', '') . '/' . $templateParts['view'];
		$paths[] = $this->container->basePath . '/views/' . $templateParts['view'] . '/tmpl';
		$paths[] = $this->container->basePath . '/View/' . $templateParts['view'] . '/tmpl';

		$paths = array_merge($paths, $this->templatePaths);

		// Look for a template override
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template)
		{
			$apath = array_shift($paths);
			array_unshift($paths, str_replace($template, $layoutTemplate, $apath));
		}

		$filetofind = $templateParts['template'] . '.php';
		$this->_tempFilePath = \Awf\Utils\Path::find($paths, $filetofind);
		if ($this->_tempFilePath)
		{
			// Unset from local scope
			unset($template);
			unset($layoutTemplate);
			unset($paths);
			unset($path);
			unset($filetofind);

			// Never allow a 'this' property
			if (isset($this->this))
			{
				unset($this->this);
			}

			// Force parameters into scope
			if (!empty($forceParams))
			{
				extract($forceParams);
			}

			// Start capturing output into a buffer
			ob_start();
			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_tempFilePath;

			// Done with the requested template; get the buffer and
			// clear it.
			$this->output = ob_get_contents();
			ob_end_clean();

			return $this->output;
		}
		else
		{
			return new \Exception(\Awf\Text\Text::sprintf('AWF_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $path), 500);
		}
	}

	/**
	 * Get the layout template.
	 *
	 * @return  string  The layout template name
	 */
	public function getLayoutTemplate()
	{
		return $this->layoutTemplate;
	}

	/**
	 * Parses the template path
	 *
	 * @param   string $path The fancy path name to the layout file
	 *
	 * @return  array  The view and template of the layout path
	 */
	private function parseTemplatePath($path = '')
	{
		$parts = array(
			'view'     => $this->name,
			'template' => 'default'
		);

		if (empty($path))
		{
			return;
		}

		$pathparts = explode('/', $path, 2);
		switch (count($pathparts))
		{
			case 2:
				$parts['view'] = array_shift($pathparts);
			// DO NOT BREAK!

			case 1:
				$parts['template'] = array_shift($pathparts);
				break;
		}

		return $parts;
	}

	/**
	 * Load a helper file
	 *
	 * @param   string $helperClass    The last part of the name of the helper
	 *                                 class.
	 *
	 * @return  void
	 */
	public function loadHelper($helperClass = null)
	{
		// Get the helper class name
		$className = '\\' . ucfirst($this->container->application->getName()) . '\\Helper\\' . ucfirst($helperClass);

		// This trick autoloads the helper class. We can't instantiate it as
		// helpers are (supposed to be) abstract classes with static method
		// interfaces.
		class_exists($className);
	}

	/**
	 * Returns a reference to the container attached to this View
	 *
	 * @return \Awf\Container\Container
	 */
	public function &getContainer()
	{
		return $this->container;
	}

	public function getTask()
	{
		return $this->task;
	}

	/**
	 * @param   string  $task
	 *
	 * @return  $this   This for chaining
	 */
	public function setTask($task)
	{
		$this->task = $task;

		return $this;
	}

	public function getDoTask()
	{
		return $this->doTask;
	}

	/**
	 * @param   string  $task
	 *
	 * @return  $this   This for chaining
	 */
	public function setDoTask($task)
	{
		$this->doTask = $task;

		return $this;
	}
}
