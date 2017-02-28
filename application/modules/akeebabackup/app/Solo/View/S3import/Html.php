<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\S3import;


use Awf\Uri\Uri;
use Solo\Helper\Escape;

class Html extends \Solo\View\Html
{
	public function onBeforeMain()
	{
		$document = $this->container->application->getDocument();

		$model = $this->getModel();
		$model->getS3Credentials();
		$contents = $model->getContents();
		$buckets = $model->getBuckets();
		$bucketSelect = $model->getBucketsDropdown();
		$root = $model->getState('folder', '', 'raw');

		// Assign variables
		$this->s3access = $model->getState('s3access');
		$this->s3secret = $model->getState('s3secret');
		$this->buckets = $buckets;
		$this->bucketSelect = $bucketSelect;
		$this->contents = $contents;
		$this->root = $root;
		$this->crumbs = $model->getCrumbs();

		// Work around Safari which ignores autocomplete=off
		$escapedAccess = Escape::escapeJS($this->s3access);
		$escapedSecret = Escape::escapeJS($this->s3secret);

		$js = <<< JS
Solo.loadScripts.push(function ()
{
	setTimeout(function(){
		document.getElementById('s3access').value = 'DummyData';
		document.getElementById('s3access').value = '$escapedAccess';

		document.getElementById('s3secret').value = 'DummyData';
		document.getElementById('s3secret').value = '$escapedSecret';
	}, 500);
});

JS;
		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);


		return true;
	}

	public function onBeforeDownloadToServer()
	{
		$this->setLayout('downloading');
		$model = $this->getModel();

		$total = $model->getState('totalsize', 0, 'int');
		$done = $model->getState('donesize', 0, 'int');
		$part = $model->getState('part', 0, 'int') + 1;
		$parts = $model->getState('totalparts', 0, 'int');

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
		$this->total_parts = $parts;
		$this->current_part = $part;

		$step = $model->getState('step', 1, 'int') + 1;
		$location = Escape::escapeJS($this->getContainer()->router->route('index.php?view=s3import&layout=downloading&task=downloadToServer&step=' . $step));
		$js = <<< JS
Solo.loadScripts.push(function ()
{
	window.location = '$location';
});

JS;
		$this->container->application->getDocument()->addScriptDeclaration($js);

		return true;
	}
} 