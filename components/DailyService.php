<?php
namespace app\components;

use app\helpers\AppHelper;
use app\models\DailyInfo;
use app\models\User;
use yii\base\Component;

class DailyService extends Component
{
    public $daily_to = "188226814@qq.com"; //techgroup@alpha-car.cn

    public function search($where = [])
    {
        $fields = ['id', 'user_id'];
        $query = DailyInfo::find();
        foreach ($fields as $f) {
            if (isset($where[$f])) {
                $query->andFilterWhere([$f => $where[$f]]);
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
         */
        $user = \Yii::$app->userService->search()
            ->andWhere(['open_id' => $open_id])
            ->limit(1)
            ->one();
        if(!$user){
            return '你还没有绑定邮箱!';
        }

        $key = $this->getDailyKey($open_id);
        $daily_data = \Yii::$app->redis->get($key);
        $daily_data_arr = json_decode($daily_data, true);
        $daily_data_arr[ $type ][] = $content;
        $daily_data = json_encode($daily_data_arr);
        $seconds = strtotime('tomorrow') - time();
        \Yii::$app->redis->setex($key, $seconds, $daily_data);

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

        $key = $this->getDailyKey($open_id);
        $daily_data = \Yii::$app->redis->get($key);
        $daily_data_arr = json_decode($daily_data, true);
        if(isset($daily_data_arr[ $type ], $daily_data_arr[ $type ][$num]) && $msg = $daily_data_arr[ $type ][$num]){
            unset($daily_data_arr[ $type ][$num-1]);
            $daily_data_arr[ $type ] = array_values($daily_data_arr[ $type ]);
            $daily_data = json_encode($daily_data_arr);
            $seconds = strtotime('tomorrow') - time();
            \Yii::$app->redis->setex($open_id . $key, $seconds, $daily_data);

            $str = "del:%s\n\ncontent:\n1. 主要工作\n%s\n2. 问题\n%s\n3. 明天计划\n%s";
            $work = isset($daily_data_arr[1]) ? $this->getSplicingContent($daily_data_arr[1], "\n") : "\n";
            $problem = isset($daily_data_arr[2]) ? $this->getSplicingContent($daily_data_arr[2], "无\n") : "无\n";
            $plan = isset($daily_data_arr[3]) ? $this->getSplicingContent($daily_data_arr[3], "待定\n") : "待定\n";

            return sprintf($str, $user->email, $this->daily_to, $msg, $work, $problem, $plan);
        }else{
            return "没有找到这条消息!";
        }
    }

    public function getHtmlContent($open_id)
    {
        $key = $this->getDailyKey($open_id);
        $content = \Yii::$app->redis->get($key);

        if($content){
            //有"主要工作"才做处理
            if(isset($content[1]) && $content[1] && is_array($content[1])){
                //主要工作
                $str = "1. 主要工作:<br>";
                foreach ($content[1] as $w_key => $item){
                    $i = $w_key + 1;
                    $str .= "($i) $item<br>";
                }
                $str .= "<br>";

                //问题
                $str .= "2. 问题:<br>";
                if(isset($content[2]) && $content[2] && is_array($content[2])){
                    foreach ($content[2] as $pr_key => $item){
                        $i = $pr_key + 1;
                        $str .= "($i) $item<br>";
                    }
                }else{
                    $str .= "无<br>";
                }
                $str .= "<br>";

                //明日计划
                $str .= "3. 明日计划:<br>";
                if(isset($content[3]) && $content[3] && is_array($content[3])){
                    foreach ($content[3] as $p_key => $item){
                        $i = $p_key + 1;
                        $str .= "($i) $item<br>";
                    }
                }else{
                    $str .= "待定<br>";
                }

                return $str;
            }
        }

        return false;
    }


}

?>
