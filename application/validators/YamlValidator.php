<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\validators;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Yii;
use yii\validators\Validator;

class YamlValidator extends Validator
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('admin', '"{attribute}" must be a valid YAML');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateValue($value)
    {
        try {
            Yaml::parse($value);
        } catch (ParseException $e) {
            return [$this->message, []];
        }
    }
}
