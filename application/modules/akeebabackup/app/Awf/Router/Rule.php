<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Router;

use Awf\Uri\Uri;

class Rule
{

	/**
	 * The routing path to use
	 *
	 * @var  string
	 */
	protected $path = '';

	/**
	 * The named variable types
	 *
	 * @var  array
	 */
	protected $types = array();

	/**
	 * The match vars that have to be satisfied for this rule
	 *
	 * @var  array
	 */
	protected $matchVars = array();

	/**
	 * The push vars to use when parsing this rule
	 *
	 * @var  array
	 */
	protected $pushVars = array();

	/**
	 * The routing callback to use instead of the built-in router
	 *
	 * @var  callable|null
	 */
	protected $routeCallable = null;

	/**
	 * The route parsing callback to use instead of the built-in parser
	 *
	 * @var  callable|null
	 */
	protected $parseCallable = null;

	/**
	 * Should I use a callback for routing URLs?
	 *
	 * @var  boolean
	 */
	protected $useCallableForRoute = false;

	/**
	 * Should I use a callback for parsing URLs?
	 *
	 * @var  boolean
	 */
	protected $useCallableForParse = false;

	/**
	 * Create a routing rule, optionally initialising it from the $definition array. The known keys are:
	 * - path                @see \Awf\Route\Rule::setPath
	 * - types                @see \Awf\Route\Rule::setTypes
	 * - matchVars            @see \Awf\Route\Rule::setMatchVars
	 * - pushVars            @see \Awf\Route\Rule::setPushVars
	 * - routeCallable        @see \Awf\Route\Rule::setRouteCallable
	 * - parseCallable        @see \Awf\Route\Rule::setParseCallable
	 *
	 * @param   array $definition See above
	 *
	 * @return  Rule
	 *
	 * @codeCoverageIgnore
	 */
	public function __construct($definition = array())
	{
		$knownKeys = array('path', 'types', 'matchVars', 'pushVars', 'routeCallable', 'parseCallable');

		foreach ($knownKeys as $key)
		{
			if (isset($definition[$key]))
			{
				$method = 'set' . ucfirst($key);

				$this->$method($definition[$key]);
			}
		}
	}

	/**
	 * Routes a non-SEF URL to its SEF counterpart.
	 *
	 * If this rule is not applicable to this URL we return null
	 *
	 * If this rule applies to this URL we return a hash array with the keys 'segments' and 'vars' containing the SEF
	 * URL's paths and any remaining query string parameters respectively.
	 *
	 * @param   string $url The non-SEF URL
	 *
	 * @return  null|array
	 */
	public function route($url)
	{
		if ($this->useCallableForRoute)
		{
			return call_user_func($this->routeCallable, $url);
		}
		else
		{
			// Extract the query parameters
			$uri = new Uri($url);
			$params = $uri->getQuery(true);

			// Make sure the "match variables" do match
			if (!$this->matchesVars($params))
			{
				return null;
			}

			// Get the route segments
			$segments = $this->buildRoute($params);

			// If we got null instead of segments we are not an applicable router to this URL
			if (is_null($segments))
			{
				return null;
			}

			// Otherwise return the path segments and the remaining vars
			return array(
				'segments' => $segments,
				'vars'     => $params,
			);
		}
	}

	/**
	 * Parse a SEF URL path into URL parameters
	 *
	 * @param   string $path The path to parse, e.g. /foo/bar/1/2/3
	 *
	 * @return  array|null  The URL parameters or null if we can't parse the route with this rule
	 */
	public function parse($path)
	{
		$extraParams = array();

		if (strpos($path, '?') !== false)
		{
			$uri = new Uri($path);
			$path = $uri->getPath();
			$extraParams = $uri->getQuery(true);
		}

		if ($this->useCallableForParse)
		{
			$ret = call_user_func($this->parseCallable, $path);
		}
		else
		{
			// Explode the path parts
			$segments = explode('/', $path);

			// Try to extract the URL parameters by parsing the segments
			$params = $this->parseRoute($segments);

			// If we got null back we can't parse this route
			if (is_null($params))
			{
				return null;
			}

			// Mix in the push variables
			$params = array_merge($this->pushVars, $params);

			// Return the URL parameters
			$ret = $params;
		}

		return array_merge($ret, $extraParams);
	}

