<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * This class is based on the JHtml package of Joomla! 3 but heavily modified
 */

namespace Awf\Html;


use Awf\Application\Application;
use Awf\Date\Date;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\Utils\ArrayHelper;

/**
 * Generic HTML output abstraction class
 */
abstract class Html
{
	/**
	 * Option values related to the generation of HTML output. Recognized
	 * options are:
	 *     fmtDepth, integer. The current indent depth.
	 *     fmtEol, string. The end of line string, default is linefeed.
	 *     fmtIndent, string. The string to use for indentation, default is
	 *     tab.
	 *
	 * @var    array
	 */
	public static $formatOptions = array('format.depth' => 0, 'format.eol' => "\n", 'format.indent' => "\t");

	/**
	 * Set format related options.
	 *
	 * Updates the formatOptions array with all valid values in the passed array.
	 *
	 * @param   array  $options  Option key/value pairs.
	 *
	 * @return  void
	 *
	 * @see     Html::$formatOptions
	 */
	public static function setFormatOptions($options)
	{
		foreach ($options as $key => $val)
		{
			if (isset(static::$formatOptions[$key]))
			{
				static::$formatOptions[$key] = $val;
			}
		}
	}

	/**
	 * Write a <a></a> element
	 *
	 * @param   string  $url      The relative URL to use for the href attribute
	 * @param   string  $text     The target attribute to use
	 * @param   array   $attribs  An associative array of attributes to add
	 *
	 * @return  string  <a></a> string
	 */
	public static function link($url, $text, $attribs = null)
	{
		if (is_array($attribs))
		{
			$attribs = ArrayHelper::toString($attribs);
		}

		return '<a href="' . $url . '" ' . $attribs . '>' . $text . '</a>';
	}

	/**
	 * Write a <iframe></iframe> element
	 *
	 * @param   string  $url       The relative URL to use for the src attribute.
	 * @param   string  $name      The target attribute to use.
	 * @param   array   $attribs   An associative array of attributes to add.
	 * @param   string  $noFrames  The message to display if the iframe tag is not supported.
	 *
	 * @return  string  <iframe></iframe> element or message if not supported.
	 */
	public static function iframe($url, $name, $attribs = null, $noFrames = '')
	{
		if (is_array($attribs))
		{
			$attribs = ArrayHelper::toString($attribs);
		}

		return '<iframe src="' . $url . '" ' . $attribs . ' name="' . $name . '">' . $noFrames . '</iframe>';
	}

	/**
	 * Returns formatted date according to a given format and time zone.
	 *
	 * @param   string       $input      String in a format accepted by date(), defaults to "now".
	 * @param   string       $format     The date format specification string (see {@link PHP_MANUAL#date}).
	 * @param   mixed        $tz         Time zone to be used for the date.  Special cases: boolean true for user
	 *                                   setting, boolean false for server setting.
	 * @param   Application  $app        The application from which we'll retrieve settings, null to use the default app
	 *
	 * @return  string    A date translated by the given format and time zone.
	 *
	 * @see     strftime
	 */
	public static function date($input = 'now', $format = null, $tz = true, Application $app = null)
	{
		if (!is_object($app))
		{
			$app = Application::getInstance();
		}

		// Get some system objects.
		$config = $app->getContainer()->appConfig;
		$userManager = $app->getContainer()->userManager;
		$user = $userManager->getUser();

		// UTC date converted to user time zone.
		if ($tz === true)
		{
			// Get a date object based on UTC.
			$date = new Date($input, 'UTC');

			// Set the correct time zone based on the user configuration.
			$date->setTimeZone(new \DateTimeZone($user->getParameters()->get('timezone', $config->get('timezone'))));
		}
		// UTC date converted to server time zone.
		elseif ($tz === false)
		{
			// Get a date object based on UTC.
			$date = new Date($input, 'UTC');

			// Set the correct time zone based on the server configuration.
			$date->setTimeZone(new \DateTimeZone($config->get('timezone', 'UTC')));
		}
		// No date conversion.
		elseif ($tz === null)
		{
			$date = new Date($input);
		}
		// UTC date converted to given time zone.
		else
		{
			// Get a date object based on UTC.
			$date = new Date($input, 'UTC');

			// Set the correct time zone based on the server configuration.
			$date->setTimeZone(new \DateTimeZone($tz));
		}

		// If no format is given use the default locale based format.
		if (!$format)
		{
			$format = Text::_('DATE_FORMAT_LC1');
		}
		// $format is an existing language key
		elseif (Text::hasKey($format))
		{
			$format = Text::_($format);
		}

		return $date->format($format, true);
	}

	/**
	 * Write a <img></img> element
	 *
	 * @param   string       $file      The relative or absolute URL to use for the src attribute.
	 * @param   string       $alt       The alt text.
	 * @param   mixed        $attribs   String or associative array of attribute(s) to use.
	 * @param   boolean      $relative  Path to file is relative to /media folder
	 * @param   Application  $app       The application to get configuration from
	 *
	 * @return  string
	 */
	public static function image($file, $alt, $attribs = null, $relative = false, Application $app = null)
	{
		if ($relative)
		{
			if (!is_object($app))
			{
				$app = Application::getInstance();
			}

			$file = Uri::base(false, $app->getContainer()) . 'media/' . ltrim($file, '/');
		}

		return '<img src="' . $file . '" alt="' . $alt . '" '
		. trim((is_array($attribs) ? ArrayHelper::toString($attribs) : $attribs) . ' /')
		. '>';
	}

