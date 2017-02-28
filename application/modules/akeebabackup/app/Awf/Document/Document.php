<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Document;

use Awf\Container\Container;
use Awf\Document\Menu\MenuManager;
use Awf\Document\Toolbar\Toolbar;

/**
 * Class Document
 *
 * Generic output document implementation
 *
 * @package Awf\Document
 */
abstract class Document
{

	/** @var   string  The output data buffer */
	protected $buffer = '';

	/** @var   array  An array of all externally defined JavaScript files */
	protected $scripts = array();

	/** @var   array  An array of all inline JavaScript scripts */
	protected $scriptDeclarations = array();

	/** @var   array  An array of all external CSS files */
	protected $styles = array();

	/** @var   array  An array of all inline CSS styles */
	protected $styleDeclarations = array();

	/** @var   array  Cache of all document instances known to us */
	private static $instances = array();

	/** @var   MenuManager  The menu manager for this document */
	protected $menu;

	/** @var   Toolbar  The toolbar for this document */
	protected $toolbar;

	/** @var   Container  The container this menu manager is attached to */
	protected $container;

	/** @var   string  The MIME type of the request */
	protected $mimeType = 'text/html';

	/** @var   array  Optional HTTP headers to send right before rendering */
	protected $HTTPHeaders = array();

	/** @var   null|string  The base name of the returned document. If set, the browser will initiate a download instead of displaying content inline. */
	protected $name = null;

	public function __construct(Container $container)
	{
		$viewPath = $container->basePath . '/View';
		$viewPath_alt = $container->basePath . '/views';

		$this->menu = new MenuManager($container);
		$this->menu->initialiseFromDirectory($viewPath);
		$this->menu->initialiseFromDirectory($viewPath_alt, false);

		$this->toolbar = new Toolbar($container);

		$this->container = $container;
	}

	/**
	 * Return the static instance of the document
	 *
	 * @param   string    $type        The document type (html or json)
	 * @param   Container $container   The application to which the document is attached
	 * @param   string    $classPrefix The prefix of the document class to use
	 *
	 * @return  \Awf\Document\Document
	 */
	public static function getInstance($type = 'html', Container $container, $classPrefix = '\\Awf')
	{
		if (!array_key_exists($type, self::$instances))
		{
			$className = $classPrefix . '\\Document\\' . ucfirst($type);

			if (!class_exists($className))
			{
				$className = '\\Awf\\Document\\Html';
			}

			self::$instances[$type] = new $className($container);
		}

		return self::$instances[$type];
	}

	/**
	 * Sets the buffer (contains the main content of the HTML page or the entire JSON response)
	 *
	 * @param   string $buffer
	 *
	 * @return  \Awf\Document\Document
	 */
	public function setBuffer($buffer)
	{
		$this->buffer = $buffer;

		return $this;
	}

	/**
	 * Returns the contents of the buffer
	 *
	 * @return  string
	 */
	public function getBuffer()
	{
		return $this->buffer;
	}

	/**
	 * Adds an external script to the page
	 *
	 * @param   string  $url    The URL of the script file
	 * @param   boolean $before (optional) Should I add this before the template's scripts?
	 * @param   string  $type   (optional) The MIME type of the script file
	 *
	 * @return  \Awf\Document\Document
	 */
	public function addScript($url, $before = false, $type = "text/javascript")
	{
		$this->scripts[$url]['mime'] = $type;
		$this->scripts[$url]['before'] = $before;

		return $this;
	}

	/**
	 * Adds an inline script to the page's header
	 *
	 * @param   string $content The contents of the script (without the script tag)
	 * @param   string $type    (optional) The MIME type of the script data
	 *
	 * @return  \Awf\Document\Document
	 */
	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		if (!isset($this->scriptDeclarations[strtolower($type)]))
		{
			$this->scriptDeclarations[strtolower($type)] = $content;
		}
		else
		{
			$this->scriptDeclarations[strtolower($type)] .= chr(13) . $content;
		}

