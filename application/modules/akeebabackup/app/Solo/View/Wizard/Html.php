<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Wizard;

use Awf\Text\Text;
use Awf\Utils\Template;
use Solo\Helper\Escape;
use Awf\Uri\Uri;

/**
 * The view class for the Configuration view
 */
class Html extends \Solo\View\Html
{
	public function onBeforeMain()
	{
		$document = $this->container->application->getDocument();

		// Load the necessary Javascript
		Template::addJs('media://js/solo/configuration.js', $this->container->application);
		Template::addJs('media://js/solo/wizard.js', $this->container->application);

		// Append buttons to the toolbar
		$buttons = array(
			array(
				'title'   => 'SOLO_BTN_SUBMIT',
				'class'   => 'btn-success',
				'onClick' => 'document.forms.adminForm.submit(); return false;',
				'icon'    => 'glyphicon glyphicon-floppy-save'
			),
		);


		$toolbar = $document->getToolbar();
		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}

		// Get the site URL and root directory
		$this->siteInfo = $this->getModel()->guessSiteParams();

		// Add Javascript
		$router                                 = $this->getContainer()->router;
		$strings                                = array();
		$strings['COM_AKEEBA_CONFIG_UI_BROWSE'] = Escape::escapeJS(Text::_('COM_AKEEBA_CONFIG_UI_BROWSE'));
		$strings['SOLO_COMMON_LBL_ROOT']        = Escape::escapeJS(Text::_('SOLO_COMMON_LBL_ROOT'));
		$urlBrowser                             = Escape::escapeJS($router->route('index.php?view=browser&tmpl=component&processfolder=1&folder='));
		$urlAjax                                = Escape::escapeJS($router->route('index.php?view=wizard&task=ajax'));
		$js                                     = <<< JS
Solo.loadScripts.push(function() {
    // Initialise the translations
	Solo.Configuration.translations['UI-BROWSE'] = '{$strings['COM_AKEEBA_CONFIG_UI_BROWSE']}';
	Solo.Configuration.translations['UI-ROOT']   = '{$strings['SOLO_COMMON_LBL_ROOT']}';

	// Push some custom URLs
	Solo.Configuration.URLs['browser'] = '$urlBrowser';
	Solo.System.params.AjaxURL         = '$urlAjax';

	// Setup buttons
	Solo.System.addEventListener(document.getElementById('btnBrowse'), 'click', Solo.Wizard.onBtnBrowseClick);
	Solo.System.addEventListener(document.getElementById('btnPythia'), 'click', Solo.Wizard.onBtnPythiaClick);

	var elDbDriver = document.querySelector('select[id*="akeeba.platform.dbdriver"]');
	Solo.System.addEventListener(elDbDriver, 'change', Solo.Wizard.onDatabaseDriverChange);
	Solo.System.triggerEvent(elDbDriver);
  
});

JS;
		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);

		// All done, show the page!
		return true;
	}

	public function onBeforeWizard()
	{
		$document = $this->container->application->getDocument();

		// Load the necessary Javascript
		Template::addJs('media://js/solo/backup.js', $this->container->application);
		Template::addJs('media://js/solo/wizard.js', $this->container->application);


		$router                                                 = $this->getContainer()->router;
		$strings                                                = array();
		$strings['COM_AKEEBA_CONFWIZ_UI_TRYAJAX']               = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_TRYAJAX'));
		$strings['COM_AKEEBA_CONFWIZ_UI_TRYIFRAME']             = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_TRYIFRAME'));
		$strings['COM_AKEEBA_CONFWIZ_UI_CANTUSEAJAX']           = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_CANTUSEAJAX'));
		$strings['COM_AKEEBA_CONFWIZ_UI_MINEXECTRY']            = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_MINEXECTRY'));
		$strings['COM_AKEEBA_CONFWIZ_UI_CANTDETERMINEMINEXEC']  = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_CANTDETERMINEMINEXEC'));
		$strings['COM_AKEEBA_CONFWIZ_UI_SAVEMINEXEC']           = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_SAVEMINEXEC'));
		$strings['COM_AKEEBA_CONFWIZ_UI_CANTSAVEMINEXEC']       = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_CANTSAVEMINEXEC'));
		$strings['COM_AKEEBA_CONFWIZ_UI_CANTFIXDIRECTORIES']    = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_CANTFIXDIRECTORIES'));
		$strings['COM_AKEEBA_CONFWIZ_UI_CANTDBOPT']             = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_CANTDBOPT'));
		$strings['COM_AKEEBA_CONFWIZ_UI_EXECTOOLOW']            = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_EXECTOOLOW'));
		$strings['COM_AKEEBA_CONFWIZ_UI_MINEXECTRY']            = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_MINEXECTRY'));
		$strings['COM_AKEEBA_CONFWIZ_UI_SAVINGMAXEXEC']         = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_SAVINGMAXEXEC'));
		$strings['COM_AKEEBA_CONFWIZ_UI_CANTSAVEMAXEXEC']       = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_CANTSAVEMAXEXEC'));
		$strings['COM_AKEEBA_CONFWIZ_UI_CANTDETERMINEPARTSIZE'] = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_CANTDETERMINEPARTSIZE'));
		$strings['COM_AKEEBA_CONFWIZ_UI_PARTSIZE']              = Escape::escapeJS(Text::_('COM_AKEEBA_CONFWIZ_UI_PARTSIZE'));
		$strings['COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE']         = Escape::escapeJS(Text::_('COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE'));

		$urlAjax = Escape::escapeJS($router->route('index.php?view=wizard&task=ajax'));

		$js = <<< JS
Solo.loadScripts.push(function ()
{
	Solo.System.params.AjaxURL = '$urlAjax';

	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_TRYAJAX'] 				= '{$strings['COM_AKEEBA_CONFWIZ_UI_TRYAJAX']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_TRYIFRAME'] 			    = '{$strings['COM_AKEEBA_CONFWIZ_UI_TRYIFRAME']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_CANTUSEAJAX'] 			= '{$strings['COM_AKEEBA_CONFWIZ_UI_CANTUSEAJAX']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_MINEXECTRY'] 			= '{$strings['COM_AKEEBA_CONFWIZ_UI_MINEXECTRY']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_CANTDETERMINEMINEXEC'] 	= '{$strings['COM_AKEEBA_CONFWIZ_UI_CANTDETERMINEMINEXEC']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_SAVEMINEXEC'] 			= '{$strings['COM_AKEEBA_CONFWIZ_UI_SAVEMINEXEC']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_CANTSAVEMINEXEC'] 		= '{$strings['COM_AKEEBA_CONFWIZ_UI_CANTSAVEMINEXEC']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_CANTFIXDIRECTORIES'] 	= '{$strings['COM_AKEEBA_CONFWIZ_UI_CANTFIXDIRECTORIES']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_CANTDBOPT'] 			    = '{$strings['COM_AKEEBA_CONFWIZ_UI_CANTDBOPT']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_EXECTOOLOW'] 			= '{$strings['COM_AKEEBA_CONFWIZ_UI_EXECTOOLOW']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_MINEXECTRY'] 			= '{$strings['COM_AKEEBA_CONFWIZ_UI_MINEXECTRY']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_SAVINGMAXEXEC'] 		    = '{$strings['COM_AKEEBA_CONFWIZ_UI_SAVINGMAXEXEC']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_CANTSAVEMAXEXEC'] 		= '{$strings['COM_AKEEBA_CONFWIZ_UI_CANTSAVEMAXEXEC']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_CANTDETERMINEPARTSIZE']	= '{$strings['COM_AKEEBA_CONFWIZ_UI_CANTDETERMINEPARTSIZE']}';
	Solo.Wizard.translation['COM_AKEEBA_CONFWIZ_UI_PARTSIZE'] 				= '{$strings['COM_AKEEBA_CONFWIZ_UI_PARTSIZE']}';
	
	Solo.Wizard.translation['COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE'] 			= '{$strings['COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE']}';

	Solo.Wizard.boot();
});
JS;
		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);

		// All done, show the page!
		return true;
	}
}