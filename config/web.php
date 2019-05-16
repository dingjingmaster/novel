<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'enjoy read',
    'language' => 'zh-cn',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'defaultRoute' => 'index',              // 默认控制器
    'components' => [
        'request' => [
            'cookieValidationKey' => 'enjoy read',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /* URL美化 */
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => true,
            'suffix' => '.html',
            'rules' => [
                'GET / '                                => 'index/index', // 首页
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {                          // 调试模式下的组件
    $config['bootstrap'][] = 'debug';       // 调试组件
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';         // 代码生成插件
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
