<?php

namespace app\commands;

use app\helpers\AppHelper;
use Yii;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionSet($task_id, $limit)
    {
        $task_conf = Yii::$app->redis->get('domino_task_conf');
        if ($task_conf) {
            $task_conf = json_decode($task_conf, true);
        } else {
            $task_conf = [];
        }
        // 考虑已经配置过的任务
        $count = 0;
        $time = time() + 3 * 24 * 3600;
        if (isset($task_conf[$task_id])) {
            // 已刷次数
            if (isset($task_conf[$task_id]['count'])) {
                $count = (int)$task_conf[$task_id]['count'];
            }
            // 过期时间
            if (isset($task_conf[$task_id]['time'])) {
                $time = (int)$task_conf[$task_id]['time'];
            }
        }

        $task_conf[$task_id] = [
            'limit' => $limit,
            'count' => $count,
            'time' => $time,
        ];

        $task_conf = json_encode($task_conf);
        Yii::$app->redis->setex('domino_task_conf', 10 * 24 * 3600, $task_conf);
    }

    public $_task_log = false;

    public function actionTask()
    {
        if($this->_task_log === false){
            $this->_task_log = date('Y-m-d H:i:s');
        }

        echo "\n------{$this->_task_log}--------\n";
        $task_conf = Yii::$app->redis->get('domino_task_conf');
        if (!$task_conf) {
            echo "当前没有任务\n";
            return 0;
        }
        echo $task_conf, "\n";

        $task_conf = json_decode($task_conf, true);
        if(!$task_conf){
            echo "当前没有任务!\n";
            return 0;
        }

        $task_count = count($task_conf);
        $number = 3 + 2 * $task_count;  // 低于3个任务可能不执行
        $num = rand(0, 9);
        if ($num > $number) {
            echo "忽略\n";
            return 0;
        }

        // 随机取一篇
        $id = array_rand($task_conf);
        echo "选中{$id}\n";
        $conf = $task_conf[$id];
        $remove = 0;
        if (!isset($conf['limit'], $conf['count'])) {
            $remove = 1;
            echo "配置不正确\n";
        }
        if ($conf['limit'] <= $conf['count']) {
            $remove = 1;
            echo "超出上限\n";
        }
        if (isset($conf['time']) && $conf['time'] < time()) {
            $remove = 1;
            echo "任务过期\n";
        }

        // 清理任务,并重新处理下一个任务
        if ($remove) {
            unset($task_conf[$id]);
            $task_conf = json_encode($task_conf);
            Yii::$app->redis->setex('domino_task_conf', 10 * 24 * 3600, $task_conf);

            return $this->actionTask();
        }

        $this->share($id);
        $task_conf[$id]['count'] = $conf['count'] + 1;
        $task_conf = json_encode($task_conf);
        Yii::$app->redis->setex('domino_task_conf', 10 * 24 * 3600, $task_conf);

        // 任务超过3个,有概率继续随机增加某任务的UV
        if($number > 9 && rand(0, 9) < 3){
            $re_rand = rand(3, 7);
            echo "随机增加UV,sleep {$re_rand}s.\n";
            sleep($re_rand);
            $this->actionTask();
        }

        $now = date("Y-m-d H:i:s");
        echo "------{$this->_task_log}------{$now}------\n";

        return 0;
    }


    public function share($id)
    {
        // Header信息
        $user_agent = AppHelper::getFakeUserAgent();
        $ip = AppHelper::getFakeIp();

        // 获取任务标题
        $url = "http://v10.alpha-car.cn/tasks/{$id}?expand=article";
        $task = Yii::$app->httpClient->createRequest()
            ->setOptions(['timeout' => 5])
            ->setUrl($url)
            ->setHeaders([
                'Content-type' => 'application/json',
                'User-Agent' => $user_agent,
                'User-CLIENT-IP' => $ip,
                'X-Real-IP' => $ip,
                'X-FORWARDED-FOR' => $ip,
            ])
            ->setMethod('GET')
            ->send()
            ->getContent();
        $title = '奔腾积分任务先到先得';
        if ($task) {
            $task = json_decode($task, true);
            if (isset($task['data'], $task['data']['article'], $task['data']['article']['title'])) {
                $title = $task['data']['article']['title'];
            }
        }

        // 创建事件
        $url = "http://v10.alpha-car.cn/events";
        $post = [
            'action' => 'view',
            'data_type' => 15,
            'data_id' => $id,
            'label' => $title,
            'url' => "http://static.alpha-car.cn/v10/H5/index.html#/article/{$id}?oem_id=1&article_link=1",
        ];

        Yii::$app->httpClient->setTransport('yii\httpclient\CurlTransport');
        $request = Yii::$app->httpClient->createRequest()
            ->setOptions(['timeout' => 5])
            ->setContent(json_encode($post, JSON_UNESCAPED_UNICODE))
            ->setUrl($url)
            ->setHeaders([
                'Content-type' => 'application/json',
                'User-Agent' => $user_agent,
                'User-CLIENT-IP' => $ip,
                'X-Real-IP' => $ip,
                'X-FORWARDED-FOR' => $ip,
            ])
            ->setMethod('POST');

        Yii::$app->httpClient->send($request)->getContent();

        $re_rand = rand(6,15);
        if($re_rand <= 7){
            echo "增加该任务PV并随机某任务增加UV,sleep {$re_rand}s.\n";
            sleep($re_rand);
            Yii::$app->httpClient->send($request)->getContent();
            $this->actionTask();
        }
    }
}
