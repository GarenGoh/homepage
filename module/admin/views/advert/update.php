<?php
/* @var $this yii\web\View */
/* @var $model app\models\Advert */

use yii\bootstrap\ActiveForm;
use app\helpers\Url;
use yii\helpers\Html;
use app\models\Advert;

$form = ActiveForm::begin([
    'method' => 'post',
    'action' => Url::action(),
    'fieldConfig' => [
        'class' => 'app\module\admin\widgets\AdminActiveField'
    ]
]);
?>
<div>
    <div class="col-sm-2">

    </div>
    <div class="col-sm-10">
        <h2><?= $model->isNewRecord ? "创建" : "更新" ?>广告</h2>
        <?= $form->field($model, 'title')->textInput() ?>
        <?= $form->field($model, 'image_id', [
            'labelOptions' => [
                'label' => $model->getAttributeLabel('image')
            ]
        ])->image(['prefix' => 'slide']) ?>
        <?= $form->field($model, 'position_id')->dropDownList(Advert::getPositionMap(), ['style' => 'width:100%']) ?>
        <?= $form->field($model, 'target')->dropDownList(Advert::getTargetMap(), ['style' => 'width:100%']) ?>
        <?= $form->field($model, 'link')->textInput() ?>
        <?= $form->field($model, 'is_enable')->dropDownList(Advert::getBooleanMap(), ['prompt' => '该文章在前台是否显示', 'style' => 'width:100%']) ?>
        <?= $form->field($model, 'weight')->textInput() ?>
        <?= $form->field($model, 'summary')->textarea() ?>
        <?= Html::submitButton('提交保存', [
            'class' => 'btn btn-primary btn-block'
        ]) ?>
    </div>
</div>
<?php
ActiveForm::end();
?>
