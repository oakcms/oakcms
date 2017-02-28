<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Mvc\DataModel\Relation;

use Awf\Application\Application;
use Awf\Database\Query;
use Awf\Mvc\DataModel;
use Awf\Mvc\DataModel\Collection;

/**
 * BelongsTo (reverse 1-to-1 or 1-to-many) relation: this model is a child which belongs to the foreign table
 *
 * For example, parentModel is Articles and foreignModel is Users. Each article belongs to one user. One user can have
 * one or more article.
 *
 * Example #2: parentModel is Phones and foreignModel is Users. Each phone belongs to one user. One user can have zero
 * or one phones.
 *
 * @package Awf\Mvc\DataModel
 */
class BelongsTo extends HasOne
{
	/**
	 * Public constructor. Initialises the relation.
	 *
	 * @param   DataModel  $parentModel        The data model we are attached to
	 * @param   string     $foreignModelClass  The class name of the foreign key's model
	 * @param   string     $localKey           The local table key for this relation, default: parentModel's ID field name
	 * @param   string     $foreignKey         The foreign key for this relation, default: parentModel's ID field name
	 * @param   string     $pivotTable         IGNORED
	 * @param   string     $pivotLocalKey      IGNORED
	 * @param   string     $pivotForeignKey    IGNORED
	 */
	public function __construct(DataModel $parentModel, $foreignModelClass, $localKey = null, $foreignKey = null, $pivotTable = null, $pivotLocalKey = null, $pivotForeignKey = null)
	{
		parent::__construct($parentModel, $foreignModelClass, $localKey, $foreignKey, $pivotTable, $pivotLocalKey, $pivotForeignKey);

		if (empty($localKey))
		{
			// Get a model instance
			$container = Application::getInstance($this->foreignModelApp)->getContainer();
			/** @var DataModel $foreignModel */
			$foreignModel = DataModel::getTmpInstance($this->foreignModelApp, $this->foreignModelName, $container);

			$this->localKey = $foreignModel->getIdFieldName();
		}

		if (empty($foreignKey))
		{
			if (!isset($foreignModel))
			{
				// Get a model instance
				$container = Application::getInstance($this->foreignModelApp)->getContainer();
				/** @var DataModel $foreignModel */
				$foreignModel = DataModel::getTmpInstance($this->foreignModelApp, $this->foreignModelName, $container);
			}

			$this->foreignKey = $foreignModel->getIdFieldName();
		}
	}

	/**
	 * This is not supported by the belongsTo relation
	 *
	 * @throws DataModel\Relation\Exception\NewNotSupported when it's not supported
	 */
	public function getNew()
	{
		throw new DataModel\Relation\Exception\NewNotSupported("getNew() is not supported by the belongsTo relation type");
	}

}