<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\View\Sysconfig;


use Awf\Router\Router;
use Awf\Utils\Template;

class Html extends \Solo\View\Html
{
	public function onBeforeMain()
	{
		$document = $this->container->application->getDocument();

		$buttons = array(
			array(
				'title' 	=> 'SOLO_BTN_SAVECLOSE',
				'class' 	=> 'btn-success',
				'onClick'	=> 'Solo.System.submitForm(\'adminForm\', \'save\')',
				'icon' 		=> 'glyphicon glyphicon-ok'
			),
			array(
				'title' 	=> 'SOLO_BTN_SAVE',
				'class'		=> 'btn-default',
				'onClick' 	=> 'Solo.System.submitForm(\'adminForm\', \'apply\')',
				'icon' 		=> 'glyphicon glyphicon-ok'
			),
			array(
				'title' 	=> 'SOLO_BTN_PHPINFO',
				'class' 	=> 'btn-info',
				'url' 		=> $this->container->router->route('index.php?view=phpinfo'),
				'icon' 		=> 'glyphicon glyphicon-info-sign'
			),
			array(
				'title' 	=> 'SOLO_BTN_CANCEL',
				'class' 	=> 'btn-warning',
				'url' 		=> $this->container->router->route('index.php'),
				'icon' 		=> 'glyphicon glyphicon-remove'
			),
		);

		$toolbar = $document->getToolbar();

		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}

		// Load Javascript
		Template::addJs('media://js/solo/setup.js', $this->container->application);
		Template::addJs('media://js/solo/sysconfig.js', $this->container->application);

		$js = <<< JS
Solo.loadScripts.push(function() {
	Solo.Setup.init();
});

JS;

		$document = $this->container->application->getDocument();
		$document->addScriptDeclaration($js);

		return true;
	}
}