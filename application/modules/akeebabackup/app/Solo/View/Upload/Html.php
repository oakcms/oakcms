<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Upload;


class Html extends \Solo\View\Html
{
	public function onBeforeUpload()
	{
		if ($this->done)
		{
			$this->setLayout('done');
		}
		elseif ($this->error)
		{
			$this->setLayout('error');
		}
		else
		{
			$this->setLayout('uploading');
		}

		return true;
	}

	public function onBeforeCancelled()
	{
		$this->setLayout('error');

		return true;
	}

	public function onBeforeStart()
	{
		if ($this->done)
		{
			$this->setLayout('done');
		}
		elseif ($this->error)
		{
			$this->setLayout('error');
		}
		else
		{
			$this->setLayout('default');
		}

		return true;
	}
}