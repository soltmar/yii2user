<?php

namespace marsoltys\yii2user\assets;

use yii\web\AssetBundle;

class ProfileFieldAssets extends AssetBundle
{
    public $sourcePath = '@marsoltys/yii2user/views/asset';
    public $css = [
      //  'css/redmond/jquery-ui.theme.min.css'
    ];

    public $depends = [
        'marsoltys\yii2user\assets\UserAssets',
        'yii\jui\JuiAsset',
    ];
}
