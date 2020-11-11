<?php

namespace app\commands;

use app\helpers\AppHelper;
use Yii;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionTask()
    {
        $num = rand(0, 9);
        if ($num > 3) {
            echo "忽略\n";
            return 0;
        }

        $id = Yii::$app->redis->get('domino_task_id');
        if (!$id) {
            echo "没有找到ID\n";
            return 0;
        }

        $limit = Yii::$app->redis->get('domino_task_limit_' . $id);
        if (!$limit) {
            $limit = 800 + rand(1, 299);
        }
        $count = (int)Yii::$app->redis->get('domino_event_count_' . $id);

        if ($count > $limit) {
            echo "达到上限!";
            return 0;
        }

        $this->share($id);
        Yii::$app->redis->setex('domino_event_count' . $id, 60 * 24 * 3600, $count + 1);
        echo $count + 1, "\n";

        return 0;
    }

    public function actionSet($task_id, $limit)
    {
        Yii::$app->redis->setex('domino_task_id', 20 * 24 * 3600, $task_id);
        Yii::$app->redis->setex('domino_task_limit_' . $task_id, 21 * 24 * 3600, $limit);
    }

    public function share($id)
    {
        $user_agent = AppHelper::getFakeUserAgent();
        $ip = AppHelper::getFakeIp();

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
    }
}
