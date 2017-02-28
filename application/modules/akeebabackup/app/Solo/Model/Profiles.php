<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Model;

use Akeeba\Engine\Platform;
use Awf\Container\Container;
use Awf\Mvc\DataModel;
use Awf\Text\Text;
use RuntimeException;

class Profiles extends DataModel
{
	/**
	 * Public constructor
	 *
	 * @param   Container  $container  Configuration parameters
	 */
	public function __construct(\Awf\Container\Container $container = null)
	{
		$this->tableName = '#__ak_profiles';
		$this->idFieldName = 'id';

		parent::__construct($container);

		$this->addBehaviour('filters');
	}

	/**
	 * Prevent the deletion of the default backup profile
	 *
	 * @param   integer  $id  The profile ID which is about to be deleted
	 *
	 * @throws  \RuntimeException  When some wise guy tries to delete the default backup profile
	 */
	public function onBeforeDelete($id)
	{
		if ($id == 1)
		{
			throw new RuntimeException(Text::_('COM_AKEEBA_PROFILE_ERR_CANNOTDELETEDEFAULT'), 403);
		}

		// If you're deleting the current backup profile we have to switch to the default profile (#1)
		$activeProfile     = Platform::getInstance()->get_active_profile();

		if ($id == $activeProfile)
		{
			throw new RuntimeException(Text::sprintf('COM_AKEEBA_PROFILE_ERR_CANNOTDELETEACTIVE', $id), 500);
		}
	}

	/**
	 * Check the data for validity.
	 *
	 * @return  DataModel  Self, for chaining
	 *
	 * @throws \RuntimeException  When the data bound to this record is invalid
	 */
	public function check()
	{
		if (!$this->description)
		{
			throw new RuntimeException(Text::_('COM_AKEEBA_PROFILE_ERR_NODESCRIPTION'));
		}

		if (!$this->quickicon)
		{
			$this->quickicon = 0;
		}

		return parent::check();
	}

	/**
	 * Save a profile from imported configuration data. The $data array must contain the keys description (profile
	 * description), configuration (engine configuration INI data) and filters (inclusion and inclusion filters JSON
	 * configuration data).
	 *
	 * @param    array  $data  See above
	 *
	 * @returns  void
	 *
	 * @throws   RuntimeException  When an iport error occurs
	 */
	public function import($data)
	{
		// Check for data validity
		$isValid =
			is_array($data) &&
			!empty($data) &&
			array_key_exists('description', $data) &&
			array_key_exists('configuration', $data) &&
			array_key_exists('filters', $data);

		if (!$isValid)
		{
			throw new RuntimeException(Text::_('COM_AKEEBA_PROFILES_ERR_IMPORT_INVALID'));
		}

		// Unset the id, if it exists
		if (array_key_exists('id', $data))
		{
			unset($data['id']);
		}

		$data['akeeba.flag.confwiz'] = 1;

		// Try saving the profile
		$result = $this->save($data);

		if (!$result)
		{
			throw new RuntimeException(Text::_('COM_AKEEBA_PROFILES_ERR_IMPORT_FAILED'));
		}
	}
} 