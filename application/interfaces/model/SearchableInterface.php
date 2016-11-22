<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\interfaces\model;

/**
 * Interface SearchableInterface
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
interface SearchableInterface
{
    /**
     * @return string
     */
    public function getSearchTitle();

    /**
     * @return string
     */
    public function getSearchContent();

    /**
     * ['sport', 'policy', ...]
     * @return array
     */
    public function getSearchTags();
}
