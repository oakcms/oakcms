<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\interfaces\module;

/**
 * Interface BootstrapInterface
 * Используется модулями для автоматического бутсрапа в Grom Platform
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
interface BootstrapInterface
{
    /**
     * @param $app \yii\base\Application
     */
    public function bootstrap($app);
}
