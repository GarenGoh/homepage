<?php
namespace app\components;

use app\helpers\AppHelper;
use app\models\User;
use yii\base\Component;

class DailyService extends Component
{
    public $daily_to = "188226814@qq.com"; //techgroup@alpha-car.cn

    public function generateDaily($open_id, $content, $type)
    {
        /**
         * @var $user User
         */
        $user = \Yii::$app->userService->search()
            ->andWhere(['open_id' => $open_id])
            ->limit(1)
            ->one();
        if(!$user){
            return '你还没有绑定邮箱!';
        }

        $daily_data = \Yii::$app->redis->get($open_id . '_daily_content');
        $daily_data_arr = json_decode($daily_data, true);
        $daily_data_arr[ $type ][] = $content;
        $daily_data = json_encode($daily_data_arr);
        $seconds = strtotime('tomorrow') - time();
        \Yii::$app->redis->setex($open_id . '_daily_content', $seconds, $daily_data);

        $str = "from: %s\nto:%s\n\ncontent:\n1. 主要工作\n%s\n2. 问题\n%s\n3. 明天计划\n%s";
        $work = isset($daily_data_arr[1]) ? $this->getSplicingContent($daily_data_arr[1], "\n") : "\n";
        $problem = isset($daily_data_arr[2]) ? $this->getSplicingContent($daily_data_arr[2], "无\n") : "无\n";
        $plan = isset($daily_data_arr[3]) ? $this->getSplicingContent($daily_data_arr[3], "待定\n") : "待定\n";

        return sprintf($str, $user->email, $this->daily_to, $work, $problem, $plan);
    }

    private function getSplicingContent($contents, $default)
    {
        if(!$contents || !is_array($contents)){
            return $default;
        }
        $splicing_content = '';
        foreach ($contents as $key => $content) {
            $num = $key+1;
            $splicing_content .= "($num) $content\n";
        }

        return $splicing_content;
    }

    public function delDaily($open_id, $num, $type)
    {
        $num = trim($num, ':');
        $num = trim($num, '：');
        if(!$num || !is_numeric($num)){
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
        if(!$user){
            return '你还没有绑定邮箱!';
        }

        $daily_data = \Yii::$app->redis->get($open_id . '_daily_content');
        $daily_data_arr = json_decode($daily_data, true);
        if(isset($daily_data_arr[ $type ], $daily_data_arr[ $type ][$num]) && $msg = $daily_data_arr[ $type ][$num]){
            unset($daily_data_arr[ $type ][$num-1]);
            $daily_data_arr[ $type ] = array_values($daily_data_arr[ $type ]);
            $daily_data = json_encode($daily_data_arr);
            $seconds = strtotime('tomorrow') - time();
            \Yii::$app->redis->setex($open_id . '_daily_content', $seconds, $daily_data);

            $str = "del:%s\n\ncontent:\n1. 主要工作\n%s\n2. 问题\n%s\n3. 明天计划\n%s";
            $work = isset($daily_data_arr[1]) ? $this->getSplicingContent($daily_data_arr[1], "\n") : "\n";
            $problem = isset($daily_data_arr[2]) ? $this->getSplicingContent($daily_data_arr[2], "无\n") : "无\n";
            $plan = isset($daily_data_arr[3]) ? $this->getSplicingContent($daily_data_arr[3], "待定\n") : "待定\n";

            return sprintf($str, $user->email, $this->daily_to, $msg, $work, $problem, $plan);
        }else{
            return "没有找到这条消息!";
        }
    }

    public function sendDaily()
    {

    }
}

?>
