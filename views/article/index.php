<?php
use yii\data\ActiveDataProvider;
use app\models\Article;
use yii\widgets\LinkPager;
use app\helpers\Tools;

$where = Yii::$app->request->get();

if (isset($category)) {
    $where['category'] = $category;
    $this->title = Article::getCategoryMap($category);
} elseif (isset($tag)) {
    $where['keywords'] = $tag;
    $this->title = '热门标签:'.$tag;
}
$query = Yii::$app->articleService->search($where)
    ->orderBy(['id' => SORT_DESC]);
$provider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => [
        'pageSize' => 8
    ]
]);
$articles = $provider->getModels();
$this->params['pageId'] = 'article-index';
$hotArticles = Yii::$app->articleService->search()
    ->select(['id', 'title', 'read_count'])
    ->limit(5)
    ->orderBy(['read_count' => SORT_DESC])
    ->all();
?>
<div class="col-md-9 primary-block">
    <div class="h1 category">
        <?= isset($category) ? Article::getCategoryMap($category) : '' ?>
        <?= isset($tag) ? strtoupper($tag) : '' ?>
    </div>
    <?php
    foreach ($articles as $a) {
        ?>
        <article>
            <div class="left">
                <a href="<?= $a->url ?>"><img src="<?= $a->image ? $a->image->url : '' ?>"></a>
            </div>
            <div class="right">
                <a href="<?= $a->url ?>">
                    <h4><?= Tools::string($a->title, 33) ?></h4>
                    <span class="small pull-left eye pc-hide"><i class="fa fa-leaf leaf "></i>
                        <?php
                        if ($a->keywords) {
                            $n = strpos($a->keywords, ',');
                            echo $n ? substr($a->keywords, 0, $n) : $a->keywords;
                        }
                        ?>
                            </span>
                    <span class="small pull-right clock pc-hide"><i
                            class="fa fa-eye eye"></i>&nbsp;&nbsp;<?= $a->read_count ?></span>
                </a>
                <p class="summary"><?= Tools::string($a->summary, 160) ?></p>
                <p class="phone-hide ">
                    <i class="fa fa-leaf leaf"></i> php学习&nbsp;&nbsp;&nbsp;
                    <i class="fa fa-clock-o clock"></i> <?= date('Y-m-d', $a->created_at) ?>&nbsp;&nbsp;&nbsp;
                    <i class="fa fa-comment-o comment"></i> 评论（ <span id="<?= 'sourceId::article' . $a->id ?>"
                                                                      class="cy_cmt_count"></span>
                    <script id="cy_cmt_num"
                            src="https://changyan.sohu.com/upload/plugins/plugins.list.count.js?clientId=cysBHKkOH">
                    </script>
                    ）&nbsp;&nbsp;&nbsp;
                    <i class="fa fa-eye eye"></i>浏览（<?= $a->read_count ?>）&nbsp;&nbsp;&nbsp;
                    <a class="pull-right" href="#">阅读原文>></a>
                </p>
            </div>
        </article>
    <?php } ?>
    <div class="linkPager">
        <?php
        $linkPager = new LinkPager([
            'firstPageLabel' => true,
            'lastPageLabel' => true,
            'maxButtonCount' => 5,
            'pagination' => $provider->pagination
        ]);
        $linkPager->run();
        ?>
    </div>
</div>
<div class="col-md-3 secondary-block">
    <?php ?>
    <div class="hot-article">
        <div class="head col-md-6">
            <h3>热门文章</h3>
        </div>
        <div class="col-md-12 body">
            <ul>
                <?php foreach ($hotArticles as $a) { ?>
                    <li><a href="<?= $a->url ?>"><?= $a->title ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>
