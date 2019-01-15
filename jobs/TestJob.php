<?php
namespace app\jobs;

use app\helpers\AppHelper;
use app\models\BaseActiveRecord;
use yii\queue\Job;

class TestJob implements Job
{
    public $id;
    public $model;
    public function execute($queue)
    {
        /**
         * @var $model BaseActiveRecord
         */
        $model = $this->model;
        AppHelper::log('test1', $model::className(), $this->id);
        $data = $model::findOne(['id' => $this->id]);

        AppHelper::log('test1', 'title', $data->title);
    }
}
?>