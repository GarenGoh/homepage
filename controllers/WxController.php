<?php
namespace app\controllers;

use app\helpers\AppHelper;
use Yii;

class WxController extends BaseController
{
    public function actionEvent()
    {
        $post = Yii::$app->request->getBodyParams();
        $get = Yii::$app->request->getQueryParams();

        AppHelper::log('test', 'post', $post);
        AppHelper::log('test', 'get', $get);
        return 9292;
    }
}

?>
