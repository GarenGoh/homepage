<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Article;

?>
<div>
    <div class="header col-md-12">
        <h1 class="title">文章管理</h1>
        <?= Html::a('<i class="fa fa-plus"></i> 创建文章', ['article/create'], ['class' => 'btn btn-danger pull-right']) ?>
    </div>
    <div class="col-md-10">
        <div class="table-responsive">
            <?= GridView::widget([
                'pager' => [
                    'firstPageLabel' => true,
                    'lastPageLabel' => true,
                    'maxButtonCount' => 5
                ],
                'layout' => "{items}\n{pager}{summary}",
                'tableOptions' => [
                    'class' => 'table table-striped table-hover'
                ],
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id',
                    [
                        'attribute' => 'title',
                        'content' => function ($model) {
                            return Html::a(\app\helpers\Tools::string($model->title, 10), $model->url, ['id' => $model->id, 'target' => "_blank"]);
                        }
                    ],
                    'creator_id',
                    [
                        'attribute' => 'category',
                        'content' => function ($model) {
                            return Article::getCategoryMap($model->category);
                        }
                    ],
                    [
                        'attribute' => 'is_enable',
                        'content' => function ($model) {
                            $status = $model->is_enable ? 'success' : 'danger';
                            return '<span class="text-' . $status . '">' . Article::getBooleanMap($model->is_enable) . '</span>';
                        }
                    ],
                    [
                        'attribute' => 'is_hot',
                        'content' => function ($model) {
                            $status = $model->is_hot ? 'success' : 'danger';
                            return '<span class="text-' . $status . '">' . Article::getBooleanMap($model->is_hot) . '</span>';
                        }
                    ],
                    'read_count',
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:Y/m/d']
                    ],
                    [
                        'content' => function ($model) {
                            $buttons = [];
                            $buttons[] = Html::a('修改', ['article/update', 'id' => $model->id]);
                            $buttons[] = Html::a('删除', ['article/delete', 'id' => $model->id], ['data-method' => 'post', 'data-confirm' => '确定要删除吗？']);
                            return implode('&nbsp;&nbsp;', $buttons);
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
    <div class="col-md-2">
    </div>
</div>
