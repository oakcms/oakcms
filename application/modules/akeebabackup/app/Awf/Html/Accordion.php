<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Html;

/**
 * An abstraction around Bootstrap collapsible panels (accordions)
 *
 * @see http://getbootstrap.com/javascript/#collapse
 *
 * Use:
 *      echo Accordion::start('myAccordion');
 *      echo Accordion::panel('My first panel', 'panel-first', 'myAccordion');
 *      echo "Some HTML for the first panel contents";
 *      echo Accordion::panel('My second panel', 'panel-second', 'myAccordion');
 *      echo "Some HTML for the second panel contents";
 *      echo Accordion::end();
 */
class Accordion
{
	/**
	 * Opens the current accordion group
	 *
	 * @param   string  $id  The ID of the accordion
	 *
	 * @return string
	 */
	public static function start($id)
	{
		return <<< HTML
<div class="panel-group" id="$id">
	<div style="display: none"><div><div>
HTML;

	}

	/**
	 * Close the current accordion group
	 *
	 * @return  string  HTML to close the accordion group
	 */
	public static function end()
	{
		return <<< HTML
			</div>
		</div>
	</div>
</div>
HTML;

	}

	/**
	 * @param   string   $title         The title HTML of this panel
	 * @param   string   $id            The ID of this panel
	 * @param   string   $accordionId   The ID of the accordion this panel belongs to
	 * @param   string   $panelStyle    The style of this panel (default, warning, info, success, danger)
	 * @param   boolean  $open          Is this panel open in the accordion?
	 */
	public static function panel($title, $id, $accordionId, $panelStyle = 'default', $open = false)
	{
		// Open a new panel inside the accordion
		$in = $open ? 'in' : '';

		echo <<< HTML
			</div>
		</div>
	</div>
	<div class="panel panel-$panelStyle">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#$accordionId" href="#$id">
					$title
				</a>
			</h4>
		</div>
		<div id="$id" class="panel-collapse collapse $in">
			<div class="panel-body">
HTML;

	}
} 