<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Utils;


use Awf\Application\Application;
use Awf\Uri\Uri;

abstract class Template
{
	public static function addCss($path, $app = null, $useMediaQueryKey = true)
	{
		if (!is_object($app))
		{
			$app = Application::getInstance();
		}

		$url = self::parsePath($path, false, $app);

		$mediaQueryKey = $app->getContainer()->mediaQueryKey;

		if ($useMediaQueryKey && !empty($mediaQueryKey))
		{
			$uri = Uri::getInstance($url);
			$uri->setVar($mediaQueryKey, '1');
			$url = $uri->toString();
		}

		$app->getDocument()->addStyleSheet($url);
	}

	public static function addJs($path, $app = null, $useMediaQueryKey = true)
	{
		if (!is_object($app))
		{
			$app = Application::getInstance();
		}

		$url = self::parsePath($path, false, $app);

		$mediaQueryKey = $app->getContainer()->mediaQueryKey;

		if ($useMediaQueryKey && !empty($mediaQueryKey))
		{
			$uri = Uri::getInstance($url);
			$uri->setVar($mediaQueryKey, '1');
			$url = $uri->toString();
		}

		$app->getDocument()->addScript($url);
	}

	/**
	 * Parse a fancy path definition into a path relative to the site's root,
	 * respecting template overrides, suitable for inclusion of media files.
	 * For example, media://com_foobar/css/test.css is parsed into
	 * media/com_foobar/css/test.css if no override is found, or
	 * templates/mytemplate/media/com_foobar/css/test.css if the current
	 * template is called mytemplate and there's a media override for it.
	 *
	 * The valid protocols are:
	 * media://		The media directory or a media override
	 * site://		Path relative to site's root (no overrides)
	 *
	 * @param   string       $path       Fancy path
	 * @param   boolean      $localFile  When true, it returns the local path, not the URL
	 * @param   Application  $app        The application we're operating under
	 *
	 * @return  string  Parsed path
	 */
	public static function parsePath($path, $localFile = false, $app = null)
	{
		$rootPath = $app->getContainer()->filesystemBase;

		if (!is_object($app))
		{
			$app = Application::getInstance();
		}

		if ($localFile)
		{
			$url = rtrim($rootPath, DIRECTORY_SEPARATOR) . '/';
		}
		else
		{
			$url = Uri::base(false, $app->getContainer());
		}

		$altPaths = self::getAltPaths($path, $app);
		$ext = pathinfo($altPaths['normal'], PATHINFO_EXTENSION);

		// We have an uncompressed CSS / JS file. We must look for a minimised file.
		if (in_array($ext, array('css', 'js')) && (strstr($altPaths['normal'], '.min.') === false))
		{
			$minFile = dirname($altPaths['normal']) . '/' . basename($altPaths['normal'], $ext) . 'min.' . $ext;
			$normalFileExists = @file_exists($altPaths['normal']);
			$minFileExists = @file_exists($rootPath . '/' . $minFile);

			// If debug is not enabled prefer the minimised file if it exists
			if ((!defined('AKEEBADEBUG') || !AKEEBADEBUG) && $minFileExists)
			{
				$altPaths['normal'] = $minFile;
			}
			// If debug is enabled only use the minimised file if the uncompressed one does not exist
			elseif ($minFileExists && !$normalFileExists)
			{
				$altPaths['normal'] = $minFile;
			}
		}

		$filePath = $altPaths['normal'];

		// If AKEEBADEBUG is enabled prefer the debug path, if one exists
		if (defined('AKEEBADEBUG') && AKEEBADEBUG && isset($altPaths['debug']))
		{
			if (file_exists($rootPath . '/' . $altPaths['debug']))
			{
				$filePath = $altPaths['debug'];
			}
		}
		// If AKEEBADEBUG is not enabled but there is an alternate path, try using the alternate path instead
		elseif (isset($altPaths['alternate']))
		{
			// Look for a minimised file, if available
			if (in_array($ext, array('css', 'js')) && strstr($altPaths['alternate'], '.min.') === false)
			{
				$minFile = dirname($altPaths['alternate']) . '/' . basename($altPaths['alternate'], $ext) . 'min.' . $ext;

				if (@file_exists($rootPath . '/' . $minFile))
				{
					$altPaths['alternate'] = $minFile;
				}
			}

			if (file_exists($rootPath . '/' . $altPaths['alternate']))
			{
				$filePath = $altPaths['alternate'];
			}
		}

		$url .= $filePath;

		return $url;
	}

	/**
	 * Parse a fancy path definition into a path relative to the site's root.
	 * It returns both the normal and alternative (template media override) path.
	 * For example, media://com_foobar/css/test.css is parsed into
	 * array(
	 *   'normal' => 'media/com_foobar/css/test.css',
	 *   'alternate' => 'templates/mytemplate/media/com_foobar/css//test.css'
	 * );
	 *
	 * The valid protocols are:
	 * media://		The media directory or a media override
	 * site://		Path relative to site's root (no alternate)
	 *
	 * @param   string       $path  Fancy path
	 * @param   Application  $app   The application we're operating under
	 *
	 * @return  array  Array of normal and alternate parsed path
	 */
	public static function getAltPaths($path, $app = null)
	{
		if (!is_object($app))
		{
			$app = Application::getInstance();
		}

		$protoAndPath = explode('://', $path, 2);

		if (count($protoAndPath) < 2)
		{
			$protocol = 'media';
		}
		else
		{
			$protocol = $protoAndPath[0];
			$path = $protoAndPath[1];
		}

		$path = ltrim($path, '/' . DIRECTORY_SEPARATOR);

		switch ($protocol)
		{
			case 'media':
				// Do we have a media override in the template?
				$pathAndParams = explode('?', $path, 2);

				// Get the path of the templates directory relative to the file system base
				$rootPath = realpath($app->getContainer()->filesystemBase);
				$templateRelativePath = realpath($app->getContainer()->templatePath);

				if ($templateRelativePath == $rootPath)
				{
					$templateRelativePath = '';
				}
				elseif (strpos($templateRelativePath, $rootPath) === 0)
				{
					$templateRelativePath = substr($templateRelativePath, strlen($rootPath));
					$templateRelativePath = trim($templateRelativePath, DIRECTORY_SEPARATOR . '/\\') . '/';
				}

				$templateRelativePath = str_replace('\\', '/', $templateRelativePath);

				// Return the alternative paths
				$ret = array(
					'normal'	 => 'media/' . $pathAndParams[0],
					'alternate'	 =>  $templateRelativePath . $app->getTemplate() . '/media/' . $pathAndParams[0],
				);
				break;

			default:
			case 'site':
				$ret = array(
					'normal' => $path
				);
				break;
		}

		// For CSS and JS files, add a debug path if the supplied file is compressed
		$ext = pathinfo($ret['normal'], PATHINFO_EXTENSION);

		if (in_array($ext, array('css', 'js')))
		{
			$file = basename($ret['normal'], '.' . $ext);

			if (strlen($file) > 4 && strrpos($file, '.min', '-4'))
			{
				$position = strrpos($file, '.min', '-4');
				$filename = str_replace('.min', '.', $file, $position) . $ext;
			}
			else
			{
				$filename = $file . '-uncompressed.' . $ext;
			}

			// Clone the $ret array so we can manipulate the 'normal' path a bit
			$t1 = (object) $ret;
			$temp = clone $t1;
			unset($t1);
			$temp = (array)$temp;
			$normalPath = explode('/', $temp['normal']);
			array_pop($normalPath);
			$normalPath[] = $filename;
			$ret['debug'] = implode('/', $normalPath);
		}

		return $ret;
	}
} 