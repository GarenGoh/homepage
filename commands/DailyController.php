<?php
namespace app\commands;

use app\models\User;
use yii\console\Controller;
use Yii;
use yii\swiftmailer\Mailer;

class DailyController extends Controller
{
    public function actionSend()
    {
        /**
         * @var $users User[]
         */
        $users = \Yii::$app->userService->search()
            ->andWhere(['daily_type' => 1])
            ->all();
        foreach ($users as $user) {
            $html = \Yii::$app->dailyService->getHtmlContent($user->open_id);
            if(!$html){
                echo "用户{$user->name}没有设置主要工作!";
                continue;
            }

            $daily_info = Yii::$app->dailyService->search(['user_id' => $user->id])
                ->limit(1)
                ->one();
            if(!$daily_info){
                echo "用户{$user->name}没有设置邮箱密码!";

                continue;
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
                'password' => '46663931Wq'
            ]);
            $mail = $mailer->compose();
            $mail->setCharset('UTF-8'); //设置编码
            $mail->setFrom([$user->email => $user->name]);    //发送者邮箱
            $mail->setTo(Yii::$app->dailyService->daily_to);    //接收人邮箱
            $mail->setSubject($subject);    //邮件标题
            $mail->setHtmlBody($html);    //发送内容(可写HTML代码)
            //var_dump($mail->toString());exit;
            if ($mail->send()) {
                echo $user->name . "的日报发送成功!\n";
            } else {
                echo $user->name . "的日报发送失败\n";
            }
        }

    }
}
