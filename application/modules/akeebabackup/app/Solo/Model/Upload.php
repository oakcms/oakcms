<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Mvc\Model;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;

class Upload extends Model
{
	/**
	 * Uploads a fragment of the backup archive to the remote server
	 *
	 * @return  boolean|integer  False on failure, true on success, 1 if more work is required
	 */
	public function upload()
	{
		$id = $this->getState('id', -1);
		$part = $this->getState('part', -1);
		$frag = $this->getState('frag', -1);

		// Calculate the filenames
		$stat = Platform::getInstance()->get_statistics($id);
		$local_filename = $stat['absolute_path'];
		$basename = basename($local_filename);
		$extension = strtolower(str_replace(".", "", strrchr($basename, ".")));

		if ($part > 0)
		{
			$new_extension = substr($extension, 0, 1) . sprintf('%02u', $part);
		}
		else
		{
			$new_extension = $extension;
		}

		$filename = $basename . '.' . $new_extension;
		$local_filename = substr($local_filename, 0, -strlen($extension)) . $new_extension;

		// Load the Configuration object
		$session = $this->container->segment;
		$savedFactory = $session->get('upload_factory', null);

		if ($savedFactory && ($frag > 0))
		{
			Factory::unserialize($savedFactory);
		}
		else
		{
			Platform::getInstance()->load_configuration($stat['profile_id']);
		}

		// Load the post-processing engine
		Platform::getInstance()->load_configuration($stat['profile_id']);
		$config = Factory::getConfiguration();
		$engine_name = $config->get('akeeba.advanced.proc_engine');
		$engine = Factory::getPostprocEngine($engine_name);

		// Start uploading
		$result = $engine->processPart($local_filename);

		// Can't use switch because true == -1 but true !== -1 and we need the latter comparison
		if ($result === true)
		{
			$part++;
			$frag = 0;
		}
		elseif ($result === 1)
		{
			$frag++;
			$session->set('upload_factory', Factory::serialize());
		}
		elseif ($result === false)
		{
			$warning = $engine->getWarning();
			$error   = $engine->getError();
			$this->setState('errorMessage', empty($warning) ? $error : $warning);

			$session->set('upload_factory', null);
			$part = -1;
			$frag = -1;

			return false;
		}
		else
		{
			echo "Unexpected result from " . get_class($engine) . ": " . print_r($result, true);
			die;
		}

		$remote_filename = $config->get('akeeba.advanced.proc_engine', '') . '://';
		$remote_filename .= $engine->remote_path;

		if ($part >= 0)
		{
			if ($part >= $stat['multipart'])
			{
				// Update stats with remote filename
				$data = array(
					'remote_filename' => $remote_filename
				);

				Platform::getInstance()->set_or_update_statistics($id, $data, $engine);
			}
		}

		$this->setState('id', $id);
		$this->setState('part', $part);
		$this->setState('frag', $frag);
		$this->setState('stat', $stat);
		$this->setState('remotename', $remote_filename);

		return $result;
	}
} 