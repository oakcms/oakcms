<?php
/**
 * @package        awf
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 *
 * This class is based on the JHtml package of Joomla! 3
 */

namespace Awf\Html;
use Awf\Text\Text;

/**
 * Administration grid actions abstraction
 */
abstract class Grid
{
	public static $javascriptPrefix = 'Solo.System.';

	/**
	 * Method to sort a column in a grid
	 *
	 * @param   string  $title          The link title
	 * @param   string  $order          The order field for the column
	 * @param   string  $direction      The current direction
	 * @param   string  $selected       The selected ordering
	 * @param   string  $task           An optional task override
	 * @param   string  $new_direction  An optional direction for the new column
	 * @param   string  $tip            An optional text shown as tooltip title instead of $title
	 * @param   string  $orderingJs     (optional) The Javascript function which handles table reordering, e.g. "Foobar.System.tableOrdering"
	 *
	 * @return  string
	 */
	public static function sort($title, $order, $direction = 'asc', $selected = '', $task = null, $new_direction = 'asc', $tip = '', $orderingJs = '')
	{
		$direction = strtolower($direction);

		$icon = array('caret-up', 'caret-down');
		$index = (int)($direction == 'desc');

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		if (empty($orderingJs))
		{
			$orderingJs = self::$javascriptPrefix . 'tableOrdering';
		}

		$html = '<a href="#" onclick="' . $orderingJs . '(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\');return false;"'
			. ' class="hasTooltip" title="' . Text::_($tip ? $tip : $title) . '">';

		$html .= Text::_($title);

		if ($order == $selected)
		{
			$html .= ' <span class="fa fa-' . $icon[$index] . '"></span>';
		}

		$html .= '</a>';

		return $html;
	}

	/**
	 * Method to check all checkboxes in a grid
	 *
	 * @param   string  $name    The name of the form element
	 * @param   string  $tip     The text shown as tooltip title instead of $tip
	 * @param   string  $action  The action to perform on clicking the checkbox, e.g. "Foobar.System.checkAll(this)"
	 *
	 * @return  string
	 */
	public static function checkAll($name = 'checkall-toggle', $tip = 'AWF_COMMON_LBL_CHECK_ALL', $action = '')
	{
		if (empty($action))
		{
			$action = self::$javascriptPrefix . 'checkAll(this)';
		}

		return '<input type="checkbox" name="' . $name . '" value="" class="hasTooltip" title="' . Html::tooltipText($tip) . '" onclick="' . $action . '" />';
	}

	/**
	 * Method to create a checkbox for a grid row.
	 *
	 * @param   integer $rowNum     The row index
	 * @param   integer $recId      The record id
	 * @param   boolean $checkedOut True if item is checke out
	 * @param   string  $name       The name of the form element
	 * @param   string  $checkedJs  (optional) The Javscript function to determine if a box is checked, e.g. "Foobar.system.isChecked"
	 *
	 * @return  mixed    String of html with a checkbox if item is not checked out, null if checked out.
	 */
	public static function id($rowNum, $recId, $checkedOut = false, $name = 'cid', $checkedJs = '')
	{
		if (empty($checkedJs))
		{
			$checkedJs = self::$javascriptPrefix . 'isChecked';
		}

		return $checkedOut ? '' : '<input type="checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId
			. '" onclick="' . $checkedJs . '(this.checked);" />';
	}
} 