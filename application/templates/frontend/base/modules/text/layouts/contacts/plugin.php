<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'contacts',
    'title' => Yii::t('text', 'Contacts'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/contacts/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/contacts/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'country1' => [
            'type' => 'textInput',
            'value' => 'Украина'
        ],
        'callCenterTitle1' => [
            'type' => 'textInput',
            'value' => 'Служба поддержки клиентов'
        ],
        'callCenterDescription1' => [
            'type' => 'textInput',
            'value' => 'Cвяжитесь с нами по телефону'
        ],
        'callCenterPhone1_1' => [
            'type' => 'textInput',
            'value' => '+38 050 371 98 00'
        ],
        'callCenterPhone1_2' => [
            'type' => 'textInput',
            'value' => '+38 067 362 04 74'
        ],
        'callCenterPhone1_3' => [
            'type' => 'textInput',
            'value' => '+38 050 111 11 11'
        ],
        'emailTitle1' => [
            'type' => 'textInput',
            'value' => 'Напишите нам на e-mail'
        ],
        'emailDescription1' => [
            'type' => 'textInput',
            'value' => 'Мы с удовольствием ответим на ваши вопросы по электронной почте'
        ],
        'email1' => [
            'type' => 'textInput',
            'value' => 'info@eurowork.com.ua'
        ],
        'address1' => [
            'type' => 'textInput',
            'value' => 'Мы находимся по адресу'
        ],
        'city1' => [
            'type' => 'textInput',
            'value' => 'г.Ровно'
        ],
        'street1' => [
            'type' => 'textInput',
            'value' => 'ул.Княгини Ольги'
        ],
        'numberOfHouse1' => [
            'type' => 'textInput',
            'value' => '5'
        ],
        'numberOfOffice1' => [
            'type' => 'textInput',
            'value' => 'оф. 314'
        ],
        'daysAtWork1' => [
            'type' => 'textInput',
            'value' => 'Пн-Пт'
        ],
        'hoursAtWork1' => [
            'type' => 'textInput',
            'value' => '09:00-18:00'
        ],
        'holidays1' => [
            'type' => 'textInput',
            'value' => 'Сб - Вc'
        ],
        'map1' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'country2' => [
            'type' => 'textInput',
            'value' => 'Poland'
        ],
        'callCenterTitle2' => [
            'type' => 'textInput',
            'value' => 'Служба поддержки клиентов'
        ],
        'callCenterDescription2' => [
            'type' => 'textInput',
            'value' => 'Cвяжитесь с нами по телефону'
        ],
        'callCenterPhone2_1' => [
            'type' => 'textInput',
            'value' => '+48 500 87 83 62'
        ],
        'callCenterPhone2_2' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'callCenterPhone2_3' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'emailTitle2' => [
            'type' => 'textInput',
            'value' => 'Напишите нам на e-mail'
        ],
        'emailDescription2' => [
            'type' => 'textInput',
            'value' => 'Мы с удовольствием ответим на ваши вопросы по электронной почте'
        ],
        'email2' => [
            'type' => 'textInput',
            'value' => 'info@eurowork.com.ua'
        ],
        'address2' => [
            'type' => 'textInput',
            'value' => 'Мы находимся по адресу'
        ],
        'city2' => [
            'type' => 'textInput',
            'value' => 'Katowice'
        ],
        'street2' => [
            'type' => 'textInput',
            'value' => 'ul.Sobieskiego'
        ],
        'numberOfHouse2' => [
            'type' => 'textInput',
            'value' => '11'
        ],
        'numberOfOffice2' => [
            'type' => 'textInput',
            'value' => 'оф. 314'
        ],
        'daysAtWork2' => [
            'type' => 'textInput',
            'value' => 'Пн-Пт'
        ],
        'hoursAtWork2' => [
            'type' => 'textInput',
            'value' => '09:00-18:00'
        ],
        'holidays2' => [
            'type' => 'textInput',
            'value' => 'Сб - Вc'
        ],
        'map2' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'form' => [
            'type' => 'formBuilder',
            'value' => ''
        ]
    ],
];
