<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Mvc\DataModel\Filter;

use Awf\Database\Driver;
use Awf\Database\Query;

class Relation extends Number
{
	/** @var Query The COUNT subquery to filter by */
	protected $subQuery = null;

	public function __construct($db, $relationName, $subQuery)
	{
		$field = (object)array(
			'name'	=> $relationName,
			'type'	=> 'relation',
		);

		parent::__construct($db, $field);

		$this->subQuery = $subQuery;
	}

	public function callback($value)
	{
		return call_user_func($value, $this->subQuery);
	}

	public function getFieldName()
	{
		return '(' . (string)$this->subQuery . ')';
	}
}