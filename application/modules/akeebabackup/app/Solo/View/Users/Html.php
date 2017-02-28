<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Users;


use Awf\Utils\Template;
use Solo\View\DataHtml;

class Html extends DataHtml
{
	public function onBeforeBrowse()
	{
		$document = $this->container->application->getDocument();

		// Buttons (new, edit, copy, delete)
		$buttons = array(
			array(
				'title'   => 'SOLO_BTN_ADD',
				'class'   => 'btn-success',
				'onClick' => 'Solo.System.submitForm(\'adminForm\', \'add\')',
				'icon'    => 'glyphicon glyphicon-plus-sign'
			),
			array(
				'title'   => 'SOLO_BTN_EDIT',
				'class'   => 'btn-default',
				'onClick' => 'Solo.System.submitForm(\'adminForm\', \'edit\')',
				'icon'    => 'glyphicon glyphicon-pencil'
			),
			array(
				'title'   => 'SOLO_BTN_DELETE',
				'class'   => 'btn-danger',
				'onClick' => 'Solo.System.submitForm(\'adminForm\', \'remove\')',
				'icon'    => 'glyphicon glyphicon-remove-sign'
			),
		);

		$toolbar = $document->getToolbar();

		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}

		return parent::onBeforeBrowse();
	}

	protected function onBeforeAdd()
	{
		$this->loadFormJavascript();
		$this->buttonsForAddEdit();

		return parent::onBeforeAdd();
	}

	protected function onBeforeEdit()
	{
		$this->loadFormJavascript();
		$this->buttonsForAddEdit();

		return parent::onBeforeEdit();
	}

	protected function buttonsForAddEdit()
	{
		$buttons = array(
			array(
				'title'   => 'SOLO_BTN_SAVECLOSE',
				'class'   => 'btn-success',
				'onClick' => 'Solo.System.submitForm(\'adminForm\', \'save\')',
				'icon'    => 'glyphicon glyphicon-floppy-save'
			),
			array(
				'title'   => 'SOLO_BTN_CANCEL',
				'class'   => 'btn-warning',
				'onClick' => 'Solo.System.submitForm(\'adminForm\', \'cancel\')',
				'icon'    => 'glyphicon glyphicon-remove'
			),
		);

		$toolbar = $this->container->application->getDocument()->getToolbar();

		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}
	}

	protected function loadFormJavascript()
	{
		Template::addJs('media://js/solo/users.js', $this->container->application);

		$js = <<< JS
Solo.loadScripts.push(function() {
	Solo.Users.initialize();
});

JS;

		$document = $this->container->application->getDocument();
		$document->addScriptDeclaration($js);
	}
}