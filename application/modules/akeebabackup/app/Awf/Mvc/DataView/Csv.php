<?php
/**
 * @package     FOF
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace Awf\Mvc\DataView;

use Awf\Container\Container;
use Awf\Inflector\Inflector;
use Awf\Mvc\DataModel;
use Awf\Text\Text;

class Csv extends Raw
{
	/**
	 *  Should I produce a CSV header row.
	 *
	 * @var  boolean
	 */
	protected $csvHeader = true;

	/**
	 * The filename of the downloaded CSV file.
	 *
	 * @var  string
	 */
	protected $csvFilename = null;

	/**
	 * The columns to include in the CSV output. If it's empty it will be ignored.
	 *
	 * @var  array
	 */
	protected $csvFields = array();

    /**
     * Set to true if your onBefore* methods have already populated the item, items, limitstart etc properties used to
     * render a CSV document.
     *
     * @var bool
     */
    public $alreadyLoaded = false;


	/**
	 * Public constructor. Instantiates a F0FViewCsv object.
	 *
	 *
	 * @param   Container  $container  The container we belong to
	 * @param   array      $config     The configuration overrides for the view
	 */
	public function __construct(Container $container, array $config = array())
	{
		parent::__construct($container);

		if (array_key_exists('csv_header', $config))
		{
			$this->csvHeader = $config['csv_header'];
		}
		else
		{
			$this->csvHeader = $this->input->getBool('csv_header', true);
		}

		if (array_key_exists('csv_filename', $config))
		{
			$this->csvFilename = $config['csv_filename'];
		}
		else
		{
			$this->csvFilename = $this->input->getString('csv_filename', '');
		}

		if (empty($this->csvFilename))
		{
			$view = $this->input->getCmd('view', 'cpanel');
			$view = Inflector::pluralize($view);
			$this->csvFilename = strtolower($view) . '.csv';
		}

		if (array_key_exists('csv_fields', $config))
		{
			$this->csvFields = $config['csv_fields'];
		}
	}

	/**
	 * Overrides the default method to execute and display a template script.
	 * Instead of loadTemplate is uses loadAnyTemplate.
	 *
	 * @param   string $tpl The name of the template file to parse
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \Exception  When the layout file is not found
	 */
	public function display($tpl = null)
	{
        $method = 'onBefore' . ucfirst($this->doTask);

        if (method_exists($this, $method))
        {
            $result = $this->$method($tpl);

            if (!$result)
            {
                throw new \Exception(Text::_('AWF_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
            }
        }

		// Load the model
		/** @var DataModel $model */
		$model = $this->getModel();

        if (!$this->alreadyLoaded)
        {
            $this->limitStart = $model->getState('limitstart', 0);
            $this->limit = $model->getState('limit', 0);
            $this->items = $model->getItemsArray($this->limitStart, $this->limit);
            $this->total = $model->count();
        }

        $document = $this->container->application->getDocument();

		if ($document instanceof \Awf\Document\Csv)
		{
            $view = $this->container->input->getCmd('view', '');
			$document->setName($view);
		}

		if (is_null($tpl))
		{
			$tpl = 'csv';
		}

		$hasFailed = false;

		try
		{
			$result = $this->loadTemplate($tpl, true);

			if ($result instanceof \Exception)
			{
				$hasFailed = true;
			}
		}
		catch (\Exception $e)
		{
			$hasFailed = true;
		}

		if (!$hasFailed)
		{
			echo $result;
		}
		else
		{
            $items = $this->items;

			// Default CSV behaviour in case the template isn't there!
			$item    = $items->last();
			$keys    = $item->getData();
			$keys    = array_keys($keys);

			reset($items);

			if (!empty($this->csvFields))
			{
				$temp = array();

				foreach ($this->csvFields as $f)
				{
					$exist = false;

					// If we have a dot and it isn't part of the field name, we are dealing with relations
					if (!$model->hasField($f) && strpos($f, '.'))
					{
						$methods = explode('.', $f);
						$object = $item;
						// Let's see if the relation exists
						foreach ($methods as $method)
						{
							if (isset($object->$method))
							{
								$exist = true;
								$object = $object->$method;
							}
							else
							{
								$exist = false;
								break;
							}
						}
					}

					if (in_array($f, $keys))
					{
						$temp[] = $f;
					}
					elseif($exist)
					{
						$temp[] = $f;
					}
				}

				$keys = $temp;
			}

			if ($this->csvHeader)
			{
				$csv = array();

				foreach ($keys as $k)
				{
					$k = str_replace('"', '""', $k);
					$k = str_replace("\r", '\\r', $k);
					$k = str_replace("\n", '\\n', $k);
					$k = '"' . $k . '"';

					$csv[] = $k;
				}

				echo implode(",", $csv) . "\r\n";
			}

			foreach ($items as $item)
			{
				$csv  = array();

				foreach ($keys as $k)
				{
					// If our key contains a dot and it isn't part of the field name, we are dealing with relations
					if (!$model->hasField($k) && strpos($k, '.'))
					{
						$methods = explode('.', $k);
						$v = $item;

						foreach ($methods as $method)
						{
							$v = $v->$method;
						}
					}
					else
					{
						$v = $item->$k;
					}

					if (is_array($v))
					{
						$v = 'Array';
					}
					elseif (is_object($v))
					{
						$v = 'Object';
					}

					$v = str_replace('"', '""', $v);
					$v = str_replace("\r", '\\r', $v);
					$v = str_replace("\n", '\\n', $v);
					$v = '"' . $v . '"';

					$csv[] = $v;
				}

				echo implode(",", $csv) . "\r\n";
			}
		}

        $method = 'onAfter' . ucfirst($this->doTask);

        if (method_exists($this, $method))
        {
            $result = $this->$method($tpl);

            if (!$result)
            {
                throw new \Exception(Text::_('AWF_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
            }
        }

		return true;
	}
}
