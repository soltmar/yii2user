<?php

namespace mariusz_soltys\yii2user\assets;

use yii\web\AssetBundle;

class UserAssets extends AssetBundle
{
    public $sourcePath = '@mariusz_soltys/yii2user/views/asset';
    public $css = [
        'css/style.css'
    ];
    public $js = [
        'js/jquery.json.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
