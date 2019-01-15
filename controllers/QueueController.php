<?php
namespace app\controllers;

use app\jobs\TestJob;
use app\models\Advert;
use app\models\Article;
use app\models\Note;

class QueueController extends BaseController
{
    public function actionRabbit()
    {
        $job = new TestJob();
        $job->id = 1;
        $job->model = Article::className();
        $id = \Yii::$app->queue/*->delay(30)*/->push($job);

        echo '<pre>';
        print_r($id);
        echo '</pre>';
        exit;
    }

    public function actionRedis()
    {
        $job = new TestJob();
        $job->id = 1;
        $job->model = Advert::className();
        $id = \Yii::$app->queue1->delay(8)->push($job);

        echo '<pre>';
        print_r($id);
        echo '</pre>';
        exit;
    }

    public function actionFile()
    {
        $job = new TestJob();
        $job->id = 1;
        $job->model = Note::className();
        $id = \Yii::$app->queue2->delay(8)->push($job);

        echo '<pre>';
        print_r($id);
        echo '</pre>';
        exit;

    }
}

?>