	/**
	 * Set the "match variables" for this routing rule. These variables must be present in the URL being routed for this
	 * routing rule to be used. You have to provide a hashed array, e.g.
	 * array( 'foo' => 1, 'bar' => null )
	 *
	 * When a match variable has a non-null value, this exact value MUST be present in the URL to trigger this rule.
	 * Moreover this variable will be removed from the query parameters used for building the route.
	 *
	 * When a match variable has a null value, this rule will be triggered if the variable is present in the URL, no
	 * matter what its value is. The variable will be available to the query parameters used for building the route.
	 *
	 * @param   array $matchVars
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setMatchVars($matchVars)
	{
		$this->matchVars = $matchVars;
	}

	/**
	 * Get the "match variables" for this routing rule
	 *
	 * @return  array
	 *
	 * @codeCoverageIgnore
	 */
	public function getMatchVars()
	{
		return $this->matchVars;
	}

	/**
	 * Set the callable to use for parsing routes. The callable must return null if it can't parse the URL (it's not
	 * applicable) or an array of the same format as the Rule::parse() method.
	 *
	 * @param   callable|null $parseCallable
	 *
	 * @return  void
	 */
	public function setParseCallable($parseCallable)
	{
		$this->useCallableForParse = !(is_null($parseCallable) || empty($parseCallable));

		$this->parseCallable = $parseCallable;
	}

	/**
	 * Get the callable to use for parsing routes
	 *
	 * @return  null|callable
	 *
	 * @codeCoverageIgnore
	 */
	public function getParseCallable()
	{
		return $this->parseCallable;
	}

	/**
	 * Set the "push variables" for routing rule. These variables are pushed to the input when a URL is successfully
	 * parsed by this rule. You have to provide a hash array, e.g.
	 * array( 'foo' => 1, 'bar' => 2 )
	 *
	 * @param   array $pushVars The push variables to set
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setPushVars($pushVars)
	{
		$this->pushVars = $pushVars;
	}

	/**
	 * Get the "push variables" for this routing rule
	 *
	 * @return  array
	 *
	 * @codeCoverageIgnore
	 */
	public function getPushVars()
	{
		return $this->pushVars;
	}

	/**
	 * Set the callable to use for routing a URL using this rule. The callable must return null if it can't route the
	 * URL (it's not applicable) or an array of the same format as the Rule::route() method.
	 *
	 * @param   callable|null $routeCallable The callable to use for routing URLs
	 *
	 * @return  void
	 */
	public function setRouteCallable($routeCallable)
	{
		$this->useCallableForRoute = !(is_null($routeCallable) || empty($routeCallable));

		$this->routeCallable = $routeCallable;
	}

	/**
	 * Get the callable to use for routing a URL using this rule
	 *
	 * @return  null|callable
	 *
	 * @codeCoverageIgnore
	 */
	public function getRouteCallable()
	{
		return $this->routeCallable;
	}

	/**
	 * Set the routing path. A routing path looks like this: foo/bar/ * /:id/:cat?/:tags* (the spaces are not part of
	 * the example, they are added to prevent PHP from breaking the comment block). It is a series of path segments:
	 *
	 * - any static string not starting with colon means that we are looking for an exact match
	 * - :something means that the value in this position will be assigned to query parameter "something"
	 * - :something? means that if a value exists in this position it will be assigned to query parameter "something"
	 * - :something* means that zero or more values in this position will be assigned to query array parameter "something"
	 * - * will match any value, but it will be ignored (not assigned to a query parameter)
	 *
	 * By default any kind of value is matched, unless there is a type regex for the named variable. The matching is
	 * greedy, therefore having :foo* / :bar? where both foo and bar are of the same type will always result in bar not
	 * being matched. In case bar is not followed by a question mark this means that the rule itself won't be matched!
	 *
	 * The same word of caution applies to the lone star operator. It will match anything that doesn't match the next
	 * segment. If the next segment doesn't have a type specified and it's not a static string, the lone start operator
	 * will never match anything at all.
	 *
	 * Moreover, you should NEVER use two or more lone stars in succession. This will completely screw up the route
	 * parsing.
	 *
	 * A lone start followed by an optional parameter will always try to match the optional parameter's type,
	 * essentially ignoring the fact that it's optional. This is because the forward type lookup is limited to a single
	 * position to improve performance. Therefore NEVER put an optional parameter after a lone star.
	 *
	 * @param   string $routePath The routing path to set
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setPath($routePath)
	{
		$this->path = $routePath;
	}

	/**
	 * Get the routing path.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set the types (matching RegEx) for named parameters of the routing path (the :something strings)
	 *
	 * @param   array $types The types to set
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 */
	public function setTypes($types)
	{
		$this->types = $types;
	}

