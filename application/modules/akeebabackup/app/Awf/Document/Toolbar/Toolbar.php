<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Document\Toolbar;

use Awf\Container\Container;
use Awf\Document\Menu\Item;

/**
 * Class Toolbar
 *
 * A simple toolbar handler. It allows you to define the title, subtitle,
 * submenu and toolbar buttons to display in an application
 *
 * @package   Awf\Document\Toolbar
 */
class Toolbar
{
	/**
	 * The title of the page
	 *
	 * @var   string
	 */
	private $title = '';

	/**
	 * An array of button definitions
	 *
	 * @var   array[Button]
	 */
	private $buttons = array();

	/**
	 * The container we are attached to
	 *
	 * @var   Container
	 */
	private $container;

	/**
	 * Public constructor
	 *
	 * @param Container $container The container we are attached to
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Sets the title of the application
	 *
	 * @param   string $title
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Gets the title of the application
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Adds a menu item to the application submenu
	 *
	 * @param   Item $item
	 *
	 * @return  void
	 */
	public function addSubmenu(Item $item)
	{
		// Make sure the name begins with "submenu_"
		$name = $item->getName();

		if (strpos($name, 'submenu_') !== 0)
		{
			$item->setName('submenu_' . $name);
		}

		// Make sure the parent menu item begins with "submenu_"
		$parent = $item->getParent();

		if (!empty($parent) && (strpos($parent, 'submenu_') !== 0))
		{
			$item->setParent('submenu_' . $parent);
		}

		// Set the item to only show in the submenu
		$item->setShow(array('submenu'));

		$this->container->application->getDocument()->getMenu()->addItem($item);
	}

	/**
	 * Adds a menu item to the application submenu given a definition in
	 * hashed array format.
	 *
	 * @param   array $options
	 *
	 * @return  void
	 */
	public function addSubmenuFromDefinition($options)
	{
		$item = new Item($options, $this->container);

		$this->addSubmenu($item);
	}

	/**
	 * Remove a submenu item
	 *
	 * @param   Item $item The submenu item to remove
	 *
	 * @return  void
	 */
	public function removeSubmenu(Item $item)
	{
		$this->removeSubmenuByName($item->getName());
	}

	/**
	 * Remove a submenu item by its name
	 *
	 * @param   $name
	 *
	 * @return  void
	 */
	public function removeSubmenuByName($name)
	{
		if (strpos($name, 'submenu_') !== 0)
		{
			$name = 'submenu_' . $name;
		}

		$this->container->application->getDocument()->getMenu()->removeItemByName($name);
	}

	/**
	 * Returns the submenu. You get a root node item which you're supposed to
	 * iterate recursively to build the actual submenu.
	 *
	 * @return   Item
	 */
	public function getSubmenu()
	{
		return $this->container->application->getDocument()->getMenu()->getMenuItems('submenu');
	}

	/**
	 * Set a list of toolbar buttons, replacing the existing set
	 *
	 * @param   array $buttons
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setButtons($buttons)
	{
		$this->buttons = $buttons;
	}

	/**
	 * Clear the list of toolbar buttons
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function clearButtons()
	{
		$this->buttons = array();
	}

	/**
	 * Get the toolbar buttons
	 *
	 * @return  array
	 *
	 * @codeCoverageIgnore
	 */
	public function getButtons()
	{
		return $this->buttons;
	}

	/**
	 * Adds a toolbar button
	 *
	 * @param   Button $button
	 *
	 * @return  void
	 */
	public function addButton(Button $button)
	{
		$key = $button->getId();

		$this->buttons[$key] = $button;
	}

	/**
	 * Adds a toolbar button from an array definition of its parameters
	 *
	 * @param   array $options
	 *
	 * @return  void
	 */
	public function addButtonFromDefinition(array $options)
	{
		$button = new Button($options);

		$this->addButton($button);
	}

	/**
	 * Removes a button from the toolbar
	 *
	 * @param   Button $button
	 *
	 * @return  void
	 */
	public function removeButton(Button $button)
	{
		$id = $button->getId();

		$this->removeButtonByName($id);
	}

	/**
	 * Removes a toolbar button by its name
	 *
	 * @param   string $name
	 *
	 * @return  void
	 */
	public function removeButtonByName($name)
	{
		if (array_key_exists($name, $this->buttons))
		{
			unset ($this->buttons[$name]);
		}
	}

	/**
	 * Returns a button by name
	 *
	 * @param   string $name
	 *
	 * @return  Button
	 *
	 * @throws  \Exception  When not found
	 */
	public function findButton($name)
	{
		if (array_key_exists($name, $this->buttons))
		{
			return $this->buttons[$name];
		}
		else
		{
			throw new \Exception('Menu item not found', 500);
		}
	}
}