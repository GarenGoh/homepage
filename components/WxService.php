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
            'work' => ['work', 'w', '主要工作', '今日工作'],
            'problem' => ['problem', 'pr', '问题'],
            'plan' => ['plan', 'p', '明日计划', '计划'],
            'email' => ['email', '我的邮箱', '邮箱', '注册邮箱'],
            'email_password' => ['email_password', 'ep', '邮箱密码'],
            'password' => ['password', 'pw', '密码', '我的密码'],
            'name' => ['name', '名字', '我的名字']
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
                    case 'work':
                        $message = Yii::$app->dailyService->generateDaily($user_open_id, $info,  1);
                        break;
                    case 'problem':
                        $message = Yii::$app->dailyService->generateDaily($user_open_id, $info, 2);
                        break;
                    case 'plan':
                        $message = Yii::$app->dailyService->generateDaily($user_open_id, $info, 3);
                        break;
                    case 'email':
                        $message = Yii::$app->userService->wxRegister($user_open_id, $info);
                        break;
                    case 'email_password':
                        $message = Yii::$app->userService->setEmailPassword($user_open_id, $info);
                        break;
                    case 'password':
                        $message = Yii::$app->userService->validatePassword($user_open_id, $info);
                        break;
                    case 'name':
                        $message = Yii::$app->userService->updateUser($user_open_id, ['name' => $info]);
                        break;
                    default:
                        $message = '没能识别您想做什么!';
                }

            }else{
                $message = '种一棵树最好的时间是十年前,其次是现在。';
            }

            $this->sendMessage($user_open_id, $message, $msg_type);
        }else{
            if(strpos($content, '删除主要工作') !== false){
                $num = mb_substr($content, 6);
                $message = Yii::$app->dailyService->delDaily($user_open_id, $num, 1);
            }elseif(strpos($content, '删除问题') !== false){
                $num = mb_substr($content, 4);
                $message = Yii::$app->dailyService->delDaily($user_open_id, $num, 2);
            }elseif(strpos($content, '删除明日计划') !== false){
                $num = mb_substr($content, 6);
                $message = Yii::$app->dailyService->delDaily($user_open_id, $num, 3);
            }else{
                $message = '大家好!';
            }
            $this->sendMessage($user_open_id, $message, $msg_type);
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