	/**
	 * Creates a tooltip with an image as button
	 *
	 * @param   string       $tooltip  The tip string.
	 * @param   mixed        $title    The title of the tooltip or an associative array with keys contained in
	 *                                 {'title','image','text','href','alt'} and values corresponding to parameters of the same name.
	 * @param   string       $image    The image for the tip, if no text is provided.
	 * @param   string       $text     The text for the tip.
	 * @param   string       $href     An URL that will be used to create the link.
	 * @param   string       $alt      The alt attribute for img tag.
	 * @param   string       $class    CSS class for the tool tip.
	 * @param   Application  $app       The application to get configuration from
	 *
	 * @return  string
	 */
	public static function tooltip($tooltip, $title = '', $image = 'images/tooltip.png', $text = '', $href = '', $alt = 'Tooltip', $class = 'hasTooltip', Application $app)
	{
		if (!is_object($app))
		{
			$app = Application::getInstance();
		}

		if (is_array($title))
		{
			foreach (array('image', 'text', 'href', 'alt', 'class') as $param)
			{
				if (isset($title[$param]))
				{
					$$param = $title[$param];
				}
			}

			if (isset($title['title']))
			{
				$title = $title['title'];
			}
			else
			{
				$title = '';
			}
		}

		if (!$text)
		{
			$alt = htmlspecialchars($alt, ENT_COMPAT, 'UTF-8');
			$text = static::image($image, $alt, null, true, $app);
		}

		if ($href)
		{
			$tip = '<a href="' . $href . '">' . $text . '</a>';
		}
		else
		{
			$tip = $text;
		}

		$tooltip = self::tooltipText($title, $tooltip, 0);

		return '<span class="' . $class . '" title="' . $tooltip . '">' . $tip . '</span>';
	}

	/**
	 * Converts a double colon separated string or 2 separate strings to a string ready for bootstrap tooltips
	 *
	 * @param   string  $title      The title of the tooltip (or combined '::' separated string).
	 * @param   string  $content    The content to tooltip.
	 * @param   int     $translate  If true will pass texts through Text.
	 * @param   int     $escape     If true will pass texts through htmlspecialchars.
	 *
	 * @return  string  The tooltip string
	 */
	public static function tooltipText($title = '', $content = '', $translate = 1, $escape = 1)
	{
		// Return empty in no title or content is given.
		if ($title == '' && $content == '')
		{
			return '';
		}

		// Split title into title and content if the title contains '::' (migrated Joomla! code, using the obsolete Mootools format).
		if ($content == '' && !(strpos($title, '::') === false))
		{
			list($title, $content) = explode('::', $title, 2);
		}

		// Pass strings through Text.
		if ($translate)
		{
			$title = Text::_($title);
			$content = Text::_($content);
		}

		// Escape the strings.
		if ($escape)
		{
			$title = str_replace('"', '&quot;', $title);
			$content = str_replace('"', '&quot;', $content);
		}

		// Return only the content if no title is given.
		if ($title == '')
		{
			return $content;
		}

		// Return only the title if title and text are the same.
		if ($title == $content)
		{
			return '<strong>' . $title . '</strong>';
		}

		// Return the formatted sting combining the title and  content.
		if ($content != '')
		{
			return '<strong>' . $title . '</strong><br />' . $content;
		}

		// Return only the title.
		return $title;
	}

	/**
	 * Displays a calendar control field
	 *
	 * @param   string       $value    The date value
	 * @param   string       $name     The name of the text field
	 * @param   string       $id       The id of the text field
	 * @param   string       $format   The date format
	 * @param   array        $attribs  Additional HTML attributes
	 * @param   Application  $app      The application to get the configuration from
	 *
	 * @return  string  HTML markup for a calendar field
	 */
	public static function calendar($value, $name, $id, $format = 'yyyy-mm-dd', $attribs = null, Application $app = null)
	{
		static $done;

		if (!is_object($app))
		{
			$app = Application::getInstance();
		}

		if ($done === null)
		{
			$done = array();
		}

		$attribs['class'] = isset($attribs['class']) ? $attribs['class'] : 'form-control';
		$attribs['class'] = trim($attribs['class'] . ' hasTooltip calendar');

		$readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
		$disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';

		if (is_array($attribs))
		{
			$attribs = ArrayHelper::toString($attribs);
		}

		if (!$readonly && !$disabled)
		{
			// Load the calendar behavior
			Behaviour::calendar();

			// Only display the triggers once for each control.
			if (!in_array($id, $done))
			{
				// @todo Implement a way for the application to override the language
				$lang = Text::detectLanguage($app->getName());

				$document = $app->getDocument();
				$document
					->addScriptDeclaration( <<< JS
akeeba.jQuery(document).ready(function(){
	akeeba.jQuery('#$id-container').datepicker({
		format: "$format",
		todayBtn: "linked",
		language: "$lang",
		autoclose: true
	});
})
JS

					);
				$done[] = $id;
			}

			return '<div class="input-group date" id="' . $id . '-container"><input type="text" title="' . (0 !== (int) $value ? static::date($value, null, null) : '')
			. '" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />'
			. '<span class="input-group-btn" id="' . $id . '_img"><span class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></span></span></div>';
		}
		else
		{
			return '<input type="text" title="' . (0 !== (int) $value ? static::date($value, null, null) : '')
			. '" value="' . (0 !== (int) $value ? static::_('date', $value, 'Y-m-d H:i:s', null) : '') . '" ' . $attribs
			. ' /><input type="hidden" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" />';
		}
	}
} 