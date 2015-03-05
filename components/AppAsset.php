<?php
namespace infinite\docHelper\components;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@infinite/docHelper/assets';
    public $css = [
        'infinite.docHelper.css',
    ];
    public $js = [
        'infinite.docHelper.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
