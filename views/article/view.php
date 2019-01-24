<?php
/* @var $this \yii\web\View */
/* @var $model \app\models\Article */
use app\helpers\Url;
use yii\web\View;
use yii\helpers\Html;

$this->title = $model->title;
$this->registerMetaTag(["name" => "keywords", "content" => $model->keywords . ',Garen.Goh']);
$this->registerMetaTag(["name" => "description", "content" => $model->summary]);
$this->params['pageId'] = 'article-view';
$hotArticles = Yii::$app->articleService->search(['category' => $model->category])
    ->select(['id', 'title', 'read_count'])
    ->limit(5)
    ->orderBy(['read_count' => SORT_DESC])
    ->all();
$model->source = explode('#o#', $model->source);
$this->registerJs('hljs.initHighlightingOnLoad();', View::POS_END);//代码高亮
?>
<div class="col-md-12 nav">
    <p class="">
        <a class="pull-left home" href="<?= Yii::$app->request->hostInfo ?>">网站首页</a>
        <a class="pull-left cate" href="<?= Url::to(['article/php']) ?>">PHP</a>
        <span class="pull-right motto">种一棵树最好的时间是十年前,其次是现在。</span>
    </p>
</div>
<div class="col-md-9 article"'>
<h3 class="title"><?= $model->title ?></h3>
<p class="article-info">
    发布时间：<?= date('Y-m-d', $model->created_at) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    来源：
    <?php
    if ($model->source && isset($model->source[0]) && isset($model->source[1])) {
        echo Html::a($model->source[0], $model->source[1], ['style' => "color:#AAA"]);
    } else {
        echo Html::a('Garen', Yii::$app->request->hostInfo);
    }
    ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    阅读（<?= $model->read_count ?>）
</p>
<!--百度分享按钮-->
<div class="bdsharebuttonbox">
    <a href="#" class="bds_more" data-cmd="more"></a>
    <a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
    <a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
    <a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
    <a href="#" class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a>
    <a href="#" class="bds_mail" data-cmd="mail" title="分享到邮件分享"></a>
    <a href="#" class="bds_fbook" data-cmd="fbook" title="分享到Facebook"></a>
    <a href="#" class="bds_twi" data-cmd="twi" title="分享到Twitter"></a>
    <a href="#" class="bds_evernotecn" data-cmd="evernotecn" title="分享到印象笔记"></a>
</div>
<?php
$js = '
    window._bd_share_config={
            "common":{
                "bdSnsKey":{},
                "bdText":"",
                "bdMini":"2",
                "bdMiniList":["copy","isohu","linkedin","mshare","bdysc","renren","tqq","kaixin001","meilishuo","mogujie","youdao","wealink","ty"],
                "bdPic":"",
                "bdStyle":"1",
                "bdSize":"24"
            },
            "share":{},
            "image":{
                "viewList":["tsina","qzone","weixin","sqq","mail","fbook","twi","evernotecn"],
                "viewText":"分享到：",
                "viewSize":"16"
            },
            "selectShare":{
                "bdContainerClass":null,
                "bdSelectMiniList":["tsina","qzone","weixin","sqq","mail","fbook","twi","evernotecn"]
            }
        };
        with(document)0[(getElementsByTagName(\'head\')[0]||body).appendChild(createElement(\'script\')).src=\'https://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=\'+~(-new Date()/36e5)];
    ';
$this->registerJs($js, View::POS_END);
?>
<div class="content">
    <?= $model->content ?>
</div>
<!--畅言评论,PC和WAP自适应版-->
<div id="SOHUCS" sid=<?= 'article' . $model->id ?>></div>
<script type="text/javascript">
    (function () {
        var appid = 'cysBHKkOH';
        var conf = 'prod_fe99f3618320574ed59a903b3ffcc946';
        var width = window.innerWidth || document.documentElement.clientWidth;
        if (width < 960) {
            window.document.write('<script id="changyan_mobile_js" charset="utf-8" type="text/javascript" src="https://changyan.sohu.com/upload/mobile/wap-js/changyan_mobile.js?client_id=' + appid + '&conf=' + conf + '"><\/script>');
        } else {
            var loadJs = function (d, a) {
                var c = document.getElementsByTagName("head")[0] || document.head || document.documentElement;
                var b = document.createElement("script");
                b.setAttribute("type", "text/javascript");
                b.setAttribute("charset", "UTF-8");
                b.setAttribute("src", d);
                if (typeof a === "function") {
                    if (window.attachEvent) {
                        b.onreadystatechange = function () {
                            var e = b.readyState;
                            if (e === "loaded" || e === "complete") {
                                b.onreadystatechange = null;
                                a()
                            }
                        }
                    } else {
                        b.onload = a
                    }
                }
                c.appendChild(b)
            };
            loadJs(
                "https://changyan.sohu.com/upload/changyan.js",
                function () {
                    window.changyan.api.config({
                        appid: appid,
                        conf: conf
                    })
                }
            );
        }
    })();
</script>
</div>
<div class="col-md-3 secondary-block">
    <div class="hot-article">
        <div class="head col-md-4">
            <h4 class="title">热门文章</h4>
        </div>
        <div class="col-md-12 body">
            <ul>
                <?php foreach ($hotArticles as $article) { ?>
                    <li><a href="<?= $article->url ?>"><?= $article->title ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="article-tag col-md-12 phone-hide">
        <div class="head col-md-4">
            <h4 class="title">热门标签</h4>
        </div>
        <div class="col-md-12 body"></div>
        <div class="containe" style="margin-top: 30px;">
            <div class="hex" style="background: #986625;">
                <a href="<?= Url::to(['article/hot_tag', 'tag' => 'gulp',]) ?>" class="h3" style="margin-top: 4px;"
                ">Gulp</a>
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
                <a href="<?= Url::to(['article/hot_tag', 'tag' => 'mysql',]) ?>" class="h4" style="margin-top: 6px;">MySql</a>
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
                <a href="<?= Url::to(['article/hot_tag', 'tag' => 'jquery',]) ?>" class="h4" style="margin-top: 6px;">jQuery</a>
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
                <a href="<?= Url::to(['article/hot_tag', 'tag' => 'nginx',]) ?>" class="h4" style="margin-top: 6px;">Nginx</a>
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
                <a href="<?= Url::to(['article/hot_tag', 'tag' => 'laravel',]) ?>" class="h5" style="margin-top: 10px;">Laravel</a>
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
                <a href="<?= Url::to(['article/hot_tag', 'tag' => 'apache',]) ?>" class="h4" style="margin-top: 6px;">apache</a>
                <div class="corner-1"></div>
                <div class="corner-2"></div>
            </div>
        </div>
    </div>
</div>
<?php
$js = "
$('.article-tag').sticky({
        'top': 0
      });
";
$this->registerJs($js, View::POS_END)
?>
