<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\helpers\Url;
use app\module\admin\asset\AdminAsset;
use yii\web\View;

AdminAsset::register($this);
$currentUser = Yii::$app->user->getIdentity();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <!--    解决400报错：您提交的数据无法被验证-->
    <?= Html::csrfMetaTags() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body style="margin: 0;">
<?php $this->beginBody() ?>
<!--提示消息:通过 Js 添加到此 DIV-->
<div id="alert-message"></div>

<div id="admin-top">
    <div class="logout">
        [<a href="<?= Yii::$app->request->hostInfo ?>">前台</a>|<a href="<?= Url::to(['site/logout']) ?>">退出</a>]
    </div>
    <div class="admin-avatar">
        <img src="<?= $currentUser->avatar_id ? $currentUser->avatar->url : Yii::$app->params['defaultAvatarUrl'] ?>">
    </div>
    <div>
        <ul class="admin-navbar">
            <li class="logo"><a href="<?= Url::to(['site/index']) ?>"><img
                        src="<?= Yii::$app->params['logoUrl'] ?>"></a></li>
            <li class="nav"><a href="<?= Url::to(['user/index']) ?>">用&nbsp;&nbsp;户</a></li>
            <li class="nav"><a href="<?= Url::to(['article/index']) ?>">文&nbsp;&nbsp;章</a></li>
            <li class="nav"><a href="<?= Url::to(['advert/index']) ?>">广&nbsp;告&nbsp;位</a></li>
            <li class="nav"><a href="<?= Url::to(['note/index']) ?>">便&nbsp;&nbsp;签</a></li>
            <li class="nav"><a href="<?= Url::to(['site/flush']) ?>">清理缓存</a></li>
        </ul>
    </div>
</div>
<div class="admin-content">
    <?= $content ?>
</div>
<?php
$type = '';
$message = '';
if ($message = Yii::$app->session->getFlash('admin_success_flash_message')) {
    $type = 'alert-success';
} else {
    $message = Yii::$app->session->getFlash('admin_error_flash_message');
    $type = 'alert-danger';
}
if ($message && $type) {
    $alertId = 'alertMessage'; //此 ID 用于控制删除消息框后执行的动作;参考 http://v3.bootcss.com/javascript/#alerts
    $js = <<<JS
$(document).ready(function(){
    $("#alert-message").prepend('<div role="alert" class="alert $type alert-dismissible fade in message" id="$alertId"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>$message</div>');
    setTimeout('$("#alert-message").hide()',5000);
});
JS;
    $this->registerJs($js, View::POS_END);
}
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
