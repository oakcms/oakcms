<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\interfaces\model;

/**
 * Interface TranslatableInterface
 * Используется для получения данных о мультиязычности модели
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \yii\db\ActiveRecord[] $translations
 * @property string $language
 */
interface TranslatableInterface
{
    /**
     * Локализации текущей модели
     * @return static[]
     */
    public function getTranslations();

    /**
     * Язык
     * @return string
     */
    public function getLanguage();
}