	/**
	 * Get the types for named parameters of the routing path
	 *
	 * @return  array
	 *
	 * @codeCoverageIgnore
	 */
	public function getTypes()
	{
		return $this->types;
	}

	/**
	 * Check whether the query string parameters $params match the "match variables" of this rule
	 *
	 * @param   array $params
	 *
	 * @return  boolean  True on success
	 */
	protected function matchesVars(array &$params)
	{
		// If we don't have match vars we assume this URL can be routed by our rule
		if (empty($this->matchVars))
		{
			return true;
		}

		foreach ($this->matchVars as $k => $v)
		{
			// If the variable doesn't exist we don't have a match; break and return false
			if (!isset($params[$k]))
			{
				return false;
			}

			// We have a match for a variable with a null value. Nothing else to do for it, continue to the next one
			if (is_null($v))
			{
				continue;
			}

			if ($params[$k] == $v)
			{
				// We have an exact variable match. Remove it from the $params array.
				unset($params[$k]);
			}
			else
			{
				// Exact match failed. We don't have a match.
				return false;
			}
		}

		// All checks passed, we were successful!
		return true;
	}

	/**
	 * Build a route segment based on the provided URL parameters and the routing path of this rule
	 *
	 * @param   array $params
	 *
	 * @return  array|null  An array of path segments, or null if this rule is not applicable
	 */
	protected function buildRoute(array &$params)
	{
		$pathRules = explode('/', $this->path);
		$segments = array();

		foreach ($pathRules as $rule)
		{
			$rule = trim($rule);

			if (substr($rule, 0, 1) == ':')
			{
				// Init
				$rule = substr($rule, 1);
				$isArray = false;
				$isOptional = false;

				// Is this an array or optional variable?
				$lastChar = substr($rule, -1);
				if ($lastChar == '*')
				{
					$isArray = true;
				}
				elseif ($lastChar == '?')
				{
					$isOptional = true;
				}

				if ($isArray || $isOptional)
				{
					$rule = substr($rule, 0, -1);
				}

				// What happens if this variable doesn't exist in my variables list?
				if (!isset($params[$rule]))
				{
					// If it's optional, skip it
					if ($isOptional)
					{
						continue;
					}
					// If it's not optional return null, meaning that we can't parse this routing rule
					else
					{
						return null;
					}
				}

				// Get the value
				$value = $params[$rule];

				// Make sure the type is right
				if ($isArray && !is_array($value))
				{
					if (is_object($value))
					{
						$value = (array)$value;
					}
					else
					{
						$value = array($value);
					}
				}
				elseif (!$isArray && (is_array($value)))
				{
					$value = array_shift($value);
				}
				elseif (!$isArray && (is_object($value)))
				{
					$value = (array)$value;
					$value = array_shift($value);
				}

				// Push the value of the variable to the segments
				if (!$isArray)
				{
					$segments[] = (string)$value;
				}
				else
				{
					foreach ($value as $v)
					{
						$segments[] = (string)$v;
					}
				}

				// Finally, unset the variable
				unset ($params[$rule]);
			}
			elseif ($rule == '*')
			{
				// Lone star rules are ignored during route building
			}
			else
			{
				// Static string, append verbose
				$segments[] = $rule;
			}
		}

		return $segments;
	}

