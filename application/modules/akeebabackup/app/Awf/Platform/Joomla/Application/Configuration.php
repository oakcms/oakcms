<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\Application;

use Awf\Utils\Phpfunc;

/**
 * @property   \Awf\Platform\Joomla\Container\Container $container
 */
class Configuration extends \Awf\Application\Configuration
{
	/**
	 * Loads the configuration from the Joomla! global configuration itself. The component's options are loaded into
	 * the options key. For example, an option called foobar is accessible as $config->get('options.foobar');
	 *
	 * @param string  $filePath Ignored
	 * @param Phpfunc $phpfunc  Ignored
	 *
	 * @return  void
	 */
	public function loadConfiguration($filePath = null, Phpfunc $phpfunc = null)
	{
		// Get the Joomla! configuration object
		$jConfig = \JFactory::getConfig();

		// Create the basic configuration data
		$data = array(
			'timezone'	=> $jConfig->get('offset', 'UTC'),
			'fs'		=> array(
				'driver'	=> 'file'
			),
			'dateformat'	=> \JText::_('DATE_FORMAT_LC2'),
			'base_url'	=> \JUri::base() . 'index.php?option=com_' . strtolower($this->container->extension_name),
			'live_site'	=> \JUri::base() . 'index.php?option=com_' . strtolower($this->container->extension_name),
			'cms_url'	=> \JUri::base(),
			'options'	=> new \stdClass(),
		);

		// Populate the options key with the component configuration
		$db = $this->container->db;

		$sql = $db->getQuery(true)
			->select($db->qn('params'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element') . " = " . $db->q('com_' . strtolower($this->container->extension_name)));

		try
		{
			$configJson = $db->setQuery($sql)->loadResult();
		}
		catch (\Exception $e)
		{
			$configJson = null;
		}

		if (!empty($configJson))
		{
			$data['options'] = json_decode($configJson, true);
		}

		// Finally, load the data to the registry class
		$this->data = new \stdClass();
		$this->loadArray($data);
	}

	/**
	 * This method will only save the Joomla!-specific configuration to the #__extensions table. This means only
	 * everything under the "options" key.
	 *
	 * @param   string $filePath Ignored
	 *
	 * @return  void
	 *
	 * @throws  \Exception  If we can't save to the database for any reason
	 */
	public function saveConfiguration($filePath = null)
	{
		$optionsRaw = $this->get('options', array());
		$optionsRaw = (array)$optionsRaw;
		$optionsJson = json_encode($optionsRaw);

		$db = $this->container->db;

		$sql = $db->getQuery(true)
			->update($db->qn('#__extensions'))
			->set($db->qn('params') . ' = ' . $db->q($optionsJson))
			->where($db->qn('element') . " = " . $db->q('com_' . strtolower($this->container->extension_name)));

		$db->setQuery($sql)->execute();
	}
}