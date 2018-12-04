<?php
namespace app\components;

use app\helpers\AppHelper;
use app\models\DailyInfo;
use app\models\User;
use yii\base\Component;
use yii\base\Exception;
use yii\swiftmailer\Mailer;
use Yii;

class DailyService extends Component
{
    public $daily_to = ["garen.goh@qq.com" => "Garen"];

    /**
     * @param array $where
     * @return \yii\db\ActiveQuery
     */
    public function search($where = [])
    {
        $fields = ['id', 'user_id', 'send_type'];
        $query = DailyInfo::find();
        foreach ($fields as $f) {
            if (isset($where[ $f ])) {
                $query->andFilterWhere([$f => $where[ $f ]]);
            }
        }

        return $query;
    }

    public function getDailyKey($open_id)
    {
        return $open_id . "_daily_content";
    }

    public function generateDaily($open_id, $content, $type)
    {

        /**
         * @var $user User
         * @var $daily_info DailyInfo
         */
        $user = \Yii::$app->userService->search()
            ->andWhere(['open_id' => $open_id])
            ->limit(1)
            ->one();
        if (!$user) {
            return '没有您的信息,可能是您还没有绑定邮箱!【邮箱:12312@qq.com】';
        }
        if (!$user->name) {
            return '你还没有设置名字!【名字:王三】';
        }

        $daily_info = Yii::$app->dailyService->search()
            ->andWhere(['user_id' => $user->id])
            ->limit(1)
            ->one();
        if(!$daily_info->email_password){
            return '你还没有设置邮箱密码!【邮箱密码:123456】';
        }

        $key = $this->getDailyKey($open_id);
        $daily_data = \Yii::$app->redis->get($key);
        $daily_data_arr = json_decode($daily_data, true);
        $daily_data_arr[ $type ][] = $content;
        $daily_data = json_encode($daily_data_arr);
        $seconds = strtotime('tomorrow') - time();
        \Yii::$app->redis->setex($key, $seconds, $daily_data);

        $str = "from: %s\nto:%s\n\ncontent:\n1. 主要工作\n%s\n2. 问题\n%s\n3. 明天计划\n%s";
        $work = isset($daily_data_arr['work']) ? $this->getSplicingContent($daily_data_arr['work'], "\n") : "\n";
        $problem = isset($daily_data_arr['problem']) ? $this->getSplicingContent($daily_data_arr['problem'], "无\n") : "无\n";
        $plan = isset($daily_data_arr['plan']) ? $this->getSplicingContent($daily_data_arr['plan'], "待定\n") : "待定\n";

        //标记用户,今天要发日报
        DailyInfo::updateAll(['send_type' => 1], ['user_id' => $user->id]);

        $to_emails = array_keys($this->daily_to);
        $to = array_shift($to_emails);

        return sprintf($str, $user->email, $to, $work, $problem, $plan);
    }

    private function getSplicingContent($contents, $default)
    {
        if (!$contents || !is_array($contents)) {
            return $default;
        }
        $splicing_content = '';
        foreach ($contents as $key => $content) {
            $num = $key + 1;
            $splicing_content .= "($num) $content\n";
        }

        return $splicing_content;
    }

    public function delDaily($open_id, $num, $type)
    {
        $num = trim($num, ':');
        $num = trim($num, '：');
        if (!$num || !is_numeric($num)) {
            AppHelper::log('daily', 'del_msg_num', $num);

            return '编号不正确!';
        }
        /**
         * @var $user User
         */
        $user = \Yii::$app->userService->search()
            ->andWhere(['open_id' => $open_id])
            ->limit(1)
            ->one();
        if (!$user) {
            return '你还没有绑定邮箱!';
        }

        $key = $this->getDailyKey($open_id);
        $daily_data = \Yii::$app->redis->get($key);
        $daily_data_arr = json_decode($daily_data, true);
        AppHelper::log('daily', 'daily_data', $daily_data);

        $num = $num - 1;
        if (isset($daily_data_arr[ $type ], $daily_data_arr[ $type ][ $num ]) && $msg = $daily_data_arr[ $type ][ $num ]) {
            unset($daily_data_arr[ $type ][ $num ]);
            $daily_data_arr[ $type ] = array_values($daily_data_arr[$type]);
            $daily_data = json_encode($daily_data_arr);

            $seconds = strtotime('tomorrow') - time();
            \Yii::$app->redis->setex($key, $seconds, $daily_data);

            $str = "del:%s\n\ncontent:\n1. 主要工作\n%s\n2. 问题\n%s\n3. 明天计划\n%s";
            $work = isset($daily_data_arr['work']) ? $this->getSplicingContent($daily_data_arr['work'], "\n") : "\n";
            $problem = isset($daily_data_arr['problem']) ? $this->getSplicingContent($daily_data_arr['problem'], "无\n") : "无\n";
            $plan = isset($daily_data_arr['plan']) ? $this->getSplicingContent($daily_data_arr['plan'], "待定\n") : "待定\n";

            return sprintf($str, $msg, $work, $problem, $plan);
        } else {
            return "没有找到这条消息!";
        }
    }

