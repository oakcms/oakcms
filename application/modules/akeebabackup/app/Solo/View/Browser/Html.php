<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\View\Browser;


use Solo\Model\Browser;

class Html extends \Solo\View\Html
{
    public $folder;
    public $folder_raw;
    public $parent;
    public $exists;
    public $inRoot;
    public $openbasedirRestricted;
    public $writable;
    public $subfolders;
    public $breadcrumbs;

	/**
	 * Pull the folder browser data from the model
	 *
	 * @return  boolean
	 */
	public function onBeforeMain()
	{
		/** @var Browser $model */
		$model = $this->getModel();

		$this->folder                = $model->getState('folder');
		$this->folder_raw            = $model->getState('folder_raw');
		$this->parent                = $model->getState('parent');
		$this->exists                = $model->getState('exists');
		$this->inRoot                = $model->getState('inRoot');
		$this->openbasedirRestricted = $model->getState('openbasedirRestricted');
		$this->writable              = $model->getState('writable');
		$this->subfolders            = $model->getState('subfolders');
		$this->breadcrumbs           = $model->getState('breadcrumbs');

		return true;
	}
}