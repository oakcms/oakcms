<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Solo;

use Awf\Database\Driver;

class Container extends \Awf\Container\Container
{
	public function __construct(array $values = array())
	{
		if (!isset($values['application_name']))
		{
			$values['application_name'] = 'Solo';
		}

		if (!isset($values['session_segment_name']))
		{
			$installationId = 'default';

			if (function_exists('base64_encode'))
			{
				$installationId = base64_encode(__DIR__);
			}

			if (function_exists('md5'))
			{
				$installationId = md5(__DIR__);
			}

			if (function_exists('sha1'))
			{
				$installationId = sha1(__DIR__);
			}

			$values['session_segment_name'] = $values['application_name'] . '_' . $installationId;
		}

        // Database Driver service
        if (!isset($this['db']))
        {
        	$this['db'] = function (Container $c)
            {
                $config = $c->appConfig;

                // Special case for SQLite: the db file will be located inside the assets/private folder
	            if (strtolower($config->get('dbdriver')) == 'sqlite')
	            {
		            // In SQLite DATABASE (name) == database (file)
		            if (!file_exists($config->get('dbname')))
		            {
			            $config->set('dbname', $c->basePath . '/assets/private/solo_db.db');
		            }
	            }

                return Driver::getInstance($c);
            };
        }

		parent::__construct($values);
	}
}