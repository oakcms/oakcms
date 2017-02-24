Yii2-field
==========

С помощью данного модуля можно добавить поля для какой-то модели через веб-интерфейс. Типы полей на данный момент:

* Text
* Numeric
* Date
* Textarea
* Select
* Radio
* Checkbox
* Image (в разработке)

Для select, radio, checkbox можно заранее задавать в настройках варианты.

Установка
---------------------------------

Выполнить команду

```
php composer require pistol88/yii2-field "*"
```

Или добавить в composer.json

```
"pistol88/yii2-field": "*",
```

И выполнить

```
php composer update
```

Далее, мигрируем базу:

```
php yii migrate --migrationPath=vendor/pistol88/yii2-field/migrations
```

Подключение и настройка
---------------------------------

В конфигурационный файл приложения добавить модуль field, настроив его

```php
    'modules' => [
        //...
        'field' => [
            'class' => 'app\modules\field\Module',
            'relationModels' => [
                'common\models\User' => 'Пользователи',
                'app\modules\shop\models\Product' => 'Продукты',
            ],
            'adminRoles' => ['administrator'],
        ],
        //...
    ]
```

* relationModels - перечень моделей, к которым можно прикрепить поля

Управление полями: ?r=field/field

Для модели, с которой будут работать поля, добавить поведение:

```php 
    function behaviors() {
        return [
            'field' => [
                'class' => 'app\modules\field\behaviors\AttachFields',
            ],
        ];
    }
```


Использование
---------------------------------

Значение поля для модели вызывается через getField(), которому передается код поля.

```php
echo $model->getField('field_name');
```

Виджеты
---------------------------------

Блок выбора значений для для полей модели $model (вставлять в админке, рядом с формой редактирования):

```php
<?=\app\modules\field\widgets\Choice::widget(['model' => $model]);?>
```

Вывести все поля модели со значениями:
<?=app\modules\field\widgets\Show::widget(['model' => $model]);?>				
