<?php

namespace marsoltys\yii2user\assets;

use yii\web\AssetBundle;

class UserAssets extends AssetBundle
{
    public $sourcePath = '@marsoltys/yii2user/views/asset';
    public $css = [
        'css/style.css'
    ];
    public $js = [
        'js/jquery.json.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
