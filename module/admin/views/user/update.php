<?php
/* @var $this yii\web\View */
/* @var $model app\models\User */

use yii\bootstrap\ActiveForm;
use app\helpers\Url;
use yii\helpers\Html;
use app\models\User;

$form = ActiveForm::begin([
    'method' => 'post',
    'action' => Url::action(),
    'fieldConfig' => [
    ]
]);
?>
<div>
    <div class="col-sm-2">

    </div>
    <div class="col-sm-10">
        <h2><?= $model->isNewRecord ? "创建" : "更新" ?>用户信息</h2>
        <?= $form->field($model, 'username')->textInput() ?>
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'email')->textInput() ?>
        <?= $form->field($model, 'mobile')->textInput() ?>
        <?= $form->field($model, 'password')->textInput() ?>
        <?= $form->field($model, 'role_id')->dropDownList(User::getRoleMap(), ['prompt' => '请选择角色', 'style' => 'width:100%']) ?>
        <?= $form->field($model, 'avatar_id')->textInput() ?>
        <?= $form->field($model, 'is_enable')->dropDownList(User::getBooleanMap(), ['prompt' => '该账号是否可以登录', 'style' => 'width:100%']) ?>
        <?= Html::submitButton('提交保存', [
            'class' => 'btn btn-primary btn-block'
        ]) ?>
    </div>
</div>
<?php
ActiveForm::end();
?>
