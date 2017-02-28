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
 * Class Raw
 *
 * Raw output of the document buffer
 *
 * @package Awf\Document
 */
class Raw extends Document
{
	public function __construct(Container $container)
	{
		parent::__construct($container);

		$this->mimeType = 'text/plain';
	}


	/**
	 * It just echoes the output buffer to the browser
	 *
	 * @return  void
	 */
	public function render()
	{
		$this->addHTTPHeader('Content-Type', $this->getMimeType());

		$name = $this->getName();

		if (!empty($name))
		{
			$this->addHTTPHeader('Content-Disposition', 'attachment; filename="' . $name . '"', true);
		}

		$this->outputHTTPHeaders();

		echo $this->getBuffer();
	}
}