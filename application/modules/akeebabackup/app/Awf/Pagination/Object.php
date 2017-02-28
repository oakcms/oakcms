<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Pagination;


class Object
{
	/**
	 * @var    string  The link text.
	 */
	public $text;

	/**
	 * @var    integer  The number of rows as a base offset.
	 */
	public $base;

	/**
	 * @var    string  The link URL.
	 */
	public $link;

	/**
	 * @var    boolean  Flag whether the object is the 'active' page
	 */
	public $active;

	/**
	 * Class constructor.
	 *
	 * @param   string   $text    The link text.
	 * @param   integer  $base    The number of rows as a base offset.
	 * @param   string   $link    The link URL.
	 * @param   boolean  $active  Flag whether the object is the 'active' page
	 */
	public function __construct($text, $base = null, $link = null, $active = false)
	{
		$this->text   = $text;
		$this->base   = $base;
		$this->link   = $link;
		$this->active = $active;
	}
} 