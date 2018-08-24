<?php

namespace app\module\admin;

use Yii;
use \yii\base\Module;

class Admin extends Module
{
    public $controllerNamespace = 'app\module\admin\controllers';
    public $layout = 'main';
    public $defaultRoute = 'site';

    public function init()
    {
        parent::init();

        //Yii::$app->user->loginUrl = '/'.ADMIN_NAME.'/site/login';
        //$app = Yii::$app;
    }
}
