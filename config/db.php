<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=novel',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60 * 60 * 24,
    'schemaCache' => 'cache',
];
