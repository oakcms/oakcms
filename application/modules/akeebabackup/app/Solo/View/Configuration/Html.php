<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Configuration;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Awf\Mvc\Model;
use Awf\Utils\Template;
use Solo\Application;
use Solo\Helper\Escape;
use Awf\Uri\Uri;
use Solo\Model\Profiles;

/**
 * The view class for the Configuration view
 */
class Html extends \Solo\View\Html
{
	public $json;

	public $profileId;

	public $profileName;

	public $quickicon;

	public $secureSettings = 0;

	public $mediadir;

	public function onBeforeMain()
	{
		$document = $this->container->application->getDocument();

		// Load the necessary Javascript
		Template::addJs('media://js/solo/configuration.js', $this->container->application);

		// Push configuration in JSON format
		$this->json = Factory::getEngineParamsProvider()->getJsonGuiDefinition();

		// Push the profile's numeric ID
		$this->profileId = Platform::getInstance()->get_active_profile();

		// Push the profile name
		/** @var Profiles $profile */
		$profile = Model::getTmpInstance('', 'Profiles');
		$profile->find($this->profileId);

		$this->profileName = $this->escape($profile->description);
		$this->quickicon = (int) $profile->quickicon;

		// Are the settings secured?
		if (Platform::getInstance()->get_platform_configuration_option('useencryption', -1) == 0)
		{
			$this->secureSettings = -1;
		}
		elseif (!Factory::getSecureSettings()->supportsEncryption())
		{
			$this->secureSettings = 0;
		}
		else
		{
			$filename = $this->container->basePath . Application::secretKeyRelativePath;

			if (@file_exists($filename))
			{
				$this->secureSettings = 1;
			}
			else
			{
				$this->secureSettings = 0;
			}
		}

		// Push the media folder name @todo Do we really use it?
		$media_folder = URI::base(false, $this->container) . '/media/';
		$this->mediadir = Escape::escapeJS($media_folder . 'theme/');

		// Append buttons to the toolbar
		$buttons = array(
			array(
				'title' => 'SOLO_BTN_SAVECLOSE',
				'class' => 'btn-success',
				'onClick' => 'Solo.System.submitForm(\'adminForm\', \'save\')',
				'icon' => 'glyphicon glyphicon-floppy-save'
			),
			array(
				'title' => 'SOLO_BTN_SAVE',
				'class' => 'btn-default',
				'onClick' => 'Solo.System.submitForm(\'adminForm\', \'apply\')',
				'icon' => 'glyphicon glyphicon-ok'
			),
			array(
				'title' => 'SOLO_BTN_SAVENEW',
				'class' => 'btn-default',
				'onClick' => 'Solo.System.submitForm(\'adminForm\', \'savenew\')',
				'icon' => 'glyphicon glyphicon-floppy-saved'
			),
			array(
				'title' => 'SOLO_BTN_CANCEL',
				'class' => 'btn-warning',
				'onClick' => 'Solo.System.submitForm(\'adminForm\', \'cancel\')',
				'icon' => 'glyphicon glyphicon-remove'
			),
			array(
				'title' => 'COM_AKEEBA_CONFWIZ',
				'class' => 'btn-primary',
				'url'   => $this->container->router->route('index.php?view=wizard'),
				'icon'  => 'glyphicon glyphicon-flash'
			),
			array(
				'title' => 'COM_AKEEBA_SCHEDULE',
				'class' => 'btn-default',
				'url'   => $this->container->router->route('index.php?view=schedule'),
				'icon'  => 'glyphicon glyphicon-calendar'
			),
		);


		$toolbar = $document->getToolbar();
		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}

		// All done, show the page!
		return true;
	}
}