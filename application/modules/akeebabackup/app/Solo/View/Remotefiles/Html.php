<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Remotefiles;


class Html extends \Solo\View\Html
{
	public function onBeforeListActions()
	{
		/** @var \Solo\Model\Remotefiles $model */
		$model = $this->getModel();

		$this->actions = $model->getActions();

		return true;
	}

	public function onBeforeDownloadToServer()
	{
		/** @var \Solo\Model\Remotefiles $model */
		$model = $this->getModel();
		$id = $model->getState('id', 0);

		$this->setLayout('dlprogress');

		// Get progress bar stats
		$session = $this->container->segment;
		$total = $session->get('dl_totalsize', 0, 'akeeba');
		$done = $session->get('dl_donesize', 0, 'akeeba');

		if ($total <= 0)
		{
			$percent = 0;
		}
		else
		{
			$percent = (int)(100 * ($done / $total));

			if ($percent < 0)
			{
				$percent = 0;
			}

			if ($percent > 100)
			{
				$percent = 100;
			}
		}

		$this->total = $total;
		$this->done = $done;
		$this->percent = $percent;

		$this->id = $model->getState('id');
		$this->part = $model->getState('part');
		$this->frag = $model->getState('frag');

		// Render the progress bar
		$document = $this->container->application->getDocument();

		$script = <<< JS
Solo.loadScripts.push(function() {
	document.forms.adminForm.submit();
});

JS;

		$document->addScriptDeclaration($script);

		return true;
	}
} 