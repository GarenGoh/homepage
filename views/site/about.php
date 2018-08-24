<?php

/* @var $this yii\web\View */

$this->title = '关于我!';
$this->params['pageId'] = 'about-page';
?>
<div class="col-md-2">
</div>
<div class="col-md-8">
    <div class="col-md-12 item" style="font-family:STXingkai, STXinwei, FangSong, 'Microsoft YaHei'">
        <div class="col-md-12 basic">
            <h4>基本资料：</h4>
            <div class="col-md-6 my-img">
                <img src="<?= Yii::$app->params['me_1'] ?>">
            </div>
            <p class="col-md-6 info">
                <i class="fa fa-user"></i> 姓名：吴强<br>
                <i class="fa fa-street-view"></i> 昵称： Garen<br>
                <i class="fa fa-qq"></i> Q Q：188226814<br>
                <i class="fa fa-envelope-o"></i> 邮箱：garen.goh@qq.com<br>
                <i class="fa fa-user-secret"></i> 职业：PHP工程师<br>
                <i class="fa fa-home"></i> 主页：wqiang.net<br>
                <i class="fa fa-map-marker"></i> 现居：北京.海淀<br>
                <i class="fa fa-pencil"></i> 格言：努力让自己吊到爆
            </p>
        </div>
        <div class="brief">
            <h4>个人简介：</h4>
            <p class="content">
                本人毕业于西南石油大学高分子专业。15年5月辞职转行做码农，6月初开始了我的北漂生活，在好友的帮助下进入一家创业公司正式开始学习编程，9月开始向公司网站贡献代码。迄今（16年5月）能较熟练的使用Yii2框架，git，MySQL；前端熟悉Html、Css、Less和简单的JavaScript、jQuery、gulp；对服务器搭建也略知一二。
            </p>
            <h4>网站简介：</h4>
            <p class="content">
                此网站使用了阿里云虚拟主机；数据库为阿里云提供的MySQL数据库；后端采用功能强大的Yii2框架；前端主要使用了主流的Bootstrap框架；网站设计借鉴了多个博客样式。做此网站的目的是想系统的学习一下前后端及服务器，新手作品，请多多指教。
            </p>
        </div>
    </div>
</div>
