<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Controller;


class Browser extends ControllerDefault
{
	/**
	 * Handle the directory listing display
	 */
	public function main()
	{
		$folder = $this->input->getString('folder', '');
		$processfolder = $this->input->getInt('processfolder', 0);

		/** @var \Solo\Model\Browser $model */
		$model = $this->getModel();
		$model->setState('folder', $folder);
		$model->setState('processfolder', $processfolder);
		$model->makeListing();

		parent::display();
	}
} 