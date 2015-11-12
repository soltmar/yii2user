<?php

namespace mariusz_soltys\yii2user\assets;

use yii\web\AssetBundle;

class ProfileFieldAssets extends AssetBundle
{
    public $sourcePath = '@mariusz_soltys/yii2user/views/asset';
    public $css = [
      //  'css/redmond/jquery-ui.theme.min.css'
    ];

    public $depends = [
        'mariusz_soltys\yii2user\assets\UserAssets',
        'yii\jui\JuiAsset',
    ];
}
