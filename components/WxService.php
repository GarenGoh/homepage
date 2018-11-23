<?php
namespace app\components;

use app\helpers\AppHelper;
use app\helpers\ArrayHelper;
use app\helpers\SnowFlake;
use Yii;
use yii\base\Component;
use yii\web\User;

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

    public function getMessage()
    {
        $msg = \Yii::$app->request->getRawBody();
        if(isset($message['FromUserName'])){
            $user =  Yii::$app->userService->search()
                ->andWhere(['open_id' => $message['FromUserName']])
                ->limit(1)
                ->one();
            if($user){
                Yii::$app->user = $user;
            }else{

            }
        }

        $msg_arr = ArrayHelper::convertXmlToArray($msg);

        return $msg_arr;
    }

    public function sendMessage($message, $content)
    {
        $toUserName = ArrayHelper::getValue($message, 'ToUserName');
        $fromUserName = ArrayHelper::getValue($message, 'FromUserName');
        $type = ArrayHelper::getValue($message, 'MsgType');
        $time = time();

        $replay = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
        $result = sprintf($replay, $fromUserName, $toUserName, $time, $type, $content);
        echo $result;
        AppHelper::log('test', '$result', $result);
        exit;
    }
}

?>
