<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

?>
<div>
    <div class="header col-md-12">
        <h1 class="title">用户管理</h1>
        <?= Html::a('<i class="fa fa-plus"></i> 创建用户', ['user/create', 'role' => Yii::$app->request->getQueryParam('role')], ['class' => 'btn btn-danger pull-right']) ?>
    </div>
    <div class="col-md-10" style="float: left">
        <div class="table-responsive">
            <?= GridView::widget([
                'pager' => [
                    'firstPageLabel' => true,//显示第一页
                    'lastPageLabel' => true,//显示最后一页
                    'maxButtonCount' => 5//显示分页个数，默认10
                ],
                'layout' => "{items}\n{pager}{summary}",//页面布局，分页和页面介绍放在项目下面
                'tableOptions' => [
                    'class' => 'table table-striped table-hover'//设置表格的class，所有class来源于bootstrap
                ],
                'dataProvider' => $dataProvider,//数据供应者，数据来源，是一个yii\data\ActiveDataProvider实例
                'columns' => [
                    'id',
                    'username',
                    'name',
                    [
                        'attribute' => 'email',
                        'content' => function ($model) {
                            return '<span class="text-' . ($model->is_email_enable ? 'success' : 'danger') . '">' . $model->email . '</span>';
                        }
                    ],
                    [
                        'attribute' => 'mobile',
                        'content' => function ($model) {
                            return '<span class="text-' . ($model->is_mobile_enable ? 'success' : 'danger') . '">' . $model->mobile . '</span>';
                        }
                    ],
                    [
                        'attribute' => 'is_enable',
                        'content' => function ($model) {
                            $status = $model->is_enable ? 'success' : 'danger';
                            return '<span class="text-' . $status . '">' . User::getBooleanMap($model->is_enable) . '</span>';
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:Y/m/d']
                    ],
                    [
                        'attribute' => 'logged_at',
                        'format' => ['date', 'php:Y/m/d']
                    ],
                    [
                        'content' => function ($model) {
                            $buttons = [];
                            $buttons[] = Html::a('修改', ['user/update', 'id' => $model->id]);
                            $buttons[] = Html::a('删除', ['user/delete', 'id' => $model->id], ['data-method' => 'post', 'data-confirm' => '确定要删除吗？']);
                            return implode('&nbsp;&nbsp;', $buttons);
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
    <div class="col-md-2" style="float: left">
    </div>
</div>
