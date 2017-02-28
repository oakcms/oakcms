<?php
/**
 * @package        awf
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\Router;


use Awf\Container\Container;
use Awf\Platform\Joomla\Helper\Helper;

class Router extends \Awf\Router\Router
{
	/** @var bool Is this the Joomla! back-end? */
	protected $isBackend = false;

	public function __construct(Container $container)
	{
		\Awf\Router\Router::__construct($container);

		$this->isBackend = Helper::isBackend();
	}

	/**
	 * Put a URL through the JRoute routing rules and return the routed URL.
	 *
	 * @param   string  $url      The URL to route
	 * @param   boolean $rebase   Should I rebase the resulting URL? False to return a relative URL, true to return an
	 *                            absolute URL.
	 *
	 * @return  string  The routed URL
	 */
	public function route($url, $rebase = true)
	{
		// Backend? Nothing to do!
		if ($this->isBackend)
		{
			return $url;
		}

		// Not rebasing? Cool! Return the relative URL. Note: & is encoded to &amp;
		if (!$rebase)
		{
			return \JRoute::_($url);
		}

		// Get the absolute URL to the site's root sans the path (which is already put in the relative URL by JRoute,
		// because it would KILL its developers to make Joomla! behave rationally and consistent...)
		$rootURL = rtrim(\JURI::base(), '/');
		$subpathURL = \JURI::base(true);

		if (!empty($subpathURL) && ($subpathURL != '/'))
		{
			$rootURL = substr($rootURL, 0, -1 * strlen($subpathURL));
		}

		// Return the absolute routed URL, using & instead of &amp;. Why? Because I'm willing to bet that the only
		// reason you want an absolute URL is to shove it in some email, not output it in an XHTML document. Right?
		return $rootURL . \JRoute::_($url, false);
	}

	/**
	 * SEF URL parser. Joomla! has its own SEF URL parser running before launching a component, therefore this method
	 * does nothing. It simply returns the URL it is given.
	 *
	 * @param string $url    The URL which is supposedly to be parsed. Ignored.
	 * @param bool   $rebase Is this a rebased URL? Ignored.
	 *
	 * @return null|void
	 */
	public function parse($url = null, $rebase = true)
	{
		return $url;
	}
}