<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use app\helpers\Url;
use app\models\Article;

AppAsset::register($this);
$currentUser = Yii::$app->user->getIdentity();
$navArticle = Article::getCategoryMap();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if($this->params['pageId'] == 'app-home') {?>
    <meta name="baidu-site-verification" content="XUHFhSQr2o" />
    <?php }?>
    <?= $this->metaTags[0] ?? '<meta name="keywords" content="garen,wqiang,博客,Garen.Goh,PHP, 编程,开发" />' ?>
    <?= $this->metaTags[1] ?? '<meta name="description" content="该网站(wqiang)为我(garengoh)的个人博客,主要记录一些有用的技术文章,以及我的学习笔记.">' ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php if (YII_ENV_PROD) { ?>
    <!--Google Analytics-->
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-82902920-1', 'auto');
        ga('send', 'pageview');

    </script>
<?php } ?>

<!--返回顶部-->
<div class="actGotop"><a href="javascript:;" title="返回顶部"></a></div>

<!--提示消息:通过 Js 添加到此 DIV-->
<div id="alert-message"></div>

<div id="app-top">
    <div class="top">
        <div class="logo">
            <img src="<?= Yii::$app->params['logoUrl'] ?>">
        </div>
    </div>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- 手机端会把导航装进集装箱 -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand gNote" href="<?= Url::to(['site/index']) ?>" msrc="/music/note-c/1.mp3">Garen's
                    blog</a>
            </div>
            <!-- 导航条内容 -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php
                    $nav = Yii::$app->request->pathInfo;
                    ?>
                    <li class="<?= $nav == 'article/php' ? 'active' : '' ?>"><a href="<?= Url::to(['article/php']) ?>"
                                                                                class="gNote"
                                                                                msrc="/music/note-c/2.mp3">PHP</a></li>
                    <li class="<?= $nav == 'article/db' ? 'active' : '' ?>"><a href="<?= Url::to(['article/db']) ?>"
                                                                               class="gNote" msrc="/music/note-c/3.mp3">数据库</a>
                    </li>
                    <li class="<?= $nav == 'article/linux' ? 'active' : '' ?>"><a
                            href="<?= Url::to(['article/linux']) ?>" class="gNote" msrc="/music/note-c/4.mp3">Linux</a>
                    </li>
                    <li class="<?= ($nav == 'article/frontend' || $nav == 'article/learn') ? 'active' : '' ?>">
                        <a href="#" class="dropdown-toggle gNote" data-toggle="dropdown" role="button"
                           aria-haspopup="true" aria-expanded="false" msrc="/music/note-c/5.mp3">其他博文 <span
                                class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= Url::to(['article/frontend']) ?>" class="gNote" msrc="/music/note-c/1.mp3">前端</a>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?= Url::to(['article/learn']) ?>" class="gNote" msrc="/music/note-c/1.mp3">学无止境</a>
                            </li>
                        </ul>
                    </li>
                    <li class="<?= $nav == 'note/index' ? 'active' : '' ?>"><a href="<?= Url::to(['note/index']) ?>"
                                                                               class="gNote" msrc="/music/note-c/6.mp3">便签</a>
                    </li>
                    <li class="<?= $nav == 'site/about' ? 'active' : '' ?>"><a href="<?= Url::to(['site/about']) ?>"
                                                                               class="gNote" msrc="/music/note-c/7.mp3">关于</a>
                    </li>
                </ul>
                <form class="navbar-form navbar-left pc-right" role="search" action="<?= Url::to(['search/index']) ?>"
                      method="get">
                    <div class="form-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Search">
                    </div>
                    <button type="submit" class="btn btn-default">搜索</button>
                </form>
                <?php if (isset($this->params['pageId']) && $this->params['pageId'] != 'app-home') { ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <?php if (Yii::$app->user->isGuest) { ?>
                                    <li><a href="<?= Url::to(['site/login']) ?>">登录</a></li>
                                <?php } else { ?>
                                    <?php if (Yii::$app->userService->isRoot(Yii::$app->user->getId())) { ?>
                                        <li><a href="<?= Url::to(['admin/site/index']) ?>">后台</a></li>
                                        <li role="separator" class="divider"></li>
                                    <?php } ?>
                                    <li><a href="<?= Url::to(['site/logout']) ?>">退出</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </nav>
</div>
<div class="content-wrapper container" id="<?= isset($this->params['pageId']) ? $this->params['pageId'] : '' ?>">
    <div style="text-align: center;">

    </div>
    <?= $content ?>
</div>
<footer class="content-wrapper container" id="app-footer">
    <div class="col-md-4 ">
        <a href="http://www.miitbeian.gov.cn/">京ICP备16014638号-1</a>
    </div>
    <div class="col-md-4 ">
        <a href="http://wqiang.net/">Garen.Goh个人博客</a> 内容版权所有，同时保留所有权利。
    </div>
    <div class="col-md-4 friendly-link">
        <p> 友情链接：
            <small><a href="https://www.sdk.cn/" target="_blank">SDK.cn</a><a href="http://xiajie.me/" target="_blank">Jerry's
                    Blog</a></small>
        </p>
    </div>
</footer>
<?php
$note = "$('.gNote').mouseenter(function () {
      var src= $(this).attr('msrc');
      var audio = new Audio();
      audio.controls = false;
      audio.src = src;
      document.body.appendChild(audio);
      audio.play();
      setTimeout(function() {
        audio.remove();
      }, 2500);
    });";
$this->registerJs($note, \yii\web\View::POS_END);

$type = '';
$message = '';
if ($message = Yii::$app->session->getFlash('app_success_flash_message')) {
    $type = 'alert-success';
} else {
    $message = Yii::$app->session->getFlash('app_error_flash_message');
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

    $this->registerJs($js, \yii\web\View::POS_END);
}
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
