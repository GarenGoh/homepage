<?php
namespace app\components;

use yii\base\Component;

class DailyService extends Component
{
    public $daily_to = "188226814@qq.com";

    public function generateDaily($open_id, $content, $type)
    {
        $daily_data = \Yii::$app->redis->get($open_id . '_daily_content');
        $daily_data_arr = json_decode($daily_data, true);
        $daily_data_arr[ $type ][] = $content;
        $daily_data = json_encode($daily_data_arr);
        $seconds = strtotime('tomorrow') - time();
        \Yii::$app->redis->setex($open_id . '_daily_content', $seconds, $daily_data);
        $str = "
        from: 
        to:
        content:
        1. 主要工作\n%s\n\n2. 问题\n%s\n\n3. 明天计划\n%s
        ";
        $work = isset($daily_data_arr[1]) ? implode("\n", $daily_data_arr[1]) : '';
        $problem = isset($daily_data_arr[2]) ? implode("\n", $daily_data_arr[2]) : '无';
        $plan = isset($daily_data_arr[3]) ? implode("\n", $daily_data_arr[3]) : '待定';

        return sprintf($str, $work, $problem, $plan);
    }

    public function sendDaily()
    {

    }
}

?>
