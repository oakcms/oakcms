<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Document;
use Awf\Container\Container;
use Awf\Document\Toolbar\Toolbar;
use Awf\Document\Menu\MenuManager;
use Awf\Application\Application;

/**
 * Class Json
 *
 * A JSON output implementation
 *
 * @package Awf\Document
 */
class Json extends Document
{
	/** @var   boolean  Should I wrap the JSON output in triple hashes? Used to work around broken servers. */
	protected $useHashes = true;

	public function __construct(Container $container)
	{
		parent::__construct($container);

		$this->mimeType = 'application/json';
	}


	/**
	 * Outputs the buffer, which is assumed to contain JSON data, to the
	 * browser. If $useHashes is true the output will be wrapped with triple
	 * hashes, essentially marking the beginning and end of the JSON data in
	 * the output. This is required on broken server implementations which may
	 * prefix or append the output with banner ad code, error messages or any
	 * crap a host might decide it's a good idea to put in all output. Welcome
	 * to the crazy world of hosting.
	 *
	 * @return  void
	 */
	public function render()
	{
		$this->addHTTPHeader('Content-Type', $this->getMimeType());

		$name = $this->getName();

		if (!empty($name))
		{
			$this->addHTTPHeader('Content-Disposition', 'attachment; filename="' . $name . '.json"', true);
		}

		$this->outputHTTPHeaders();

		if ($this->useHashes)
		{
			echo '###' . $this->getBuffer() . '###';
		}
		else
		{
			echo $this->getBuffer();
		}
	}

	/**
	 * Public setter for the $useHashes protected variable.
	 *
	 * @see \Awf\Document\Json::render()
	 *
	 * @param   boolean  $setting
	 *
	 * @return  void
	 */
	public function setUseHashes($setting)
	{
		$this->useHashes = (bool)$setting;
	}

	/**
	 * Public getter for the $useHashes protected variable.
	 *
	 * @see \Awf\Document\Json::render()
	 *
	 * @return  boolean
	 */
	public function getUseHashes()
	{
		return $this->useHashes;
	}
}