<?php

namespace YOOtheme\Framework\Yii;

use app\modules\admin\widgets\Html;
use YOOtheme\Framework\Csrf\DefaultCsrfProvider;

class CsrfProvider extends DefaultCsrfProvider
{
    /**
     * {@inheritdoc}
     */
    /*public function generate()
    {
        $csrf= new DefaultCsrfProvider();
        return $csrf->generate();
    }*/
}
