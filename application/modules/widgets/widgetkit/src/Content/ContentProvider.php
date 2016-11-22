<?php

namespace YOOtheme\Widgetkit\Content;

use YOOtheme\Framework\Application;
use YOOtheme\Framework\ApplicationAware;

class ContentProvider extends ApplicationAware
{
    protected $class = 'YOOtheme\Widgetkit\Content\Content';

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Gets the content object, if type object exists.
     *
     * @param  int $id
     * @return Content
     */
    public function get($id)
    {
        if ($content = $this->find($id) and $type = $this['types']->get($content->getType())) {
            $content->setTypeObject($type);
            return $content;
        }
    }

    /**
     * Gets the content object by id.
     *
     * @param  int $id
     * @return bool|Content
     */
    public function find($id)
    {
        return $this['db']->fetchObject('SELECT * FROM @widgetkit WHERE id = :id', compact('id'), $this->class);
    }

    /**
     * Gets all content objects.
     *
     * @return array
     */
    public function findAll()
    {
        $contents = array();

        foreach ($this['db']->fetchAllObjects('SELECT * FROM @widgetkit', array(), $this->class) as $content) {
            $contents[$content->getId()] = $content;
        }

        return $contents;
    }

    /**
     * Saves the content object.
     *
     * @param  array $data
     * @return array
     */
    public function save($data)
    {
        $store = $data;
        $store['data'] = json_encode($store['data']);

        if (!isset($store['id']) || !$store['id']) {
            $this['db']->insert('@widgetkit', $store);
            $data['id'] = $this['db']->lastInsertId();
        } else {
            $this['db']->update('@widgetkit', $store, array('id' => $store['id']));
        }

        return $data;
    }

    /**
     * Deletes the content object.
     *
     * @param  int $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this['db']->delete('@widgetkit', compact('id'));
    }

    /**
     * Creates content object from data.
     *
     * @param  array $data
     * @return Content
     */
    protected function hydrate($data)
    {
        $data['data'] = json_decode($data['data'], true);

        return new Content($data);
    }
}
