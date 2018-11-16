<?php
namespace app\components;

use app\helpers\AppHelper;
use Yii;
use yii\base\Component;

class WxService extends Component
{
    public $token;

    public function checkSignature()
    {
        $get = Yii::$app->request->getQueryParams();
        $signature = $get['signature'] ?? '';
        $timestamp = $get['timestamp'] ?? '';
        $nonce = $get['nonce'] ?? '';
        $token = $this->token;

        $arr = [$token, $timestamp, $nonce];
        sort($arr, SORT_STRING);
        $str = implode($arr);
        $sha = sha1($str);

        if ($sha == $signature) {
            return true;
        }else {
            AppHelper::log('test', '$sha', $sha);
            AppHelper::log('test', '$signature', $signature);
            return false;
        }
    }
}

?>
