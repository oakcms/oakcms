<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'ourServices',
    'title' => Yii::t('text', 'Our Services'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/ourServices/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/ourServices/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'title' => [
            'type' => 'textInput',
            'value' => 'Наши услуги'
        ],
        'description' => [
            'type' => 'textInput',
            'value' => 'это комплексное предложение для соискателей, подразумевающее трудоустройство за границей для украинцев.'
        ],
        'firstBlockTitle' => [
            'type' => 'textInput',
            'value' => '100% гарантия результата'
        ],
        'firstBlockDescription' => [
            'type' => 'textInput',
            'value' => 'За 10 лет нашей работы мы сформировали большую базу проверенных и надежных работодателей.'
        ],
        'firstBlockImage' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'firstBlockImageAlt' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'secondBlockTitle' => [
            'type' => 'textInput',
            'value' => 'Поддержка менеджера'
        ],
        'secondBlockDescription' => [
            'type' => 'textInput',
            'value' => 'С момента первого звонка и на всех этапах трудоустройства Вас сопровождает личный менеджер'
        ],
        'secondBlockImage' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'secondBlockImageAlt' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'thirdBlockTitle' => [
            'type' => 'textInput',
            'value' => 'Наличие бесплатных вакансий'
        ],
        'thirdBlockDescription' => [
            'type' => 'textInput',
            'value' => 'Вы получаете специальные предложения, согласно вашей специальности'
        ],
        'thirdBlockImage' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'thirdBlockImageAlt' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'fourthBlockTitle' => [
            'type' => 'textInput',
            'value' => '10 лет на рынке'
        ],
        'fourthBlockDescription' => [
            'type' => 'textInput',
            'value' => 'Вы получаете прямую вакансию от  работодателя, проверенного временем'
        ],
        'fourthBlockImage' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'fourthBlockImageAlt' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'fifthBlockTitle' => [
            'type' => 'textInput',
            'value' => '437 проверенных работодателей'
        ],
        'fifthBlockDescription' => [
            'type' => 'textInput',
            'value' => 'Вы имеете возможность работать вместе с украинцами, которые уже воспользовались нашими услугами'
        ],
        'fifthBlockImage' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'fifthBlockImageAlt' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'sixthBlockTitle' => [
            'type' => 'textInput',
            'value' => 'Индивидуальный подход'
        ],
        'sixthBlockDescription' => [
            'type' => 'textInput',
            'value' => 'Мы работаем не с группами клиентов, а с каждым человеком индивидуально'
        ],
        'sixthBlockImage' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'sixthBlockImageAlt' => [
            'type' => 'textInput',
            'value' => ''
        ]
    ],
];
