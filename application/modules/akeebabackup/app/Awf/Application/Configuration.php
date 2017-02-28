<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Awf\Application;


use Awf\Container\Container;
use Awf\Registry\Registry;
use Awf\Utils\Phpfunc;

class Configuration extends Registry
{

	/** @var \Awf\Container\Container|null The DI container we belong to */
	protected $container = null;

	/** @var string The path to the default JSON configuration file */
	protected $defaultPath = null;

	/**
	 * Public constructor
	 *
	 * @param Container $container The DI container we belong to
	 * @param null      $data      [optional] Data to initialise the application configuration
	 */
	public function __construct(Container $container, $data = null)
	{
		parent::__construct($data);

		$this->container = $container;
	}

	/**
	 * Loads the configuration off a JSON file
	 *
	 * @param string  $filePath The path to the JSON file (optional)
	 * @param Phpfunc $phpfunc  The PHP function abstraction, used for testing
	 *
	 * @return  void
	 */
	public function loadConfiguration($filePath = null, Phpfunc $phpfunc = null)
	{
		// @codeCoverageIgnoreStart
		if (!is_object($phpfunc))
		{
			$phpfunc = new Phpfunc();
		}
		// @codeCoverageIgnoreEnd

		if (empty($filePath))
		{
			$filePath = $this->getDefaultPath();
		}

		// Reset the class
		$this->data = new \stdClass();

		// Try to open the file
		$fileData = @$phpfunc->file_get_contents($filePath);

		if ($fileData !== false)
		{
			$fileData = explode("\n", $fileData, 2);

			if (count($fileData) < 2)
			{
				return;
			}

			$fileData = $fileData[1];
			$this->loadString($fileData);
		}
	}

	/**
	 * Save the application configuration
	 *
	 * @param   string $filePath The path to the JSON file (optional)
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  When saving fails
	 */
	public function saveConfiguration($filePath = null)
	{
		if (empty($filePath))
		{
			$filePath = $this->getDefaultPath();
		}

		$fileData = $this->toString('JSON', array('pretty_print' => true));
		$fileData = "<?php die; ?>\n" . $fileData;

		$res = $this->container->fileSystem->write($filePath, $fileData);

        if (!$res)
        {
            throw new \RuntimeException('Can not save ' . $filePath, 500);
        }
	}

	/**
	 * Sets the default configuration file path
	 *
	 * @param string $defaultPath
	 */
	public function setDefaultPath($defaultPath)
	{
		$this->defaultPath = $defaultPath;
	}

	/**
	 * Returns the default configuration file path. If it's not specified, it defines it based on the built-in
	 * conventions.
	 *
	 * @return string
	 */
	public function getDefaultPath()
	{
		if (empty($this->defaultPath))
		{
			$this->defaultPath = $this->container->basePath . '/assets/private/config.php';
		}

		return $this->defaultPath;
	}
}