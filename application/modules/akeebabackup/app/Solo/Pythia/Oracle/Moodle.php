<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Awf\Text\Text;
use Solo\Pythia\AbstractOracle;

class Moodle extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'moodle';

	/**
	 * Does this class recognises the site as a Moodle installation?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		if (!@file_exists($this->path . '/config.php'))
		{
			return false;
		}

		if (!@file_exists($this->path . '/version.php'))
		{
			return false;
		}

		if (!@is_dir($this->path . '/repository'))
		{
			return false;
		}

		if (!@is_dir($this->path . '/userpix'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Return the database connection information for this CMS / script
	 *
	 * @return  array
	 */
	public function getDbInformation()
	{
		$ret = array(
			'driver'	=> 'mysqli',
			'host'		=> '',
			'port'		=> '',
			'username'	=> '',
			'password'	=> '',
			'name'		=> '',
			'prefix'	=> '',
		);

		$filePath = $this->path . '/config.php';

		$fileContents = file($filePath);

		foreach ($fileContents as $line)
		{
			$line = trim($line);

			if (strpos($line, '$CFG->') === 0)
			{
				$line = trim(substr($line, 6));
				$line = trim(rtrim($line, ';'));
				list($key, $value) = explode('=', $line);
				$key = trim($key);
				$key = trim($key, "'\"");
				$value = trim($value);
				$value = $this->parseStringDefinition($value);

				switch (strtolower($key))
				{
					case 'dbname':
						$ret['name'] = $value;
						break;

					case 'dbuser':
						$ret['username'] = $value;
						break;

					case 'dbpass':
						$ret['password'] = $value;
						break;

					case 'dbhost':
						$ret['host'] = $value;
						break;

					case 'prefix':
						$ret['prefix'] = $value;
						break;

				}
			}
		}

		return $ret;
	}

	public function getExtradirs()
	{
		$ret      = array();
		$filePath = $this->path . '/config.php';

		$fileContents = file($filePath);

		foreach ($fileContents as $line)
		{
			$line = trim($line);

			if (strpos($line, '$CFG->') === 0)
			{
				$line = trim(substr($line, 6));
				$line = trim(rtrim($line, ';'));
				list($key, $value) = explode('=', $line);
				$key = trim($key);
				$key = trim($key, "'\"");
				$value = trim($value);
				$value = trim($value, "'\"");

				switch (strtolower($key))
				{
					case 'dataroot':
						$ret[] = $value;
						// I'm interested in the dataroot folder only
						break 2;
				}
			}
		}

		if(!$ret)
		{
			return $ret;
		}

		// In Core version there's no support for Extra directories
		if (!AKEEBABACKUP_PRO)
		{
			$text = '<input class="form-control" type="text" value="'.$ret[0].'" readonly/>';
			$text .= '<div class="alert alert-danger">'.Text::_('xxx').'</div>';

			return array($text);
		}

		// Ok, I have the extra directory, now let's add it to the extra-site folders
		/** @var \Solo\Model\Extradirs $extradirs */
		$extradirs = \Awf\Mvc\Model::getTmpInstance(null, 'Extradirs');
		$extradirs->setFilter('moodledata', array($ret[0],'moodledata'));

		return $ret;
	}

    public function getExtraDb()
    {
        return array();
    }
}