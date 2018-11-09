<?php
namespace app\controllers;

use app\helpers\AppHelper;
use Yii;

class WxController extends BaseController
{
    public function actionEvent()
    {
        $get = Yii::$app->request->getQueryParams();
        $post = Yii::$app->request->getBodyParams();

        $signature = $get['signature'] ?? '';
        $echostr = $get['echostr'] ?? '';
        $timestamp = $get['timestamp'] ?? '';
        $nonce = $get['nonce'] ?? '';
        $token = '9292' ?? '';

        AppHelper::log('test', '$get', $get);
        AppHelper::log('test', '$post', $post);

        return $echostr;
    }
}

?>
