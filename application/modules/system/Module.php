<?php

namespace app\modules\system;


/**
 * system module definition class
 */
class Module extends \yii\base\Module
{

    const VERSION = '0.0.1-alpha.0.4';

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
        'system/<_c>/<_a:[\w\-]+>/page-<page:\d+>' => 'system/<_c>/<_a>',
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
