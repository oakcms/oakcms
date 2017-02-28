<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Router;

use Awf\Application\Application;
use Awf\Container\Container;
use Awf\Uri\Uri;

class Router
{
	/**
	 * The container this router is attached to
	 *
	 * @var Container|null
	 */
	protected $container = null;

	/**
	 * The routing rules for the application
	 *
	 * @var  array[Rule]
	 */
	protected $rules = array();

	/**
	 * Public constructor
	 *
	 * @param   Container $container The container this router is attached to
	 *
	 * @return  Router
	 *
	 * @codeCoverageIgnore
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Add a routing rule to the stack
	 *
	 * @param   Rule $rule The routing rule to add
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function addRule(Rule $rule)
	{
		$this->rules[] = $rule;
	}

	/**
	 * Add a routing rule to the stack from an array definition
	 *
	 * @param   array $definition The definition of the routing rule to add, @see Route::__construct()
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function addRuleFromDefinition(array $definition)
	{
		$rule = new Rule($definition);

		$this->addRule($rule);
	}

	/**
	 * Add multiple rules in one go. The array can contain either Rule objects or rule definitions in array format
	 *
	 * @param   array $rules The rules to add
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function addRules(array $rules)
	{
		if (!empty($rules))
		{
			foreach ($rules as $rule)
			{
				if (is_array($rule))
				{
					$this->addRuleFromDefinition($rule);

					continue;
				}

				if (!is_object($rule))
				{
					continue;
				}

				if (!($rule instanceof Rule))
				{
					continue;
				}

				$this->addRule($rule);
			}
		}
	}

	/**
	 * Clear all routing rules
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function clearRules()
	{
		$this->rules = array();
	}

	/**
	 * Put a URL through the routing rules and return the routed URL.
	 *
	 * @param   string  $url      The URL to route
	 * @param   boolean $rebase   Should I rebase the resulting URL? False to return a relative URL to the application's
	 *                            live_site and base_url, as defined in the application configuration
	 *
	 * @return  string  The routed URL
	 */
	public function route($url, $rebase = true)
	{
		// Initialise
		$routeResult = null;

		// Use the routing rules to produce a list of segments
		if (!empty($this->rules))
		{
			/** @var Rule $rule */
			foreach ($this->rules as $rule)
			{
				$routeResult = $rule->route($url);

				if (is_array($routeResult))
				{
					break;
				}
			}
		}

		// Parse the URL into an object
		$uri = new Uri($url);

		if (!is_null($routeResult))
		{
			// We'll replace the path and query string parameters with what the routing rule sent us
			$uri->setPath(implode('/', $routeResult['segments']));
			$uri->setQuery($routeResult['vars']);
		}

		// Do we have to rebase?
		if ($rebase)
		{
			// Get the base URL
			$baseUrl = $this->container->appConfig->get('base_url', '');

			if (empty($baseUrl))
			{
				$baseUrl = '';
			}

			$baseUrl = rtrim($baseUrl, '/');

			if ((strpos($baseUrl, 'http://') === 0) || (strpos($baseUrl, 'https://') === 0))
			{
				$base = $baseUrl;
			}
			else
			{
				$base = Uri::base(false, $this->container);
				$base = rtrim($base, '/') . '/' . $baseUrl;
			}
			$rebaseURI = new Uri($base);

			// Merge the paths of the rebase and routed URIs. However if the router URI contains index.php
			// and the base URL ends in a .php script do not append index.php to the other .php script
			// (this is required for the WP integration to work properly)
			if (!(($uri->getPath() == 'index.php') && (substr($rebaseURI->getPath(), -4) == '.php')))
			{
				$rebaseURI->setPath(rtrim($rebaseURI->getPath(), '/') . '/' . $uri->getPath());
			}

			// Merge the query string parameters of the rebase and routed URIs
			$vars_routed = $uri->getQuery(true);
			$vars_rebase = $rebaseURI->getQuery(true);
			$rebaseURI->setQuery(array_merge($vars_rebase, $vars_routed));

			// We'll return the rebased URI
			$uri = $rebaseURI;
		}

		return $uri->toString();
	}

	/**
	 * Parse a routed URL based on the routing rules, setting the input variables of the attached application.
	 *
	 * @param   string  $url    The URL to parse. If omitted the current URL will be used.
	 * @param   boolean $rebase Is this a rebased URL? If false we assume we're given a relative URL.
	 */
	public function parse($url = null, $rebase = true)
	{
		// If we are not given a URL, use the current URL of the site
		if (empty($url))
		{
			$url = Uri::current();
		}

		// Initialise
		$removePath = null;
		$removeVars = null;

		if ($rebase)
		{
			// Get the base URL
			$baseUrl = $this->container->appConfig->get('base_url', '');

			if (empty($baseUrl))
			{
				$baseUrl = '';
			}

			$baseUrl = rtrim($baseUrl, '/');

			$base = Uri::base(false, $this->container);
			$base = rtrim($base, '/') . '/' . $baseUrl;
			$rebaseURI = new Uri($base);

			// Get the path and vars to remove from the parsed route
			$removePath = $rebaseURI->getPath();
			$removePath = trim($removePath, '/');
			$removeVars = $rebaseURI->getQuery(true);
		}

		$uri = new Uri($url);
		$path = $uri->getPath();
		$path = trim($path, '/');

		// Remove the $removePath
		if (!empty($removePath))
		{
			if (strpos($path, $removePath) === 0)
			{
				$path = substr($path, strlen($removePath));
			}
		}

		// Use the routing rules to parse the URL
		$routeVars = null;
		if (!empty($this->rules))
		{
			/** @var Rule $rule */
			foreach ($this->rules as $rule)
			{
				$routeVars = $rule->parse($path);

				if (is_array($routeVars))
				{
					break;
				}
			}
		}

		if (is_null($routeVars))
		{
			$routeVars = array();
		}

		// Mix route and URI vars
		$uriVars = $uri->getQuery(true);
		$routeVars = array_merge($routeVars, $uriVars);

		// Eliminate $removeVars
		if (is_array($removeVars) && !empty($removeVars))
		{
			foreach ($removeVars as $k => $v)
			{
				if (isset($routeVars[$k]) && ($routeVars[$k] == $v))
				{
					unset($routeVars[$k]);
				}
			}
		}

		// Set the query vars to the application
		if (is_array($routeVars) && !empty($routeVars))
		{
			$this->container->input->setData($routeVars);
		}
	}

	/**
	 * Exports the routing maps as a JSON string
	 *
	 * @return  string  The routes in JSON format
	 */
	public function exportRoutes()
	{
		$maps = array();

		if (!empty($this->rules))
		{
			/** @var Rule $rule */
			foreach ($this->rules as $rule)
			{
				$maps[] = array(
					'path'          => $rule->getPath(),
					'types'         => $rule->getTypes(),
					'matchVars'     => $rule->getMatchVars(),
					'pushVars'      => $rule->getPushVars(),
					'routeCallable' => $rule->getRouteCallable(),
					'parseCallable' => $rule->getParseCallable(),
				);
			}
		}

		$options = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0;

		return json_encode($maps, $options);
	}

	/**
	 * Imports routes from a JSON string
	 *
	 * @param   string  $json    The JSON string to parse
	 * @param   boolean $replace [optional] Should I replace existing routes?
	 *
	 * @return  void
	 */
	public function importRoutes($json, $replace = true)
	{
		$definitions = json_decode($json, true);

		if ($replace)
		{
			$this->clearRules();
		}

		$this->addRules($definitions);
	}
} 