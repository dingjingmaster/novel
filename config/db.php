<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;port=3306;dbname=novel_online',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60 * 60 * 24,
    'schemaCache' => 'cache',
];