	/**
	 * Parse the segments of the SEF URL and convert them to query parameters
	 *
	 * @param   array $segments
	 *
	 * @return  array|null  The query parameters, or null if we can't parse this route
	 */
	protected function parseRoute(array $segments)
	{
		$pathRules = explode('/', $this->path);
		$vars = array();

		$isGreedy = false;
		$rule = null;
		$varName = null;
		$varType = null;
		$isArray = false;
		$isOptional = false;
		$segment = null;

		while (!empty($segments))
		{
			if (is_null($segment))
			{
				$segment = array_shift($segments);
			}

			// No current rule. Let's fetch one.
			if (is_null($rule))
			{
				// Do we have more path parts but no more rules?
				if (empty($pathRules))
				{
					if (!$isGreedy)
					{
						// If we are not in a star rule, we shouldn't be parsing this route.
						return null;
					}
					else
					{
						// It's a star rule. The rest of the query is ignored. We can just return now.
						return $vars;
					}
				}

				// Re-initialise
				$isGreedy = false;
				$rule = null;
				$varName = null;
				$varType = null;
				$isArray = false;
				$isOptional = false;

				// Get the next rule
				$rule = array_shift($pathRules);
				$rule = trim($rule);

				if ($rule == '*')
				{
					// We have a greedy lonely star. Set the greedy flag and fetch the next rule
					$isGreedy = true;
					$rule = array_shift($pathRules);
					$rule = trim($rule);
				}
			}

			// Do we have a rule with a variable name in it?
			if (substr($rule, 0, 1) == ':')
			{
				// Do I have to parse the rule first?
				if (empty($varName))
				{
					$varName = substr($rule, 1);
					$varType = null;
					$isArray = false;
					$isOptional = false;

					// Is this an array or optional variable?
					$lastChar = substr($varName, -1);
					if ($lastChar == '*')
					{
						$isArray = true;
					}
					elseif ($lastChar == '?')
					{
						$isOptional = true;
					}

					if ($isArray || $isOptional)
					{
						$varName = substr($varName, 0, -1);
					}

					// Get the variable type
					if (isset($this->types[$varName]))
					{
						$varType = $this->types[$varName];
					}
				}

				// Try to match the variable type
				if (!empty($varType))
				{
					// What to do if we don't have a match?
					$matched = preg_match($varType, $segment);

					if (!$matched)
					{
						if ($isGreedy)
						{
							// If we are in a greedy match ignore this segment
							$segment = null;
							continue;
						}
						elseif ($isOptional)
						{
							// If it's an optional variable, ignore this rule
							$rule = null;
						}
						elseif ($isArray && count($vars[$varName]))
						{
							// It's an array variable and we're done parsing it
							$rule = null;
							$segments[] = $segment;
							$segment = null;
							continue;
						}
						else
						{
							// We can't parse this rule
							return null;
						}
					}
				}

				// The type matched. First, unset the greedy flag.
				$isGreedy = false;

				// Extract the variable.
				if ($isArray)
				{
					// Make sure the array variable is an array
					if (!isset($vars[$varName]))
					{
						$vars[$varName] = array();
					}

					// Push the segment to the array variable
					$vars[$varName][] = $segment;
				}
				else
				{
					// Push the segment to the variable
					$vars[$varName] = $segment;

					// Since this is a single match rule, go to the next rule
					$rule = null;
				}

				// Unset the segment so that we can proceed
				$segment = null;
			}
			// Do we have an exact match rule (warning: case sensitive!!)?
			else
			{
				if ($segment !== $rule)
				{
					// No match and we already have a greedy lone star? Ignore the segment!
					if ($isGreedy)
					{
						$segment = null;
						continue;
					}
					else
					{
						// Not greedy and no match? We can't parse this rule.
						return null;
					}
				}
				else
				{
					// We have a match. Kill the greedy flag and mark this segment as complete
					$isGreedy = false;
					$rule = null;
					$segment = null;
				}
			}
		}

		// We ran out of segments. Have we also run out of rules?
		if (empty($pathRules) || ((count($pathRules) == 1) && ($pathRules[0] == '*')))
		{
			// No rules left, or just a lone star. All good!
			return $vars;
		}
		else
		{
			// Unmatched rules left. Are all the rules left optional or greedy?
			$canSkip = true;

			foreach($pathRules as $rule)
			{
				$firstChar = substr($rule, 0, 1);
				$lastChar = substr($rule, -1);

				if ($firstChar == '*')
				{
					continue;
				}

				if ($firstChar != ':')
				{
					$canSkip = false;
					break;
				}

				if (($lastChar != '*') && ($lastChar != '?'))
				{
					$canSkip = false;
					break;
				}
			}

			return $canSkip ? $vars : null;
		}
	}
}