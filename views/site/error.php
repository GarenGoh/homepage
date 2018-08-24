<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = '未找到此页面';
$messages = $message . '
原因：
页面不存在或者已经被删除；
您并没有权限执行此操作；
此页面被外星人劫持。';
?>
<div class="col-md-4">
    <h2><?= Html::encode($name) ?></h2>
    <div class="alert alert-danger">
        <p><?= nl2br(Html::encode($messages)) ?></p>
        <p style="text-align: right"><a href="<?= Yii::$app->request->hostInfo ?>">跳转到首页>></a></p>
    </div>
</div>
<div class="col-md-8">
    <img style="max-width: 100%; width: 450px;" src="<?= Yii::$app->params['404Image'] ?>">
</div>
