<?php
namespace app\assets;

use yii\web\AssetBundle;

class NovelAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'web/css/style.css',
    ];
    public $js = [
        'web/js/vue.js',
        'web/js/index.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}

/* 资源包发布 */
return [
    'components' => [
        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => true,      // 只用最新资源包
        ],
    ],
];