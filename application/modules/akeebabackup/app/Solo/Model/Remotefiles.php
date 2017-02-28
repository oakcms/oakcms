<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Mvc\Model;
use Awf\Text\Text;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Remotefiles extends Model
{
	/**
	 * Returns an icon definition list for the applicable actions on this backup record
	 *
	 * @return  array
	 */
	function getActions()
	{
		$id = $this->getState('id', -1);

		$actions = array();

		// Load the stats record
		$stat = Platform::getInstance()->get_statistics($id);

		// Get the post-proc engine from the remote location
		$remote_filename = $stat['remote_filename'];
		if (empty($remote_filename))
		{
			return $actions;
		}

		$remoteFilenameParts = explode('://', $remote_filename, 2);
		$engine = Factory::getPostprocEngine($remoteFilenameParts[0]);

		// Does the engine support local d/l and we need to d/l the file locally?
		if ($engine->can_download_to_file && !$stat['filesexist'])
		{
			// Add a "Fetch back to server" button
			$action = array(
				'label' => Text::_('COM_AKEEBA_REMOTEFILES_FETCH'),
				'link'  => "index.php?view=remotefiles&task=downloadToServer&tmpl=component&id={$stat['id']}&part=-1",
				'type'  => 'button',
				'icon'  => 'fa fa-cloud-download',
				'class' => 'btn btn-lg btn-primary',
			);
			$actions[] = $action;
		}

		// Does the engine support remote deletes?
		if ($engine->can_delete)
		{
			// Add a Delete button
			$action = array(
				'label' => Text::_('COM_AKEEBA_REMOTEFILES_DELETE'),
				'link'  => "index.php?view=remotefiles&task=delete&tmpl=component&id={$stat['id']}&part=-1",
				'type'  => 'button',
				'icon'  => 'fa fa-trash-o',
				'class' => 'btn btn-danger',
			);
			$actions[] = $action;
		}

		// Does the engine support downloads to browser?
		if ($engine->can_download_to_browser)
		{
			$parts = $stat['multipart'];
			if ($parts == 0)
			{
				$parts++;
			}
			for ($i = 0; $i < $parts; $i++)
			{
				$action = array(
					'label' => Text::sprintf('COM_AKEEBA_REMOTEFILES_PART', $i),
					'link'  => "index.php?&view=remotefiles&task=downloadFromRemote&id={$stat['id']}&part=$i",
					'type'  => 'link',
					'class' => '',
					'icon'  => 'fa fa-download',
				);
				$actions[] = $action;
			}
		}

		return $actions;
	}

	/**
	 * Downloads a remote file back to the site's server
	 *
	 * @return  array
	 */
	function downloadToServer()
	{
		$id = $this->getState('id', -1);
		$part = $this->getState('part', -1);
		$frag = $this->getState('frag', -1);

		$ret = array(
			'error'    => false,
			'finished' => false,
			'id'       => $id,
			'part'     => $part,
			'frag'     => $frag,
		);

		// Gather the necessary information to perform the download
		$stat = Platform::getInstance()->get_statistics($id);
		$remoteFilename = $stat['remote_filename'];
		$remoteFileNameParts = explode('://', $remoteFilename);
		$engine = Factory::getPostprocEngine($remoteFileNameParts[0]);
		$remote_filename = $remoteFileNameParts[1];

		// Load the correct backup profile
		Platform::getInstance()->load_configuration($stat['profile_id']);
		$config = Factory::getConfiguration();

		// Get a reference to the session object
		$session = $this->container->segment;

		// Start timing ourselves
		$timer = Factory::getTimer(); // The core timer object
		$start = $timer->getRunningTime(); // Mark the start of this download
		$break = false; // Don't break the step

		while ($timer->getTimeLeft() && !$break && ($part < $stat['multipart']))
		{
			// Get the remote and local filenames
			$basename = basename($remote_filename);
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
			$remote_filename = substr($remote_filename, 0, -strlen($extension)) . $new_extension;

			// Figure out where on Earth to put that file
			$local_file = $config->get('akeeba.basic.output_directory') . '/' . basename($remote_filename);

			// Do we have to initialize the process?
			if ($part == -1)
			{
				// Total size to download
				$session->set('dl_totalsize', $stat['total_size'], 'akeeba');
				// Currently downloaded size
				$session->set('dl_donesize', 0, 'akeeba');
				// Init
				$part = 0;
			}

			// Do we have to initialize the file?
			if ($frag == -1)
			{
				// Delete and touch the output file
				Platform::getInstance()->unlink($local_file);
				$fp = @fopen($local_file, 'wb');
				if ($fp !== false)
				{
					@fclose($fp);
				}
				// Init
				$frag = 0;
			}

			// Calculate from and length
			$length = 1048576;
			$from = $frag * $length + 1;

			// Try to download the first frag
			$temp_file = $local_file . '.tmp';
			$staggered = true;
			$required_time = 1.0;

			$result = $engine->downloadToFile($remote_filename, $temp_file, $from, $length);

			if ($result == -1)
			{
				// The engine doesn't support staggered downloads
				$staggered = false;
				$result = $engine->downloadToFile($remote_filename, $temp_file);
			}

			if (!$result)
			{
				// Failed download
				if (
					(
						(($part < $stat['multipart']) || (($stat['multipart'] == 0) && ($part == 0))) &&
						($frag == 0)
					)
					||
					!$staggered
				)
				{
					// Failure to download the part's beginning = failure to download. Period.
					$ret['error'] = Text::_('COM_AKEEBA_REMOTEFILES_ERR_CANTDOWNLOAD') . $engine->getWarning();

					return $ret;
				}
				elseif ($part >= $stat['multipart'])
				{
					// Just finished! Update the stats record.
					$stat['filesexist'] = 1;
					Platform::getInstance()->set_or_update_statistics($id, $stat, $engine);
					$ret['finished'] = true;

					return $ret;
				}
				else
				{
					// Since this is a staggered download, consider this normal and go to the next part.
					$part++;
					$frag = -1;
				}
			}

			// Add the currently downloaded frag to the total size of downloaded files
			if ($result)
			{
				$filesize = (int)@filesize($temp_file);
				$total = $session->get('dl_donesize', 0, 'akeeba');
				$total += $filesize;
				$session->set('dl_donesize', $total, 'akeeba');
			}

			// Successful download, or have to move to the next part.
			if ($staggered)
			{
				if ($result)
				{
					// Append the file
					$fp = @fopen($local_file, 'ab');
					if ($fp === false)
					{
						// Can't open the file for writing
						Platform::getInstance()->unlink($temp_file);
						$ret['error'] = Text::sprintf('COM_AKEEBA_REMOTEFILES_ERR_CANTOPENFILE', $local_file);

						return $ret;
					}
					$tf = fopen($temp_file, 'rb');
					while (!feof($tf))
					{
						$data = fread($tf, 262144);
						fwrite($fp, $data);
					}
					fclose($tf);
					fclose($fp);
					Platform::getInstance()->unlink($tf);
				}

				// Advance the frag pointer and mark the end
				$end = $timer->getRunningTime();
				$frag++;
			}
			else
			{
				if ($result)
				{
					// Rename the temporary file
					if (@file_exists($local_file))
					{
						$result = Platform::getInstance()->unlink($local_file);
					}

					if ($result)
					{
						$result = Platform::getInstance()->move($temp_file, $local_file);
					}
					else
					{
						$result = Platform::getInstance()->unlink($temp_file);
					}

					if (!$result)
					{
						// Renaming failed. Goodbye.
						Platform::getInstance()->unlink($temp_file);
						$ret['error'] = Text::sprintf('COM_AKEEBA_REMOTEFILES_ERR_CANTOPENFILE', $local_file);

						return $ret;
					}
				}
				// In whole part downloads we break the step without second thought
				$break = true;
				$end = $timer->getRunningTime();
				$frag = -1;
				$part++;
			}

			// Do we predict that we have enough time?
			$required_time = max(1.1 * ($end - $start), $required_time);
			if ($timer->getTimeLeft() < $required_time)
			{
				$break = true;
			}
			$start = $end;
		}

		// Pass the id, part, frag in the request so that the view can grab it
		$ret['id'] = $id;
		$ret['part'] = $part;
		$ret['frag'] = $frag;
		$this->setState('id', $id);
		$this->setState('part', $part);
		$this->setState('frag', $frag);

		if ($part >= $stat['multipart'])
		{
			// Just finished!
			$stat['filesexist'] = 1;
			Platform::getInstance()->set_or_update_statistics($id, $stat, $engine);
			$ret['finished'] = true;

			return $ret;
		}

		return $ret;
	}

	/**
	 * Deletes the backup archives from the remote server
	 *
	 * @return  array
	 */
	public function deleteRemoteFiles()
	{
		$id = $this->getState('id', -1);
		$part = $this->getState('part', -1);

		$ret = array(
			'error'    => false,
			'finished' => false,
			'id'       => $id,
			'part'     => $part
		);

		// Gather the necessary information to perform the delete
		$stat = Platform::getInstance()->get_statistics($id);
		$remoteFilename = $stat['remote_filename'];
		$rfparts = explode('://', $remoteFilename);
		$engine = Factory::getPostprocEngine($rfparts[0]);
		$remote_filename = $rfparts[1];

		// Load the correct backup profile
		Platform::getInstance()->load_configuration($stat['profile_id']);
		$config = Factory::getConfiguration();

		// Start timing ourselves
		$timer = Factory::getTimer(); // The core timer object
		$start = $timer->getRunningTime(); // Mark the start of this download
		$break = false; // Don't break the step

		while ($timer->getTimeLeft() && !$break && ($part < $stat['multipart']))
		{
			// Get the remote filename
			$basename = basename($remote_filename);
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
			$remote_filename = substr($remote_filename, 0, -strlen($extension)) . $new_extension;

			// Do we have to initialize the process?
			if ($part == -1)
			{
				// Init
				$part = 0;
			}

			// Try to delete the part
			$required_time = 1.0;
			$result = $engine->delete($remote_filename);

			if (!$result)
			{
				$ret['error'] = Text::_('COM_AKEEBA_REMOTEFILES_ERR_CANTDELETE') . $engine->getWarning();

				return $ret;
			}
			else
			{
				// Successful delete
				$end = $timer->getRunningTime();
				$part++;
			}

			// Do we predict that we have enough time?
			$required_time = max(1.1 * ($end - $start), $required_time);
			if ($timer->getTimeLeft() < $required_time)
			{
				$break = true;
			}
			$start = $end;
		}

		if ($part >= $stat['multipart'])
		{
			// Just finished!
			$stat['remote_filename'] = '';
			Platform::getInstance()->set_or_update_statistics($id, $stat, $engine);
			$ret['finished'] = true;

			return $ret;
		}
		else
		{
			// More work to do...
			$ret['id'] = $id;
			$ret['part'] = $part;

			return $ret;
		}
	}
} 