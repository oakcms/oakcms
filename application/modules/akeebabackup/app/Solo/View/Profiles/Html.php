<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\View\Profiles;

use Akeeba\Engine\Platform;
use Awf\Mvc\DataModel;
use Awf\Pagination\Pagination;
use Solo\View\DataHtml;

class Html extends DataHtml
{
	public function onBeforeBrowse()
	{
		$document = $this->container->application->getDocument();

		// Buttons (new, edit, copy, delete)
		$buttons = array(
			array(
				'title' 	=> 'SOLO_BTN_ADD',
				'class' 	=> 'btn-success',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'add\')',
				'icon' 		=> 'glyphicon glyphicon-plus-sign'
			),
			array(
				'title' 	=> 'SOLO_BTN_EDIT',
				'class' 	=> 'btn-default',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'edit\')',
				'icon' 		=> 'glyphicon glyphicon-pencil'
			),
			array(
				'title' 	=> 'SOLO_BTN_COPY',
				'class' 	=> 'btn-default',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'copy\')',
				'icon' 		=> 'fa fa-copy'
			),
			array(
				'title' 	=> 'SOLO_BTN_DELETE',
				'class' 	=> 'btn-danger',
				'onClick' 	=> 'Solo.System.submitForm(\'adminForm\', \'remove\')',
				'icon' 		=> 'glyphicon glyphicon-remove-sign'
			),
		);

		$toolbar = $document->getToolbar();

		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}

		// Pass the profile ID and name
		$this->profileid = Platform::getInstance()->get_active_profile();

		// Get profile name
		$this->profilename = $this->escape(Platform::getInstance()->get_profile_name($this->profileid));

		return parent::onBeforeBrowse();
	}

	protected function onBeforeAdd()
	{
		$document = $this->container->application->getDocument();

		// Buttons (save, save and close, save and new, cancel)
		$buttons = array(
			array(
				'title' 	=> 'SOLO_BTN_SAVE',
				'class' 	=> 'btn-success',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'apply\')',
				'icon' 		=> 'glyphicon glyphicon-pencil'
			),
			array(
				'title' 	=> 'SOLO_BTN_SAVECLOSE',
				'class' 	=> 'btn-default',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'save\')',
				'icon' 		=> 'glyphicon glyphicon-ok'
			),
			array(
				'title' 	=> 'SOLO_BTN_SAVENEW',
				'class' 	=> 'btn-default',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'savenew\')',
				'icon' 		=> 'glyphicon glyphicon-plus'
			),
			array(
				'title' 	=> 'SOLO_BTN_CANCEL',
				'class' 	=> 'btn-warning',
				'onClick' 	=> 'Solo.System.submitForm(\'adminForm\', \'cancel\')',
				'icon' 		=> 'glyphicon glyphicon-remove'
			),
		);

		$toolbar = $document->getToolbar();

		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}

		return parent::onBeforeAdd();
	}

	protected function onBeforeEdit()
	{
		$document = $this->container->application->getDocument();

		// Buttons (save, save and close, save and new, cancel)
		$buttons = array(
			array(
				'title' 	=> 'SOLO_BTN_SAVE',
				'class' 	=> 'btn-success',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'apply\')',
				'icon' 		=> 'glyphicon glyphicon-pencil'
			),
			array(
				'title' 	=> 'SOLO_BTN_SAVECLOSE',
				'class' 	=> 'btn-default',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'save\')',
				'icon' 		=> 'glyphicon glyphicon-ok'
			),
			array(
				'title' 	=> 'SOLO_BTN_SAVENEW',
				'class' 	=> 'btn-default',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'savenew\')',
				'icon' 		=> 'glyphicon glyphicon-plus'
			),
			array(
				'title' 	=> 'SOLO_BTN_CANCEL',
				'class' 	=> 'btn-warning',
				'onClick' 	=> 'Solo.System.submitForm(\'adminForm\', \'cancel\')',
				'icon' 		=> 'glyphicon glyphicon-remove'
			),
		);

		$toolbar = $document->getToolbar();

		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}

		return parent::onBeforeEdit();
	}
} 