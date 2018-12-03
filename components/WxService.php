<?php
namespace app\components;

use app\helpers\AppHelper;
use app\helpers\ArrayHelper;
use Yii;
use yii\base\Component;

class WxService extends Component
{
    public $token;
    public $user_name;

    /**
     * @param $title
     * @return string
     */
    public function getTitleType($title)
    {
        $all_titles = [
            'email' => ['我的邮箱', '邮箱', '注册邮箱', 'email'],
            'problem' => ['problem', 'pr', '问题'],
            'work' => ['work', 'w', '主要工作', '今日工作'],
            'plan' => ['plan', 'pl', '明日计划', '计划']
        ];

        foreach ($all_titles as $type => $titles){
            if(in_array($title, $titles)){
                return $type;
            }
        }

        return '';
    }

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

        $msg_arr = ArrayHelper::convertXmlToArray($msg);

        return $msg_arr;
    }

    public function processData($message)
    {
        $content = ArrayHelper::getValue($message, 'Content');
        //$official_user_name = ArrayHelper::getValue($message, 'ToUserName');
        $user_open_id = ArrayHelper::getValue($message, 'FromUserName');
        $msg_type = ArrayHelper::getValue($message, 'MsgType');

        $content = "我的邮箱:1382342@qq.com";

        //账号处理
        $len = mb_strpos($content, ':');
        if(!$len){
            $len = mb_strpos($content, '：');
        }

        if($len && $len<= 6){
            $title = mb_substr($content, 0, $len);
            $info = mb_substr($content, $len+1);

            $type = $this->getTitleType($title);
            if($type){
                switch ($type){
                    case 'email':
                        $message = Yii::$app->userService->wxRegister($user_open_id, $info);
                        break;
                    case 'work':
                        $message = Yii::$app->dailyService->generateDaily($user_open_id, $info,  1);
                        break;
                    case 'problem':
                        $message = Yii::$app->dailyService->generateDaily($user_open_id, $info, 2);
                        break;
                    case 'plan':
                        $message = Yii::$app->dailyService->generateDaily($user_open_id, $info, 3);
                        break;
                    default:
                        $message = '没能识别您想做什么!';
                }

            }else{
                $message = '种一棵树最好的时间是十年前,其次是现在。';
            }

            $this->sendMessage($user_open_id, $message, $msg_type);
        }else{
            $this->sendMessage($user_open_id, '大家好!', $msg_type);
        }
    }

    public function sendMessage($open_id, $content, $type = 'text')
    {
        $time = time();

        $replay = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
        $result = sprintf($replay, $open_id, $this->user_name, $time, $type, $content);
        echo $result;
        AppHelper::log('test', '$result', $result);
        exit;
    }
}

?>