    public function getHtmlContent($open_id)
    {
        $key = $this->getDailyKey($open_id);
        AppHelper::log('daily', 'key', $key);
        $content = \Yii::$app->redis->get($key);
        AppHelper::log('daily', 'content', $content);
        $content = json_decode($content, true);

        if ($content) {
            //有"主要工作"才做处理
            if (isset($content['work']) && $content['work'] && is_array($content['work'])) {
                //主要工作
                $str = "1. 主要工作:<br>";
                foreach ($content['work'] as $w_key => $item) {
                    $i = $w_key + 1;
                    $str .= "($i) $item<br>";
                }
                $str .= "<br>";

                //问题
                $str .= "2. 问题:<br>";
                if (isset($content['problem']) && $content['problem'] && is_array($content['problem'])) {
                    foreach ($content['problem'] as $pr_key => $item) {
                        $i = $pr_key + 1;
                        $str .= "($i) $item<br>";
                    }
                } else {
                    $str .= "无<br>";
                }
                $str .= "<br>";

                //明日计划
                $str .= "3. 明日计划:<br>";
                if (isset($content['plan']) && $content['plan'] && is_array($content['plan'])) {
                    foreach ($content['plan'] as $p_key => $item) {
                        $i = $p_key + 1;
                        $str .= "($i) $item<br>";
                    }
                } else {
                    $str .= "待定<br>";
                }

                return $str;
            }
        }

        return false;
    }

    /**
     * @param $open_id
     * @return string
     */
    public function send($open_id)
    {
        /**
         * @var $user User
         * @var $daily_info DailyInfo
         */
        $user = \Yii::$app->userService->search()
            ->andWhere(['open_id' => $open_id])
            ->limit(1)
            ->one();
        if(!$user){
            return "没有找到你的信息!";
        }
        if(!$user->name){
            return "你没有设置名字!【名字:王三】";
        }

        $html = $this->getHtmlContent($open_id);
        if(!$html){
            return "您没有设置主要工作!";
        }

        $daily_info = \Yii::$app->dailyService->search()
            ->andWhere(['user_id' => $user->id])
            ->limit(1)
            ->one();
        if(!$daily_info->email_password){
            return "您没有设置邮箱密码!";
        }
        $date = date("Ymd");
        $subject = "【开发日报】{$date}_" . $user->name;
        /**
         * @var $mailer Mailer
         */
        $mailer = Yii::$app->mailer;
        $mailer->setTransport([
            'class' => 'Swift_SmtpTransport',
            'host' => 'smtp.exmail.qq.com',  //每种邮箱的host配置不一样
            'port' => '465',
            'encryption' => 'ssl',
            'username' => $user->email,
            'password' => $daily_info->email_password
        ]);
        $mail = $mailer->compose();
        $mail->setCharset('UTF-8'); //设置编码
        $mail->setFrom([$user->email => $user->name]);    //发送者邮箱
        $mail->setTo(Yii::$app->dailyService->daily_to);    //接收人邮箱
        $mail->setSubject($subject);    //邮件标题
        $mail->setHtmlBody($html);    //发送内容(可写HTML代码)
        try{
            if ($mail->send()) {
                $daily_info->send_type = 0;
                $daily_info->save();

                return "您的日报发送成功!";
            } else {
                return "您的日报发送失败!";
            }
        }catch (Exception $e){
            AppHelper::log('daily', 'send_email_fail', $e->getMessage());
            return "您的日报发送失败!";
        }
    }

    /**
     * @param $open_id
     * @return string
     */
    public function view($open_id)
    {
        /**
         * @var $user User
         * @var $daily_info DailyInfo
         */
        $user = \Yii::$app->userService->search()
            ->andWhere(['open_id' => $open_id])
            ->limit(1)
            ->one();
        if(!$user){
            return "没有找到你的信息!";
        }

        $html = $this->getHtmlContent($open_id);

        return str_replace('<br>', "\n", $html);
    }
}

?>
