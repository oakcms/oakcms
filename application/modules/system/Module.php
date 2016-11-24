<?php

namespace app\modules\system;

use app\modules\admin\models\ModulesModules;
use Yii;
use yii\base\Application;
use app\components\FrontendView;
use yii\helpers\VarDumper;

/**
 * system module definition class
 */
class Module extends \yii\base\Module
{

    const VERSION = '0.0.1';

    public $activeModules;

    public $settings = [
        'BackCallEmail' => [
            'type' => 'textInput',
            'value' => 'script@email.ua'
        ],
        'BackCallSubject' => [
            'type' => 'textInput',
            'value' => 'Новая заявка з сайта falconcity.kz'
        ],
        'BackCallSuccessText' => [
            'type' => 'textInput',
            'value' => 'Ваш запрос получен!<br>В ближайшее время наш менеджер свяжится с Вами!'
        ],
        'SocialInstagramLink' => [
            'type' => 'textInput',
            'value' => '#'
        ],
        'SocialTwitterLink' => [
            'type' => 'textInput',
            'value' => '#'
        ],
        'SocialFacebookLink' => [
            'type' => 'textInput',
            'value' => '#'
        ],
        'FrequentlyAskedQuestionsLink' => [
            'type' => 'textInput',
            'value' => '#'
        ],
    ];

    public static $urlRulesFrontend = [
        '<action:(?!admin|user)(.*)+>/<id:\d+>'        => 'system/default/<action>',
        '<action:(?!admin|user)(.*)+>/page-<page:\d+>' => 'system/default/<action>',
        '<action:(?!admin|user)(.*)+>'                 => 'system/default/<action>',
    ];

    public static $installConfig = [];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\system\controllers\frontend';

    /**
     * Функція яка повертає варсію системи:
     *
     * @return string
     **/
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Повертає копірайт:
     *
     * @return string
     **/
    public static function powered()
    {
        return \Yii::t('yii', 'Powered by {OakCMS}', [
            'OakCMS' => '<a href="http://www.oakcms.com/" rel="external">' . \Yii::t('yii', 'OakCMS') . '</a>'
        ]);
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

    }
}
