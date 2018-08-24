<?php
/* @var $this yii\web\View */
/* @var $model app\models\Article */

use yii\bootstrap\ActiveForm;
use app\helpers\Url;
use yii\helpers\Html;
use app\models\Article;
use yii\web\View;

$form = ActiveForm::begin([
    'method' => 'post',
    'action' => Url::action(),
    'fieldConfig' => [
        'class' => 'app\module\admin\widgets\AdminActiveField'
    ]
]);
?>
<div class="content-wrapper container">
    <div class="col-sm-2">

    </div>
    <div class="col-sm-10">
        <h2><?= $model->isNewRecord ? "创建" : "更新" ?>文章</h2>
        <?= $form->field($model, 'title')->textInput() ?>
        <?= $form->field($model, 'image_id', [
            'labelOptions' => [
                'label' => $model->getAttributeLabel('image')
            ]
        ])->image(['prefix' => 'article']) ?>
        <?= $form->field($model, 'category')->dropDownList(Article::getCategoryMap(), ['prompt' => '选择分类', 'style' => 'width:100%']) ?>
        <?= $form->field($model, 'keywords')->textInput(['placeholder' => '多个关键词以“,”隔开']) ?>
        <?= $form->field($model, 'source')->textUrl() ?>
        <?= $form->field($model, 'is_hot')->dropDownList(Article::getBooleanMap(), ['prompt' => '该文章是否排在所有文章之前', 'style' => 'width:100%']) ?>
        <?= $form->field($model, 'is_enable')->dropDownList(Article::getBooleanMap(), ['prompt' => '该文章在前台是否显示', 'style' => 'width:100%']) ?>
        <?= $form->field($model, 'summary')->textarea() ?>
        <?= $form->field($model, 'content')->textarea(['id' => "editor", 'placeholder' => "这里输入内容", 'autofocus' => true]) ?>
        <?= $form->field($model, 'read_count')->textInput() ?>
        <?= Html::submitButton('提交保存', [
            'class' => 'btn btn-primary btn-block'
        ]) ?>
    </div>
</div>
<?php
ActiveForm::end();
$uploadUrl = Url::to(['/api/file/editor-file']);
$a = Yii::$app->articleService->search()
    ->orderBy(['id' => SORT_DESC])
    ->one();
$id = $model->isNewRecord ? ($a->id + 1) : $model->id;
$js = <<<JS
var editor = new Simditor({
        textarea: $('#editor'),
        toolbar: [
            'title', 'bold', 'italic', 'underline', 'strikethrough', 'color','ol', 'ul','blockquote',
             'code', 'table', 'link', 'image', 'hr', 'alignment', 'html'
        ],
        defaultImage: 'http://wqiang.ts/images/default/imgUpFail.jpg',
        upload: {
            url: '{$uploadUrl}',
            params: {
                id: '{$id}'
            },
            fileKey: 'image',
            connectionCount: 3,
            leaveConfirm: '正在上传文件，如果离开上传会自动取消'
        },
        allowedTags: [
            'br','iframe','li','font','embed'
        ],
        allowedAttributes: {
            font: ['color','size'],
            img: ['src', 'alt', 'width', 'height', 'data-non-image'],
            a: ['href', 'target'],
            code: ['class'],
            iframe: ['id', 'src', 'name', 'style','scrolling','frameborder','allowtransparency','height'],
            p: ['id', 'data-align'],
            h4: ['id', 'data-align'],
            h3: ['id', 'data-align'],
            h2: ['id', 'data-align'],
            embed: ['src', 'autostart','data-align']
        },
        allowedStyles: {
            span: ['color', 'font-size', 'font-family']
        }
    });
JS;
$this->registerJs($js, View::POS_END)
?>
