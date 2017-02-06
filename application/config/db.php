<?php

if (in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=kn-group',
        'username' => 'root',
        'password' => '',
        'charset' => getenv('DB_CHARSET'),
        'tablePrefix' => getenv('DB_TABLE_PREFIX'),
        'enableSchemaCache' => !YII_DEBUG,
        'schemaCacheDuration' => 3600,
        'schemaCache' => 'cache',
    ];
} else {
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME'),
        'username' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
        'charset' => getenv('DB_CHARSET'),
        'tablePrefix' => getenv('DB_TABLE_PREFIX'),
        'enableSchemaCache' => !YII_DEBUG,
        'schemaCacheDuration' => 3600,
        'schemaCache' => 'cache',
    ];
}
