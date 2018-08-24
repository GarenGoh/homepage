<?php
/* @var $this yii\web\View */
use app\helpers\Tools;
use yii\web\View;
use app\helpers\Url;

$this->title = 'Garen 的主页';
$this->params['pageId'] = 'app-home';
$articles = Yii::$app->articleService->search()
    ->orderBy(['id' => SORT_DESC])
    ->limit(10)
    ->all();
$adverts = Yii::$app->advertService->search()
    ->orderBy(['weight' => SORT_DESC, 'id' => SORT_DESC])
    ->limit(4)
    ->all();
$ad_0 = isset($adverts[0]) ? $adverts[0] : [];
unset($adverts[0]);
?>
<!--<embed src='/music/tkzc.mp3' width="0" height="0" loop="-1" autostart="true">-->
<div class="col-md-9 primary-block" style='background: repeat-y right url("<?= Yii::$app->params['line'] ?>");'>
    <?php if ($ad_0) { ?>
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                <?php foreach ($adverts as $k => $ad) { ?>
                    <li data-target="#carousel-example-generic" data-slide-to="<?= $k ?>"></li>
                <?php } ?>
            </ol>
            <!-- Wrapper for slides -->
            <div class="carousel-inner sparkly" role="listbox">
                <div class="item active">
                    <a href="<?= $ad_0->link ?>" target="_blank"><img src="<?= $ad_0->image->url ?>"></a>
                    <div class="carousel-caption slide-text">
                        <h3><a href="<?= $ad_0->link ?>" class="slide-title"><?= $ad_0->title ?></a></h3>
                        <p><a href="<?= $ad_0->link ?>" class="slide-summary"><?= $ad_0->summary ?></a></p>
                    </div>
                </div>
                <?php foreach ($adverts as $ad) { ?>
                    <div class="item">
                        <a href="<?= $ad->link ?>" target="_blank"><img src="<?= $ad->image->url ?>"></a>
                        <div class="carousel-caption slide-text">
                            <h3><a href="<?= $ad->link ?>" class="slide-title"><?= $ad->title ?></a></h3>
                            <p><a href="<?= $ad->link ?>" class="slide-summary"><?= $ad->summary ?></a></p>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Controls -->
            <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                <i class="fa fa-chevron-left"></i>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                <i class="fa fa-chevron-right"></i>
                <span class="sr-only">next</span>
            </a>
        </div>
    <?php } ?>
    <div class="article" style='background: repeat-x top url("<?= Yii::$app->params['line'] ?>");'>
        <div class="article-top">
            <h4>最新文章
                <small class="pull-right"><a href="#">最新文章</a></small>
            </h4>
        </div>
        <div id="alist">
            <?=$this->render('_index-article',['articles' => $articles])?>
        </div>
    </div>
</div>
<div class="col-md-3 second-block">
    <div class="follow">
        <div class="head">
            <h4>关注我</h4>
        </div>
        <div class="body">
            <div class="item">
                <i class="fa fa-github"></i>
                <p class="sparkly-p"><a href="https://github.com/GarenGoh" target="_blank">GitHub</a></p>
            </div>
            <div class="item">
                <i class="fa fa-weibo" style="color: #FE2A27;"></i>
                <p class="sparkly-p"><a href="http://weibo.com/garengoh" target="_blank">微 博</a></p>
            </div>
            <div class="item" id="wechat-panel">
                <i class="fa fa-wechat" style="color: #3DAF36;"></i>
                <p class="sparkly-p"><a href="#">微 信</a></p>
            </div>
            <div class="item">
                <i class="fa fa-envelope-o" style="color: #FFB902;"></i>
                <p class="sparkly-p"><a href="mailto:garen.goh&#64;&#113;&#113;&#46;com?subject=来自Garen的个人博客">邮 箱</a>
                </p>
            </div>
            <div class="my-avatar">
                <a href="<?= Url::to(['site/about']) ?>"><img src="<?= Yii::$app->params['me_3'] ?>"></a>
            </div>
            <div class="my-summary">
                <p>网名：
                    <small>Garen.Goh</small>
                </p>
                <p>职业：
                    <small>PHP工程师</small>
                </p>
                <p>主页：
                    <small>wqiang.net</small>
                </p>
                <p>现居：
                    <small>北京.海淀</small>
                </p>
                <p>格言：
                    <small>努力让自己吊到爆</small>
                </p>
            </div>
        </div>
    </div>
    <div class="hot-article">
        <ul class="nav nav-tabs head" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">热门文章</a>
            </li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">站长推荐</a>
            </li>
        </ul>
        <?php
        $hotArticle = Yii::$app->articleService->search()
            ->select(['id', 'title',])
            ->orderBy(['read_count' => SORT_DESC])
            ->limit(8)
            ->all();
        $recommendArticles = Yii::$app->articleService->search()
            ->orderBy(['is_hot' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(10)
            ->all();
        ?>
        <div class="tab-content body">
            <div role="tabpanel" class="tab-pane active" id="home">
                <ul>
                    <?php foreach ($hotArticle as $a) { ?>
                        <li class="title"><a href="<?= $a->url ?>" title="<?= $a->title ?>"><i
                                    class="fa fa-hand-o-right"></i> <?= $a->title ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <div role="tabpanel" class="tab-pane" id="profile">
                <ul>
                    <?php foreach ($recommendArticles as $a) { ?>
                        <li class="title"><a href="<?= $a->url ?>" title="<?= $a->title ?>"><i
                                    class="fa fa-hand-o-right"></i> <?= $a->title ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <div id="fixed-top" class="phone-hide">
        <div class="hot-tag"
             style='padding-top: 10px; background: repeat-x top url("<?= Yii::$app->params['line'] ?>");'>
            <div class="head">
                <h4>热门标签</h4>
            </div>
            <div class="body">
                <div class="containe" style="margin-top: 30px;">
                    <div class="hex" style="background: #986625;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'gulp',]) ?>" class="h3"
                           style="margin-top: 4px;" ">Gulp</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex" style="background: #138898;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'javascript',]) ?>"
                           style="margin-top: 8px;font-size: 12px;">JavaScript</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex hex-one" style="background: #49980c;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'mysql',]) ?>" class="h4"
                           style="margin-top: 6px;">MySql</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex hex-half" style="background: #585398;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'bootstrap',]) ?>"
                           style="font-size: 12px;margin-top: 8px;">Bootstrap</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex" style="background: #984718;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'less',]) ?>"
                           style="font-size: 12px;margin-top: 8px;">Less</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex" style="background: #339858;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'jquery',]) ?>" class="h4"
                           style="margin-top: 6px;">jQuery</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex" style="background: #986625;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'yii',]) ?>" class="h3"
                           style="margin-top: 4px;">Yii2</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex hex-one" style="background: #138898;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'thinkphp',]) ?>"
                           style="margin-top: 8px;font-size: 12px;">ThinkPHP</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex" style="background: #987318;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'nginx',]) ?>" class="h4"
                           style="margin-top: 6px;">Nginx</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex hex-half" style="background: #986625;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'git',]) ?>" class="h3"
                           style="margin-top: 4px;">Git</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex hex-one" style="background: #416b98;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'laravel',]) ?>" class="h5"
                           style="margin-top: 10px;">Laravel</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex hex-one" style="background: #49980c;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'ajax',]) ?>" class="h4"
                           style="margin-top: 6px;">ajax</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex" style="background: #735898;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'css',]) ?>" class="h3"
                           style="margin-top: 4px;">CSS</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex" style="background: #985374;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'seo',]) ?>" class="h3"
                           style="margin-top: 4px;">SEO</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                    <div class="hex hex-half" style="background: #20988e;">
                        <a href="<?= Url::to(['article/hot_tag', 'tag' => 'apache',]) ?>" class="h4"
                           style="margin-top: 6px;">apache</a>
                        <div class="corner-1"></div>
                        <div class="corner-2"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="friendly" style="padding: 0;">
            <div class="head">
                <h4>友情链接:</h4>
            </div>
            <div class="body">
                <ul>
                    <li><a href="https://www.sdk.cn/" target="_blank">SDK.cn</a></li>
                    <li><a href="http://xiajie.me/" target="_blank">Jerry's Blog</a></li>
                    <li><a href="http://www.luxingyun.xyz/" target="_blank">Christian's Blog</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
$js_wechat = "
$('#wechat-panel').popover({
    'trigger': 'hover',
    'html': true,
    'placement': 'top',
    'title': '添加我为微信好友',
    'content': '<img src=\"" . Yii::$app->params['wechatImageUrl'] . "\" style=\"height: 150px;width: 150px\">'
    });
";
$this->registerJs($js_wechat, View::POS_END);
$js_top = "
$('#fixed-top').sticky({
    'top': 0
    });
";
$this->registerJs($js_top, View::POS_END);

$url = Url::to(['site/article'],true);
$js_load_more = <<<ARTICLE
var page = 1;
var url = '{$url}';
var is_end = 0;
$(window).scroll(function () {
   loadData();
 });
 
 function loadData() {
    totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
    if ($(document).height() == totalheight && is_end == 0) {
        loadMore();
    }
 }
 
function loadMore(){
    Ajax.get(url+'?page='+page).success(function(data) {
        if(data.is_end) {
            $('#alist').append('<p style="text-align: center">没有更多的文章...</p>');
            is_end = 1;
        }
        $('#alist').append(data.html);
        page = Number(data.page)+1;
    });
}
ARTICLE;
$this->registerJs($js_load_more, View::POS_END);
?>
