<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Solo\View\Setup;

use Awf\Mvc\View;
use Awf\Uri\Uri;

class Html extends View
{
    public $reqSettings;
    public $reqMet;
    public $recommendedSettings;
    public $recMet;
    public $params;
    public $connectionParameters;

    /**
	 * Executes before displaying the "main" task (initial requirements check page)
	 *
	 * @return  boolean
	 */
	public function onBeforeMain()
	{
		// Set up the page header and toolbar buttons
		$buttons = array(
			array(
				'title' => 'SOLO_BTN_NEXT',
				'class' => 'btn-primary',
				'url' => Uri::rebase('?view=setup&task=database', $this->container),
				'icon' => 'glyphicon glyphicon-chevron-right'
			),
		);
		$this->setupPageHeader($buttons);

		// Get the model
		/** @var \Solo\Model\Setup $model */
		$model = $this->getModel();

		// Push data from the model
		$this->reqSettings = $model->getRequired();
		$this->reqMet = $model->isRequiredMet();
		$this->recommendedSettings = $model->getRecommended();
		$this->recMet = $model->isRecommendedMet();

		return true;
	}

	public function onBeforeSession()
	{
		// Set up the page header and toolbar buttons
		$buttons = array(
			array(
				'title' => 'SOLO_BTN_NEXT',
				'class' => 'btn-primary',
				'onClick' => "Solo.System.triggerEvent('setupFormSubmit', 'click')",
				'icon' => 'glyphicon glyphicon-chevron-right'
			),
		);
		$this->setupPageHeader($buttons);

		// Get the model
		/** @var \Solo\Model\Setup $model */
		$model = $this->getModel();

		$this->params = $model->getSetupParameters();

		return true;
	}

	public function onBeforeDatabase()
	{
		// Set up the page header and toolbar buttons
		$buttons = array(
			array(
				'title' => 'SOLO_BTN_PREV',
				'class' => 'btn-default',
				'url' => Uri::rebase('?view=setup', $this->container),
				'icon' => 'glyphicon glyphicon-chevron-left'
			),
			array(
				'title' => 'SOLO_BTN_NEXT',
				'class' => 'btn-primary',
				'onClick' => "Solo.System.triggerEvent('dbFormSubmit', 'click')",
				'icon' => 'glyphicon glyphicon-chevron-right'
			),
		);
		$this->setupPageHeader($buttons);

		// Get the model
		/** @var \Solo\Model\Setup $model */
		$model = $this->getModel();

		// Push data from the model
		$this->connectionParameters = $model->getDatabaseParameters();

		return true;
	}

	public function onBeforeSetup()
	{
		// Set up the page header and toolbar buttons
		$buttons = array(
			array(
				'title' => 'SOLO_BTN_PREV',
				'class' => 'btn-default',
				'url' => Uri::rebase('?view=database', $this->container),
				'icon' => 'glyphicon glyphicon-chevron-left'
			),
			array(
				'title' => 'SOLO_BTN_NEXT',
				'class' => 'btn-primary',
				'onClick' => "Solo.System.triggerEvent('setupFormSubmit', 'click')",
				'icon' => 'glyphicon glyphicon-chevron-right'
			),
		);
		$this->setupPageHeader($buttons);

		// Get the model
		/** @var \Solo\Model\Setup $model */
		$model = $this->getModel();

		$this->params = $model->getSetupParameters();

		return true;
	}

	/**
	 * Set up the page header
	 *
	 * @param   array  $buttons  An array of button definitions to add to the toolbar
	 *
	 * @return void
	 */
	private function setupPageHeader($buttons = array())
	{
		$toolbar = $this->container->application->getDocument()->getToolbar();
		$toolbar->setTitle('SOLO_SETUP_TITLE');

		if (!empty($buttons))
		{
			foreach ($buttons as $button)
			{
				$toolbar->addButtonFromDefinition($button);
			}
		}
	}
}