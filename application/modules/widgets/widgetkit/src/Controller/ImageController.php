<?php

namespace YOOtheme\Widgetkit\Controller;

use YOOtheme\Framework\Routing\Controller;
use YOOtheme\Framework\Routing\Exception\HttpException;

class ImageController extends Controller
{
    public function imageAction($file, $hash, $w = '', $h = '', $strategy = '')
    {
        if (!$image = $this['image']->create($file)) {
            throw new HttpException(404);
        }

        $image->setOptions(compact('w', 'h', 'strategy'));

        if ($image->getHash() !== $hash) {
            throw new HttpException(401);
        }

        $image->cache(true);

        return $this['response']->raw($image->output());
    }

    public static function getRoutes()
    {
        return array(
            array('image', 'imageAction')
        );
    }
}
