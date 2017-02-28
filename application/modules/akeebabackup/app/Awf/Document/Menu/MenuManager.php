<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Document\Menu;

use Awf\Container\Container;

/**
 * Class MenuManager
 *
 * @package Awf\Document\Menu
 */
class MenuManager
{

	/**
	 * The array holding the Item objects
	 *
	 * @var   array
	 */
	private $items = array();

	/**
	 * Holds the enabled status of different menus
	 *
	 * @var   array
	 */
	private $menuEnabledStatus = array();

	/**
	 * The Application this menu manager belongs to
	 *
	 * @var   Container
	 */
	private $container;

	public function __construct(Container &$container)
	{
		$this->container = $container;
	}

	/**
	 * Adds a menu item to the stack
	 *
	 * @param   Item $item The item to add
	 *
	 * @return  void
	 */
	public function addItem(Item $item)
	{
		$key = $item->getName();

		$this->items[$key] = $item;
	}

	/**
	 * Adds a menu item based on a raw definition
	 *
	 * @param   array $options The item definition variables
	 *
	 * @return  void
	 *
	 * @throws  \Exception  If the definition lacks mandatory values
	 */
	public function addItemFromDefinition(array $options)
	{
		$item = new Item($options, $this->container);

		$this->addItem($item);
	}

	/**
	 * Removes an item from the menus
	 *
	 * @param   Item $item The item to remove. Only its $name is read.
	 */
	public function removeItem(Item $item)
	{
		$name = $item->getName();

		$this->removeItemByName($name);
	}

	/**
	 * Removes an item from the menus given its name
	 *
	 * @param   string $name The name of the menu item to remove
	 */
	public function removeItemByName($name)
	{
		if (array_key_exists($name, $this->items))
		{
			unset ($this->items[$name]);
		}
	}

	/**
	 * Find and return an item by name
	 *
	 * @param   string $name The menu item's name
	 *
	 * @return  Item  A copy of the item
	 *
	 * @throws  \Exception  If not found
	 */
	public function findItem($name)
	{
		if (array_key_exists($name, $this->items))
		{
			return $this->items[$name];
		}
		else
		{
			throw new \Exception('Menu item not found', 500);
		}
	}

	/**
	 * Gets a hierarchical list of menu items
	 *
	 * @param   string $menu  Which menu to return the items for
	 * @param   string $group (optional) which group to return the items for
	 *
	 * @return  Item
	 */
	public function getMenuItems($menu, $group = '')
	{
		// Filter by menu and group
		$deck = array();

		/** @var   Item $item */
		foreach ($this->items as $key => $item)
		{
			$menus = $item->getShow();

			if (!in_array($menu, $menus))
			{
				continue;
			}

			if (!empty($group) && ($item->getGroup() != $group))
			{
				continue;
			}

			$deck[] = $item;
		}

		$ret = new Item(array('name' => '', 'title' => 'ROOT'), $this->container);

		$this->extractChildren($deck, $ret);

		return $ret;
	}

	/**
	 * Extracts the children elements of $parent from a $deck of menu items
	 *
	 * @param   array $deck   The deck of items to search for children
	 * @param   Item  $parent The parent element
	 */
	private function extractChildren(array &$deck, Item &$parent)
	{
		$children = array();

		if (empty($deck))
		{
			return;
		}

		/** @var   Item $item */
		foreach ($deck as $key => $item)
		{
			if ($item->getParent() == $parent->getName())
			{
				$children[] = $item;
				unset($deck[$key]);
			}
		}

		if (!empty($children))
		{
			uasort($children, array($this, 'compareItemOrder'));
		}

		$this->addChildrenToParent($parent, $children, $deck);
	}

	/**
	 * Adds the children items to the parent item, making sure any further
	 * generations will be added recursively.
	 *
	 * @param   Item  $parent   The parent item
	 * @param   array $children Its children
	 * @param   array $deck     The deck of remaining menu items
	 */
	private function addChildrenToParent(Item &$parent, array $children, array &$deck)
	{
		/** @var Item $child */
		foreach ($children as $child)
		{
			$this->extractChildren($deck, $child);

			$parent->addChild($child);
		}
	}

