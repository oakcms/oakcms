<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Awf\Filesystem\Factory;
use Awf\Mvc\Model;
use Awf\Utils\PhpTokenizer;
use Solo\Application;
use Solo\Pythia\AbstractOracle;

class Drupal8 extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'drupal8';

	/**
	 * Does this class recognises the CMS type as Drupal?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		if (!@file_exists($this->path . '/sites/default/settings.php'))
		{
			return false;
		}

        if (!@is_dir($this->path . '/core'))
        {
            return false;
        }

		if (!@is_dir($this->path . '/vendor'))
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
        static $ret = null;

        // Let's cache the database info, since it's quite expensive to retrieve (requires file parsing)
        if(!is_null($ret))
        {
            return $ret;
        }

		$ret = array(
			'driver'	=> 'mysqli',
			'host'		=> '',
			'port'		=> '',
			'username'	=> '',
			'password'	=> '',
			'name'		=> '',
			'prefix'	=> '',
		);

        // Sadly the settings file is a big php file, where variables are defined and functions are called
        // Moreover the user is supposed to manually edit it, so we can't really trust what's inside that
        // The only solution is to parse all PHP tokens and extract the info we need
		$filePath = $this->path . '/sites/default/settings.php';

        $tokenizer = new PhpTokenizer();
        $tokenizer->setCode(file_get_contents($filePath));

        $skip   = 0;
        $error  = false;
        $tokens = array();

        while(!$error)
        {
            try
            {
                // Let's try to extract all the occurrences until we get an error. Since it's just PHP code,
                // you could write it in a million of different ways
                $info = $tokenizer->searchToken('T_VARIABLE', '$databases', $skip);

                $skip     = $info['endLine'] + 1;
                $tokens[] = $info['data'];
            }
            catch(\RuntimeException $e)
            {
                $error = true;
            }
        }

        // Ok, now I got all the fragments I can truly evaluate them
        $databases = $this->extractVariables($tokens);

        if(isset($databases['default']) && isset($databases['default']['default']))
        {
            $curSettings = $databases['default']['default'];

            $ret['driver']   = $curSettings['driver'];
            $ret['host']     = $curSettings['host'];
            $ret['port']     = $curSettings['port'];
            $ret['username'] = $curSettings['username'];
            $ret['password'] = $curSettings['password'];
            $ret['name']     = $curSettings['database'];
            $ret['prefix']   = $curSettings['prefix'];
        }

		return $ret;
	}

    public function getExtraDb()
    {
        $extraDb = $this->findDbSettings();

        if(!$extraDb)
        {
            return $extraDb;
        }

        $app    = Application::getInstance();
        $mainDb = $this->getDbInformation();
        /** @var \Solo\Model\Multidb $multiDb */
        $multiDb   = Model::getInstance($app->getName(), 'Multidb', $app->getContainer());
        /** @var \Solo\Model\Dbfilters $dbFilters */
        $dbFilters = Model::getInstance($app->getName(), 'Dbfilters', $app->getContainer());

        foreach($extraDb as $subdomain => $extra)
        {
            if(($mainDb['host'] == $extra['host']) && ($mainDb['name'] == $extra['name']))
            {
                // If host and db name are the same, I don't have to add it, I simply have to exclude useless tables
                // (session, cache..) with a different prefix
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_bootstrap', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_config', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_container', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_data', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_default', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_discovery', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_entity', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_menu', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_render', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'cache_path', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'sessions', 'tabledata');
                $dbFilters->setFilter('[SITEDB]', $extra['prefix'].'watchdog', 'tabledata');
            }
            else
            {
                // Ok, this truly is a subsite stored in a different database. Let's add it as an extra db
                // Just a random string, there's no extra needs
                $root = md5(time());

                $data['host']     = $extra['host'];
                $data['driver']   = $extra['driver'];
                $data['port']     = $extra['port'];
                $data['username'] = $extra['username'];
                $data['password'] = $extra['password'];
                $data['database'] = $extra['name'];
                $data['prefix']   = $extra['prefix'];
                $data['dumpFile'] = substr($root, 0, 9).'-'.$subdomain.'-'.$data['database'].'.sql';

                // If a filter with the same rules exists, simply skip it
                if($multiDb->filterExists($data))
                {
                    continue;
                }

                $multiDb->setFilter($root, $data);

                // And then let's exclude some useless tables
                $dbFilters->setFilter($root, '#__cache_bootstrap', 'tabledata');
                $dbFilters->setFilter($root, '#__cache_config', 'tabledata');
                $dbFilters->setFilter($root, '#__cache_container', 'tabledata');
                $dbFilters->setFilter($root, '#__cache_data', 'tabledata');
                $dbFilters->setFilter($root, '#__cache_default', 'tabledata');
                $dbFilters->setFilter($root, '#__cache_discovery', 'tabledata');
                $dbFilters->setFilter($root, '#__cache_entity', 'tabledata');
                $dbFilters->setFilter($root, '#__cache_menu', 'tabledata');
                $dbFilters->setFilter($root, '#__cache_render', 'tabledata');
                $dbFilters->setFilter($root, '#__cache_path', 'tabledata');
                $dbFilters->setFilter($root, '#__sessions', 'tabledata');
                $dbFilters->setFilter($root, '#__watchdog', 'tabledata');
            }
        }

        return $extraDb;
    }

    private function extractVariables($fragments)
    {
        $fileSystem = Factory::getAdapter();
        $tmpFolder  = Application::getInstance()->getContainer()->temporaryPath;

        // Let's evaluate the code of every fragment. Since eval() could be disabled, let's do the write + include trick
        foreach($fragments as $fragment)
        {
            $file = tempnam($tmpFolder, 'pythia_');

            file_put_contents($file, "<?php \n".$fragment);

            @include $file;

            $fileSystem->delete($file);
        }

        if(isset($databases))
        {
            return $databases;
        }

        return null;
    }

    private function findDbSettings()
    {
        // Do I have a multi-site environment? If so I have to display the setup page several times
        $iterator    = new \DirectoryIterator($this->path.'/sites');
        $directories = array();
        $extraDb     = array();

        // First of all let's get all the directories. I have to exclude the "all" one since there could be a massive
        // amount of files/directories
        foreach($iterator as $file)
        {
            if($file->isDot() || !$file->isDir())
            {
                continue;
            }

            // Let's skip the "all" and "default" one, additional sites can't be there
            if($file->getFilename() == 'all' || $file->getFilename() == 'default')
            {
                continue;
            }

            $directories[] = $file->getPathname();
        }

        // Let's iterate over all the directories and find the settings.php file
        foreach($directories as $directory)
        {
            $file = $directory.'/settings.php';

            if(!file_exists($file))
            {
                continue;
            }

            $tokenizer = new PhpTokenizer(file_get_contents($file));

            $skip   = 0;
            $error  = false;
            $tokens = array();

            while(!$error)
            {
                try
                {
                    // Let's try to extract all the occurrences until we get an error. Since it's just PHP code,
                    // you could write it in a million of different ways
                    $info = $tokenizer->searchToken('T_VARIABLE', '$databases', $skip);

                    $skip     = $info['endLine'] + 1;
                    $tokens[] = $info['data'];
                }
                catch(\RuntimeException $e)
                {
                    $error = true;
                }
            }

            // Ok, now I got all the fragments I can truly evaluate them
            $databases = $this->extractVariables($tokens);

            if(isset($databases['default']) && isset($databases['default']['default']))
            {
                $curSettings = $databases['default']['default'];

                $ret['driver']   = $curSettings['driver'];
                $ret['host']     = $curSettings['host'];
                $ret['port']     = $curSettings['port'];
                $ret['username'] = $curSettings['username'];
                $ret['password'] = $curSettings['password'];
                $ret['name']     = $curSettings['database'];
                $ret['prefix']   = $curSettings['prefix'];

                // Let's save the directory (ie the subdomain) for later use
                $extraDb[basename($directory)] = $ret;
            }
        }

        return $extraDb;
    }
}