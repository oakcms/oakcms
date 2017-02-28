<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Document\Menu;

use Awf\Container\Container;
use Awf\Router\Router;
use Awf\Uri\Uri;

/**
 * Class Item
 *
 * Definition of a menu item
 *
 * @package Awf\Document\Menu
 */
class Item
{

	/**
	 * The name of this menu item
	 *
	 * @var   string
	 */
	private $name = '';

	/**
	 * The title of this menu item
	 *
	 * @var   string
	 */
	private $title = '';

	/**
	 * URL parameters for this menu item
	 *
	 * @var   array
	 */
	private $params = array();

	/**
	 * A custom URL for this menu item
	 *
	 * @var   string
	 */
	private $url = '';

	/**
	 * The parent menu item
	 *
	 * @var   string
	 */
	private $parent = '';

	/**
	 * Which menus to show this item under
	 *
	 * @var   array
	 */
	private $show = array('main');

	/**
	 * Icon classes (for use in CPanel views)
	 *
	 * @var   string
	 */
	private $icon = '';

	/**
	 * The icon group this belongs to (for use in CPanel views)
	 *
	 * @var   string
	 */
	private $group = '';

	/**
	 * The onClick event handler of this menu item
	 *
	 * @var   string
	 */
	private $onClick = '';

	/**
	 * A PHP function (string) or class and method (array) name which returns
	 * the title of the menu item.
	 *
	 * @var   string|array
	 */
	private $titleHandler = '';

	/**
	 * The ordering of the items in the menu
	 *
	 * @var   integer
	 */
	private $order = 0;

	/**
	 * Children menu items
	 *
	 * @var   array
	 */
	private $children = array();

	/**
	 * The container this menu item belongs to
	 *
	 * @var \Awf\Container\Container|null
	 */
	private $container = null;

	/**
	 * Public constructor
	 *
	 * @param   array     $options   The configuration parameters of this menu item
	 * @param   Container $container The container this menu item belongs to
	 *
	 * @throws  \Exception  When basic parameters are missing
	 */
	public function __construct(array $options, Container $container)
	{
		$this->container = $container;

		foreach ($options as $k => $v)
		{
			$method = 'set' . ucfirst($k);

			if (method_exists($this, $method))
			{
				$this->$method($v);
			}
		}

		if (empty($this->name) && ($this->title != 'ROOT'))
		{
			throw new \Exception('A menu item must have a name', 500);
		}

		if (empty($this->title) && empty($this->titleHandler))
		{
			throw new \Exception('A menu item must have a title or a title handler', 500);
		}
	}

	/**
	 * Sets the group for this menu item
	 *
	 * @param   string $group
	 *
	 * @return  void
	 */
	public function setGroup($group)
	{
		$this->group = $group;
	}

	/**
	 * Returns the group of this menu item
	 *
	 * @return  string
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * Sets the icon classes
	 *
	 * @param   string $icon
	 *
	 * @return  void
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
	}

	/**
	 * Returns the icon classes
	 *
	 * @return  string
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * Sets the name of the menu item
	 *
	 * @param   string $name
	 *
	 * @return  void
	 */
	public function setName($name)
	{
		$filter = new \Awf\Input\Filter();
		$this->name = $filter->clean($name, 'cmd');
	}

	/**
	 * Returns the name of the menu item
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the handler for this menu item's title
	 *
	 * @param   string $titleHandler
	 *
	 * @return  void
	 */
	public function setTitleHandler($titleHandler)
	{
		if (is_string($titleHandler))
		{
			$this->titleHandler = $titleHandler;
		}
		elseif (is_array($titleHandler))
		{
			if (count($titleHandler) >= 2)
			{
				$class = array_shift($titleHandler);
				$method = array_shift($titleHandler);
				$this->titleHandler = array($class, $method);
			}
		}
		else
		{
			$this->titleHandler = '';
		}
	}

	/**
	 * Get the title handler
	 *
	 * @return  string
	 */
	public function getTitleHandler()
	{
		return $this->titleHandler;
	}

	/**
	 * Set the click handler
	 *
	 * @param   string $onClick
	 *
	 * @return  void
	 */
	public function setOnClick($onClick)
	{
		$this->onClick = $onClick;
	}

