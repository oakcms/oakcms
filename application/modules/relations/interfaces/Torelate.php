<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\relations\interfaces;


/**
 * Interface Torelate
 * @package app\modules\relations\interfaces
 */
interface Torelate {

    /**
     * @return int
     */
    public function getId();


    /**
     * @return string
     */
    public function getName();
}
