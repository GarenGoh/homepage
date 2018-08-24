<?php
/* @var $this yii\web\View */
/* @var $model app\models\Note */

use yii\bootstrap\ActiveForm;
use app\helpers\Url;
use yii\helpers\Html;
use app\models\Note;

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
        <h2><?= $model->isNewRecord ? "创建" : "更新" ?>便签</h2>
        <?= $form->field($model, 'title')->textInput() ?>
        <?= $form->field($model, 'keywords')->textInput() ?>
        <?= $form->field($model, 'content')->textarea(['rows' => 15]) ?>
        <?= $form->field($model, 'is_enable')->dropDownList(Note::getBooleanMap(), ['prompt' => '该文章在前台是否显示', 'style' => 'width:100%']) ?>
        <?= $form->field($model, 'weight')->textInput() ?>

        <?= Html::submitButton('提交保存', [
            'class' => 'btn btn-primary btn-block'
        ]) ?>
    </div>
</div>
<?php
ActiveForm::end();
?>
