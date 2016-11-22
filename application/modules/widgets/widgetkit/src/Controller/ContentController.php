<?php

namespace YOOtheme\Widgetkit\Controller;

use YOOtheme\Framework\Routing\Controller;
use YOOtheme\Framework\Routing\Exception\HttpException;

class ContentController extends Controller
{
    public function indexAction()
    {
        return $this['view']->render('views/content.php');
    }

    public function queryContentAction()
    {
        $contents = new \ArrayObject;

        foreach ($this['content']->findAll() as $id => $content) {

            if (isset($this['types'][$content->getType()])) {
                $contents[$id] = $content->toArray();
            }
        }

        return $this['response']->json($contents);
    }

    public function getContentAction($id)
    {
        if ($content = $this['content']->find($id)) {
            return $this['response']->json($content->toArray());
        }

        throw new HttpException(404);
    }

    public function saveContentAction($content)
    {
        $status = !isset($content['id']) || !$content['id'] ? 201 : 200;

        if ($content = $this['content']->save($content)) {
            return $this['response']->json($content, $status);
        }

        throw new HttpException(400);
    }

    public function deleteContentAction($id)
    {
        if ($this['content']->delete($id)) {
            return $this['response']->json(null, 204);
        }

        throw new HttpException(400);
    }

    public static function getRoutes()
    {
        return array(
            array('index', 'indexAction', 'GET', array('access' => 'manage_widgetkit')),
            array('/content', 'queryContentAction', 'GET', array('access' => 'manage_widgetkit')),
            array('/content/:id', 'getContentAction', 'GET', array('access' => 'manage_widgetkit')),
            array('/content(/:id)', 'saveContentAction', 'POST', array('access' => 'manage_widgetkit')),
            array('/content/:id', 'deleteContentAction', 'DELETE', array('access' => 'manage_widgetkit'))
        );
    }
}
