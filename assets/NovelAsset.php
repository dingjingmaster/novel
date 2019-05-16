<?php
namespace app\assets;

use yii\web\AssetBundle;

class NovelAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'web/css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
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
