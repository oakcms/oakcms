<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Html;

/**
 * An abstraction around Bootstrap tabs / pills
 *
 * @see http://getbootstrap.com/javascript/#tabs
 *
 * Use:
 *      echo Tabs::start();
 *      echo Tabs::addNav('tab-one', 'First tab');
 *      echo Tabs::addNav('tab-two', 'Second tab');
 *      echo Tabs::startContent();
 *      echo Tabs::tab('tab-one');
 *      echo "HTML for the first tab";
 *      echo Tabs::tab('tab-two');
 *      echo "HTML for the second tab";
 *      echo Tabs::end();
 */
class Tabs
{
	/**
	 * Start a new tabbed area
	 *
	 * @param   boolean  $pills  Should I use the pill navigation style? Otherwise tabs will be used.
	 *
	 * @return  string  The HTML
	 */
	public static function start($pills = false)
	{
		$type = $pills ? 'pills' : 'nav';

		return <<< HTML
<ul class="nav nav-$type">
HTML;

	}

	/**
	 * Add one more tab/pill in the navigation section of the tabbed area
	 *
	 * @param   string  $id     The HTML ID of the tab content area opened by this tab/pill
	 * @param   string  $title  The title of the tab/pill
	 *
	 * @return  string  The HTML
	 */
	public static function addNav($id, $title)
	{
		return <<< HTML
	<li><a href="#$id" data-toggle="tab">$title</a></li>
HTML;

	}

	/**
	 * Closes the navigation area of the tabs and starts the content area
	 *
	 * @return  string  The HTML
	 */
	public static function startContent()
	{
		return <<< HTML
</ul>
<div class="tab-content">
 	<div style="display:none">
HTML;

	}

	/**
	 * Starts the content section of a tab
	 *
	 * @param   string   $id      The HTML ID of this tab content. Must match what you previously used in addNav
	 * @param   boolean  $active  Is this tab active by default?
	 * @param   boolean  $fade    Should we use a fade transition effect?
	 *
	 * @return  string  The HTML
	 */
	public static function tab($id, $active = false, $fade = false)
	{
		$activeString = $active ? 'active' . ($fade ? ' in' : '') : '';
		$fadeString = $fade ? 'fade' : '';

		return <<< HTML
	<div class="tab-pane $activeString $fadeString" id="$id">
HTML;

	}

	/**
	 * Ends the tabbed area
	 *
	 * @return  string  The HTML
	 */
	public static function end()
	{
		return <<< HTML
	</div>
</div>
HTML;

	}
} 