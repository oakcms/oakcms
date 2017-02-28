<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Router\Router;
use Awf\Text\Text;

class Update extends ControllerDefault
{
	public function main()
	{
		$force = $this->input->getInt('force', 0) == 1;

		/** @var \Solo\Model\Update $model */
		$model = $this->getModel();
		$model->load($force);

		parent::main();
	}

	public function download()
	{
		/** @var \Solo\Model\Update $model */
		$model = $this->getModel();
		$model->prepareDownload();

		$this->layout = 'download';

		$this->display();
	}

	public function downloader()
	{
		$json = $this->input->get('json', '', 'raw');
		$params = json_decode($json, true);

		/** @var \Solo\Model\Update $model */
		$model = $this->getModel();

		if (is_array($params) && !empty($params))
		{
			foreach ($params as $k => $v)
			{
				$model->setState($k, $v);
			}
		}

		$ret = $model->stepDownload();

		echo '###' . json_encode($ret) . '###';
	}

	public function extract()
	{
		$this->csrfProtection();

		$this->layout = 'extract';

		/** @var \Solo\Model\Update $model */
		$model = $this->getModel();
		$model->createRestorationINI();

		$this->display();
	}

	public function finalise()
	{
		// Do not add CSRF protection in this view; it called after the
		// installation of the update. At this point the session MAY have
		// already expired.

		/** @var \Solo\Model\Update $model */
		$model = $this->getModel();
		$model->finalise();

		$router = $this->container->router;

		$this->setRedirect($router->route('index.php?view=update&force=1'), Text::_('SOLO_UPDATE_COMPLETE_OK'), 'success');
	}
} 