		return $this;
	}

	/**
	 * Adds an external stylesheet to the page
	 *
	 * @param   string  $url    The URL of the stylesheet file
	 * @param   boolean $before (optional) Should I add this before the template's scripts?
	 * @param   string  $type   (optional) The MIME type of the stylesheet file
	 * @param   string  $media  (optional) The media target of the stylesheet file
	 *
	 * @return  \Awf\Document\Document
	 */
	public function addStyleSheet($url, $before = false, $type = 'text/css', $media = null)
	{
		$this->styles[$url]['mime'] = $type;
		$this->styles[$url]['media'] = $media;
		$this->styles[$url]['before'] = $before;

		return $this;
	}

	/**
	 * Adds an inline stylesheet to the page's header
	 *
	 * @param   string $content The contents of the stylesheet (without the style tag)
	 * @param   string $type    (optional) The MIME type of the stylesheet data
	 *
	 * @return  \Awf\Document\Document
	 */
	public function addStyleDeclaration($content, $type = 'text/css')
	{
		if (!isset($this->styleDeclarations[strtolower($type)]))
		{
			$this->styleDeclarations[strtolower($type)] = $content;
		}
		else
		{
			$this->styleDeclarations[strtolower($type)] .= chr(13) . $content;
		}

		return $this;
	}

	/**
	 * Return the array with external scripts
	 *
	 * @return  array
	 */
	public function getScripts()
	{
		return $this->scripts;
	}

	/**
	 * Return the array with script declarations
	 *
	 * @return  array
	 */
	public function getScriptDeclarations()
	{
		return $this->scriptDeclarations;
	}

	/**
	 * Return the array with external stylesheets
	 *
	 * @return  array
	 */
	public function getStyles()
	{
		return $this->styles;
	}

	/**
	 * Return the array with style declarations
	 *
	 * @return  array
	 */
	public function getStyleDeclarations()
	{
		return $this->styleDeclarations;
	}

	/**
	 * Each document class implements its own renderer which outputs the buffer
	 * to the browser using the appropriate template.
	 *
	 * @return  void
	 */
	abstract public function render();

	/**
	 * Returns an instance of the menu manager
	 *
	 * @return  MenuManager
	 */
	public function &getMenu()
	{
		return $this->menu;
	}

	/**
	 * Returns a reference to our Toolbar object
	 *
	 * @return Toolbar
	 */
	public function &getToolbar()
	{
		return $this->toolbar;
	}

	/**
	 * Returns a reference to our Application object
	 *
	 * @return \Awf\Application\Application
	 */
	public function getApplication()
	{
		return $this->container->application;
	}

	/**
	 * Returns a reference to our Container object
	 *
	 * @return \Awf\Container\Container
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Set the MIME type of the document
	 *
	 * @param   string $mimeType
	 */
	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;
	}

	/**
	 * Get the MIME type of the document
	 *
	 * @return  string
	 */
	public function getMimeType()
	{
		return $this->mimeType;
	}

	/**
	 * Add an HTTP header
	 *
	 * @param   string  $header    The HTTP header to add, e.g. Content-Type
	 * @param   string  $content   The content of the HTTP header, e.g. text/plain
	 * @param   boolean $overwrite Should I overwrite an existing header?
	 *
	 * @return  void
	 */
	public function addHTTPHeader($header, $content, $overwrite = true)
	{
		if (!$overwrite && isset($this->HTTPHeaders[$header]))
		{
			return;
		}

		$this->HTTPHeaders[$header] = $content;
	}

	/**
	 * Remove an HTTP header if set
	 *
	 * @param   string $header The header to remove, e.g. Content-Type
	 *
	 * @return  void
	 */
	public function removeHTTPHeader($header)
	{
		if (isset($this->HTTPHeaders[$header]))
		{
			unset($this->HTTPHeaders[$header]);
		}
	}

	/**
	 * Get the contents of an HTTP header defined in the document
	 *
	 * @param   string $header  The HTTP header to return
	 * @param   string $default The default value if it's not already set
	 *
	 * @return  string  The HTTP header's value
	 */
	public function getHTTPHeader($header, $default = null)
	{
		if (isset($this->HTTPHeaders[$header]))
		{
			return $this->HTTPHeaders[$header];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Returns the raw HTTP headers as a hash array
	 *
	 * @return array Key = header, value = header value.
	 */
	public function getHTTPHeaders()
	{
		return $this->HTTPHeaders;
	}

	/**
	 * Output the HTTP headers to the browser
	 *
	 * @return  void
	 */
	public function outputHTTPHeaders()
	{
		if (!empty($this->HTTPHeaders) && !headers_sent())
		{
			foreach ($this->HTTPHeaders as $header => $value)
			{
				if (substr($header, 0, 5) == 'HTTP/')
				{
					header($header . ' ' . $value);
				}
				else
				{
					header($header . ': ' . $value);
				}
			}
		}
	}

	/**
	 * Set the document's name
	 *
	 * @param   null|string $name
	 *
	 * @return  void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Get the document's name
	 *
	 * @return  null|string
	 */
	public function getName()
	{
		return $this->name;
	}
}