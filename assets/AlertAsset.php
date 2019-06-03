<?php

namespace app\assets;

use yii\web\AssetBundle;

class AlertAsset extends AssetBundle
{
    public $sourcePath = '@npm';

    public $css = [
    ];

    public $js = [
        'bootstrap-notify/bootstrap-notify.js',
    ];

    public $depends = [
    ];
}