	/**
	 * Get the click handler
	 *
	 * @return  string
	 */
	public function getOnClick()
	{
		return $this->onClick;
	}

	/**
	 * Set the parent menu item
	 *
	 * @param   string $parent
	 *
	 * @return  void
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * Get the parent menu item
	 *
	 * @return  string
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Set the menus this menu item is visible in
	 *
	 * @param   array   $show The menus this item is visible in
	 * @param   boolean $add  When true the $show items will be added, otherwise will replace existing items
	 *
	 * @return  void
	 */
	public function setShow($show, $add = false)
	{
		if (is_string($show))
		{
			$show = array($show);
		}

		$this->show = $show;
	}

	/**
	 * Get the menus this menu item is visible in
	 *
	 * @return  array
	 */
	public function getShow()
	{
		return $this->show;
	}

	/**
	 * Set the title of this menu item
	 *
	 * @param   string $title
	 *
	 * @return  void
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Get the title of this menu item
	 *
	 * @return  string
	 */
	public function getTitle()
	{
		if (empty($this->title) && !empty($this->titleHandler))
		{
			$titleHandler = $this->titleHandler;

			if (is_string($titleHandler))
			{
				$this->title = $titleHandler($this);
			}
			else
			{
				$this->title = call_user_func($titleHandler, $this);
			}
		}

		return $this->title;
	}

	/**
	 * Set the custom URL
	 *
	 * @param   string $url
	 *
	 * @return  void
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * Get the URL associated with this menu item. If no custom URL is
	 * specified we construct a URL based on the URL query params.
	 *
	 * @return  string
	 */
	public function getUrl()
	{
		if (!empty($this->url))
		{
			return $this->url;
		}
		else
		{
			$router = $this->container->router;
			$tempUrl = 'index.php?' . http_build_query($this->params);

			return $router->route($tempUrl);
		}
	}

	/**
	 * Set the order of a mneu item
	 *
	 * @param   integer $order The new order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}

	/**
	 * Get the order of a menu item
	 *
	 * @return   integer
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * Adds a child menu item
	 *
	 * @param   Item $item
	 *
	 * @return  void
	 */
	public function addChild(Item $item)
	{
		$key = $item->getName();

		$this->children[$key] = $item;
	}

	/**
	 * Remove a child menu item
	 *
	 * @param   Item $item
	 *
	 * @return  void
	 */
	public function removeChild(Item $item)
	{
		$key = $item->getName();

		if (!array_key_exists($key, $this->children))
		{
			return;
		}

		unset($this->children[$key]);
	}

	/**
	 * Reset the children items cache
	 *
	 * @return  void
	 */
	public function resetChildren()
	{
		$this->children = array();
	}

	/**
	 * Return all children items
	 *
	 * @return  array
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * Sets the menu item URL parameters
	 *
	 * @param   array $params
	 */
	public function setParams($params)
	{
		$this->params = $params;
	}

	/**
	 * Returns the menu item's URL parameters
	 *
	 * @param   boolean $asQueryString Return the parameters in query string format
	 *
	 * @return  array|string
	 */
	public function getParams($asQueryString = false)
	{
		// Should I just return the raw array?
		if (!$asQueryString)
		{
			return $this->params;
		}

		// Construct a query string
		$parts = array();

		foreach ($this->params as $k => $v)
		{
			$parts[] = urlencode($k) . '=' . urlencode($v);
		}

		return implode('&', $parts);
	}

	/**
	 * Is this menu item the active one?
	 *
	 * @return  boolean
	 */
	public function isActive()
	{
		// Get the current URI
		$uri = Uri::getInstance();

		// If we have an exact match to the custom URL, return true
		if ($uri->toString() == $this->url)
		{
			return true;
		}

		// If there are no parameters to check and the URLs don't match, it's not an active menu item
		if (empty($this->params))
		{
			return false;
		}

		// Otherwise check if the parameters match
		foreach ($this->params as $k => $v)
		{
			$uv = $uri->getVar($k, null);

			if ($uv != $v)
			{
				return false;
			}
		}

		return true;
	}
} 