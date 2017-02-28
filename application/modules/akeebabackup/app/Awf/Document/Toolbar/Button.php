<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Document\Toolbar;
use Awf\Input\Filter;

/**
 * Class Button
 *
 * Definition of a toolbar button
 *
 * @package   Awf\Document\Toolbar
 */
class Button
{
	/**
	 * The class of the button. Usually used to define the color of the button.
	 *
	 * @var   string
	 */
	private $class = '';

	/**
	 * The class of the button's icon
	 *
	 * @var   string
	 */
	private $icon = '';

	/**
	 * The title of the button
	 *
	 * @var   string
	 */
	private $title = '';

	/**
	 * The DOM identifier of the button
	 *
	 * @var   string
	 */
	private $id = '';

	/**
	 * The onClick event of the button
	 *
	 * @var   string
	 */
	private $onClick = '';

	/**
	 * A custom URL to visit when the button's clicked. Overrides the onClick
	 * handler.
	 *
	 * @var   string
	 */
	private $url = '';

	/**
	 * Public constructor
	 *
	 * @param   array  $options  The configuration parameters of this menu item
	 *
	 * @throws  \Exception  When basic parameters are missing
	 */
	public function __construct(array $options)
	{
		foreach ($options as $k => $v)
		{
			$method = 'set' . ucfirst($k);

			if (method_exists($this, $method))
			{
				$this->$method($v);
			}
		}
	}

	/**
	 * Sets the button's class
	 *
	 * @param   string  $class
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setClass($class)
	{
		$this->class = $class;
	}

	/**
	 * Gets the button's class
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * Sets the button icon's class
	 *
	 * @param   string  $icon
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
	}

	/**
	 * Gets the button icon's class
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * Sets the button's DOM identifier
	 *
	 * @param   string  $id
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Gets the button's DOM identifier
	 *
	 * @return  string
	 */
	public function getId()
	{
		if (empty($this->id))
		{
			$filter = new Filter();
			$this->id = $filter->clean($this->getTitle(), 'cmd');
		}

		return $this->id;
	}

	/**
	 * Sets the button's onClick handler
	 *
	 * @param   string  $onClick
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setOnClick($onClick)
	{
		$this->onClick = $onClick;
	}

	/**
	 * Gets the button's onClick handler
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	public function getOnClick()
	{
		return $this->onClick;
	}

	/**
	 * Sets the button's title (raw key)
	 *
	 * @param   string  $title
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
	 * Gets the button's title (raw key)
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
	 * Sets the button's direct URL
	 *
	 * @param   string  $url
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * Gets the button's direct URL
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	public function getUrl()
	{
		return $this->url;
	}
}