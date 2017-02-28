<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Akeeba\Engine\Factory;

/** @var \Solo\View\Profiles\Json $this */

/** @var \Solo\Model\Profiles $model */
$model = $this->getModel();

$data = $model->toArray();

if (substr($data['configuration'], 0, 12) == '###AES128###')
{
	// Load the server key file if necessary
	$key = Factory::getSecureSettings()->getKey();

	$data['configuration'] = Factory::getSecureSettings()->decryptSettings($data['configuration'], $key);
}

$defaultName = $this->input->get('view', 'joomla', 'cmd');
$filename = $this->input->get('basename', $defaultName, 'cmd');

$document = $this->container->application->getDocument();
$document->setName($filename);

echo json_encode($data);