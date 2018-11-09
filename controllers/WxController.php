<?php
namespace app\controllers;

use app\helpers\AppHelper;
use Yii;

class WxController extends BaseController
{
    public function actionEvent()
    {
        $get = Yii::$app->request->getQueryParams();

        $signature = $get['signature'];
        $echostr = $get['echostr'];
        $timestamp = $get['timestamp'];
        $nonce = $get['nonce'];
        $token = '9292';

        AppHelper::log('test', '$echostr', $echostr);

        return $echostr;
    }
}

?>
