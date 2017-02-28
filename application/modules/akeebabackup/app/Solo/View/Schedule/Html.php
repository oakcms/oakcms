<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\View\Schedule;

use Akeeba\Engine\Platform;
use Solo\Model\Schedule;

class Html extends \Solo\View\Html
{
    public $profileid;
    public $profileName;
    public $croninfo;
    public $checkinfo;

	public function onBeforeMain()
	{
		// Get profile ID
		$this->profileid = Platform::getInstance()->get_active_profile();

		// Get profile name
		$this->profileName = $this->escape(Platform::getInstance()->get_profile_name($this->profileid));

		// Get the CRON paths
        /** @var Schedule $model */
        $model           = $this->getModel();
		$this->croninfo  = $model->getPaths();
		$this->checkinfo = $model->getCheckPaths();

		return true;
	}
}