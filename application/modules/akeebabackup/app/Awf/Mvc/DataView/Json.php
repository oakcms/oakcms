<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Mvc\DataView;

use Awf\Container\Container;
use Awf\Hal\Link;
use Awf\Mvc\DataModel;
use Awf\Pagination\Pagination;
use Awf\Text\Text;
use Awf\Uri\Uri;

class Json extends Raw
{
	/**
	 * When set to true we'll add hypermedia to the output, implementing the
	 * HAL specification (http://stateless.co/hal_specification.html)
	 *
	 * @var   boolean
	 */
	public $useHypermedia = false;

	/**
	 * Set to true if your onBefore* methods have already populated the item, items, limitstart etc properties used to
	 * render a JSON document.
	 *
	 * @var bool
	 */
	public $alreadyLoaded = false;

	/**
	 * Public constructor
	 *
	 * @param   Container  $container
	 * @param   array      $config  The component's configuration array
	 */
	public function __construct($container = null, $config = null)
	{
		parent::__construct($container);

		if (isset($config['use_hypermedia']))
		{
			$this->useHypermedia = (bool) $config['use_hypermedia'];
		}
	}

	/**
	 * Overrides the default method to execute and display a template script.
	 * Instead of loadTemplate is uses loadAnyTemplate.
	 *
	 * @param   string  $tpl  The name of the template file to parse
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

	/**
	 * The event which runs when we are displaying the record list JSON view
	 *
	 * @param   string  $tpl  The sub-template to use
	 *
	 * @return  boolean  True to allow display of the view
	 */
	public function onBeforeBrowse($tpl = null)
	{
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

		if ($document instanceof \Awf\Document\Json)
		{
			$document->setUseHashes(false);

			if ($this->useHypermedia)
			{
				$document->setMimeType('application/hal+json');
			}
			else
			{
				$document->setMimeType('application/json');
			}
		}

		if (is_null($tpl))
		{
			$tpl = 'json';
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

		if ($hasFailed)
		{
			// Default JSON behaviour in case the template isn't there!
			if ($this->useHypermedia)
			{
                $data = array();

                foreach($this->items as $item)
                {
                    if(is_object($item) && method_exists($item, 'toArray'))
                    {
                        $data[] = $item->toArray();
                    }
                    else
                    {
                        $data[] = $item;
                    }
                }

				$HalDocument = $this->_createDocumentWithHypermedia($data, $model);
				$json = $HalDocument->render('json');
			}
			else
			{
                $result = array();

                foreach($this->items as $item)
                {
                    if(is_object($item) && method_exists($item, 'toArray'))
                    {
                        $result[] = $item->toArray();
                    }
                    else
                    {
                        $result[] = $item;
                    }
                }

				$json = json_encode($result);
			}

			// JSONP support
			$callback = $this->input->get('callback', null, 'raw');

			if (!empty($callback))
			{
				echo $callback . '(' . $json . ')';
			}
			else
			{
				$defaultName = $this->input->get('view', 'main', 'cmd');
				$filename = $this->input->get('basename', $defaultName, 'cmd');

				$document->setName($filename);
				echo $json;
			}
		}
		else
		{
			echo $result;
		}

		return true;
	}

	/**
	 * The event which runs when we are displaying a single item JSON view
	 *
	 * @param   string  $tpl  The view sub-template to use
	 *
	 * @return  boolean  True to allow display of the view
	 */
	protected function onBeforeRead($tpl = null)
	{
		// Load the model
		/** @var DataModel $model */
		$model = $this->getModel();

		if (!$this->alreadyLoaded)
		{
			$this->item = $model->find();
		}


		$document = $this->container->application->getDocument();

		if ($document instanceof \Awf\Document\Json)
		{
			$document->setUseHashes(false);

			if ($this->useHypermedia)
			{
				$document->setMimeType('application/hal+json');
			}
			else
			{
				$document->setMimeType('application/json');
			}
		}

		if (is_null($tpl))
		{
			$tpl = 'json';
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

		if ($hasFailed)
		{
			// Default JSON behaviour in case the template isn't there!

			if ($this->useHypermedia)
			{
				$haldocument = $this->_createDocumentWithHypermedia($this->item, $model);
				$json = $haldocument->render('json');
			}
			else
			{
                if(is_object($this->item) && method_exists($this->item, 'toArray'))
                {
                    $json = json_encode($this->item->toArray());
                }
                else
                {
                    $json = json_encode($this->item);
                }

			}

			// JSONP support
			$callback = $this->input->get('callback', null);

			if (!empty($callback))
			{
				echo $callback . '(' . $json . ')';
			}
			else
			{
				$defaultName = $this->input->get('view', 'main', 'cmd');
				$filename = $this->input->get('basename', $defaultName, 'cmd');
				$document->setName($filename);

				echo $json;
			}
		}
		else
		{
			echo $result;
		}

		return true;
	}

	/**
	 * Creates a FOFHalDocument using the provided data
	 *
	 * @param   array      $data   The data to put in the document
	 * @param   DataModel  $model  The model of this view
	 *
	 * @return  \Awf\Hal\Document  A HAL-enabled document
	 */
	protected function _createDocumentWithHypermedia($data, $model = null)
	{
		// Create a new HAL document

		if (is_array($data))
		{
			$count = count($data);
		}
		else
		{
			$count = null;
		}

		if ($count == 1)
		{
			reset($data);
			$document = new \Awf\Hal\Document(end($data));
		}
		else
		{
			$document = new \Awf\Hal\Document($data);
		}

		// Create a self link
		$router = $this->container->router;

		$uri = (string) (Uri::getInstance());
		$uri = $this->_removeURIBase($uri);
		$uri = $router->route($uri);
		$document->addLink('self', new Link($uri));

		// Create relative links in a record list context
		if (is_array($data) && ($model instanceof DataModel))
		{
            if(!isset($this->total))
            {
                $this->total = $model->count();
            }

            if(!isset($this->limitStart))
            {
                $this->limitStart = $model->getState('limitstart', 0);
            }

            if(!isset($this->limit))
            {
                $this->limit = $model->getState('limit', 0);
            }

			$pagination = new Pagination($this->total, $this->limitStart, $this->limit, 10, $this->container->application);

			if ($pagination->pagesTotal > 1)
			{
				// Try to guess URL parameters and create a prototype URL
				// NOTE: You are better off specialising this method
				$protoUri = $this->_getPrototypeURIForPagination();

				// The "first" link
				$uri = clone $protoUri;
				$uri->setVar('limitstart', 0);
				$uri = $router->route($uri);

				$document->addLink('first', new Link($uri));

				// Do we need a "prev" link?
				if ($pagination->pagesCurrent > 1)
				{
					$prevPage = $pagination->pagesCurrent - 1;
					$limitstart = ($prevPage - 1) * $pagination->limit;
					$uri = clone $protoUri;
					$uri->setVar('limitstart', $limitstart);
					$uri = $router->route($uri);

					$document->addLink('prev', new Link($uri));
				}

				// Do we need a "next" link?
				if ($pagination->pagesCurrent < $pagination->pagesTotal)
				{
					$nextPage = $pagination->pagesCurrent + 1;
					$limitstart = ($nextPage - 1) * $pagination->limit;
					$uri = clone $protoUri;
					$uri->setVar('limitstart', $limitstart);
					$uri = $router->route($uri);

					$document->addLink('next', new Link($uri));
				}

				// The "last" link?
				$lastPage = $pagination->pagesTotal;
				$limitstart = ($lastPage - 1) * $pagination->limit;
				$uri = clone $protoUri;
				$uri->setVar('limitstart', $limitstart);
				$uri = $router->route($uri);

				$document->addLink('last', new Link($uri));
			}
		}

		return $document;
	}

	/**
	 * Convert an absolute URI to a relative one
	 *
	 * @param   string  $uri  The URI to convert
	 *
	 * @return  string  The relative URL
	 */
	protected function _removeURIBase($uri)
	{
		static $root = null, $rootlen = 0;

		if (is_null($root))
		{
			$root = rtrim(Uri::base(false, $this->container), '/');
			$rootlen = strlen($root);
		}

		if (substr($uri, 0, $rootlen) == $root)
		{
			$uri = substr($uri, $rootlen);
		}

		return ltrim($uri, '/');
	}

	/**
	 * Returns a JUri instance with a prototype URI used as the base for the
	 * other URIs created by the JSON renderer
	 *
	 * @return  Uri  The prototype JUri instance
	 */
	protected function _getPrototypeURIForPagination()
	{
		$protoUri = new Uri(Uri::rebase('index.php', $this->container));
		$protoUri->setQuery($this->input->getData());
		$protoUri->delVar('savestate');
		$protoUri->delVar('base_path');

		return $protoUri;
	}
}