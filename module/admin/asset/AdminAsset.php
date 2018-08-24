<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\module\admin\asset;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@frontend/dist';

    public $css = [
        'styles/app.css',
    ];

    public $js = [
        'scripts/app.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',//载入yii.js jquery.js
        //'yii\bootstrap\BootstrapAsset',//载入Bootstrap css
    ];
}