	/**
	 * Initialise the menu structure from the views found inside the directory
	 * $path. The view.json files are read to produce these menu items.
	 *
	 * @param   string  $path  The path to scan
	 * @param   boolean $reset Should I reset the existing items? Default = true
	 */
	public function initialiseFromDirectory($path, $reset = true)
	{
		$appName = $this->container->application->getName();

		if ($reset)
		{
			$this->items = array();
		}

		if (!is_dir($path))
		{
			return;
		}

		$di = new \DirectoryIterator($path);

		/** @var \DirectoryIterator $entry */
		foreach ($di as $entry)
		{
			// Ignore dot files
			if ($entry->isDot())
			{
				continue;
			}

			// Ignore non-directory entities
			if (!$entry->isDir())
			{
				continue;
			}

			// Look for a view.json file and try to load it
			$sourceFile = $di->getPath() . '/' . $di->getFilename() . '/view.json';

			$options = array('show' => array('main'));

			if (file_exists($sourceFile))
			{
				// Load the view.json file
				$jsonData = @file_get_contents($sourceFile);
				$options = json_decode($jsonData, true);

				if (!is_array($options) || empty($options))
				{
					$options = array('show' => array('main'));
				}
			}

			// If show is empty or doesn't exist, ignore this view
			if (!array_key_exists('show', $options))
			{
				continue;
			}
			elseif (empty($options['show']))
			{
				continue;
			}

			$viewName = strtolower($di->getFilename());

			// If there are no params set, try to guess!
			if (!array_key_exists('params', $options))
			{
				$options['params'] = array(
					'view' => $viewName,
				);
			}

			// If there is no name set, try to guess!
			if (!array_key_exists('name', $options))
			{
				$options['name'] = $viewName;
			}

			// If there is no title set, try to guess!
			if (!array_key_exists('title', $options))
			{
				$options['title'] = strtoupper($appName . '_' . $viewName . '_TITLE');
			}

			// If there is no order set, use 0
			if (!array_key_exists('order', $options))
			{
				$options['order'] = 0;
			}

			if (array_key_exists('privileges', $options) && is_array($options['privileges']))
			{
				$user = $this->container->userManager->getUser();
				$allow = true;

				foreach ($options['privileges'] as $privilege)
				{
					if (!$user->getPrivilege($privilege, true))
					{
						$allow = false;
						break;
					}
				}

				if (!$allow)
				{
					continue;
				}
			}

			// Create a menu item
			$item = new Item($options, $this->container);

			// Add the menu item
			$this->addItem($item);
		}
	}

	/**
	 * Is a particular menu enabled?
	 *
	 * @param   string $menu The menu to check
	 *
	 * @return  boolean
	 */
	public function isEnabled($menu)
	{
		if (!array_key_exists($menu, $this->menuEnabledStatus))
		{
			$session = $this->container->segment;
			$this->menuEnabledStatus[$menu] = $session->getFlash('menu.' . $menu . '.enabled');

			if (is_null($this->menuEnabledStatus[$menu]))
			{
				$this->menuEnabledStatus[$menu] = true;
			}
		}

		return (bool)$this->menuEnabledStatus[$menu];
	}

	/**
	 * Disables a menu
	 *
	 * @param   string $menu Which menu to disable
	 */
	public function disableMenu($menu = 'main')
	{
		$this->menuEnabledStatus[$menu] = false;
	}

	/**
	 * Enables a menu
	 *
	 * @param   string $menu Which menu to enable
	 */
	public function enableMenu($menu = 'main')
	{
		$this->menuEnabledStatus[$menu] = true;
	}

	/**
	 * Removes all menu items
	 */
	public function clear()
	{
		$this->items = array();
	}

	/**
	 * Compares two menu items and returns their sorting relation to each other
	 *
	 * @param   Item $a First item
	 * @param   Item $b Second item
	 *
	 * @return  integer  0 if order A = order B, -1 if order A < order B, 1 if order A > order B
	 */
	protected function compareItemOrder($a, $b)
	{
		$orderA = $a->getOrder();
		$orderB = $b->getOrder();

		if ($orderA == $orderB)
		{
			return 0;
		}

		return ($orderA < $orderB) ? -1 : 1;
	}
} 