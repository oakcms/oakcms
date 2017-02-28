<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Mvc\DataModel\Behaviour;


use Awf\Database\Query;
use Awf\Event\Observer;
use Awf\Mvc\DataModel;
use Awf\Registry\Registry;

class RelationFilters extends Observer
{
	/**
	 * This event runs after we have built the query used to fetch a record list in a model. It is used to apply
	 * automatic query filters based on model relations.
	 *
	 * @param   DataModel  &$model  The model which calls this event
	 * @param   Query      &$query  The query we are manipulating
	 *
	 * @return  void
	 */
	public function onAfterBuildQuery(&$model, &$query)
	{
		$relationFilters = $model->getRelationFilters();

		foreach ($relationFilters as $filterState)
		{
			$relationName = $filterState['relation'];

			$subQuery = $model->getRelations()->getCountSubquery($relationName);
			$filter = new DataModel\Filter\Relation($model->getDbo(), $relationName, $subQuery);

			$options = new Registry($filterState);

			$methods = $filter->getSearchMethods();
			$method = $options->get('method', $filter->getDefaultSearchMethod());

			if (!in_array($method, $methods))
			{
				$method = 'exact';
			}

			switch ($method)
			{
				case 'between':
				case 'outside':
					$sql = $filter->$method($options->get('from', null), $options->get('to'));
					break;

				case 'interval':
					$sql = $filter->$method($options->get('value', null), $options->get('interval'));
					break;

				case 'search':
					$sql = $filter->$method($options->get('value', null), $options->get('operator', '='));
					break;

				default:
					$sql = $filter->$method($options->get('value', null));
					break;
			}

			if ($sql)
			{
				$query->where($sql);
			}

		}
	}
} 