<?php

namespace app\module\admin\widgets;

use Yii;
use yii\bootstrap\ActiveField;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

class AdminActiveField extends ActiveField
{
    /*
     * 由对象名和属性名生成一段字符串作为对象属性的ID标识；
     * 例如：article-title
     */
    public function getAttributeId()
    {
        return Html::getInputId($this->model, $this->attribute);
    }

    public function getAttributeValue()
    {
        return Html::getAttributeValue($this->model, $this->attribute);
    }

    public function getAttributeName()
    {
        return Html::getAttributeName($this->attribute);
    }

    public function image($options = [])
    {
        return $this->file($options, true);
    }

    public function file($options = [], $isImage = false)
    {
        $prefix = $options['prefix'];
        $fileId = $prefix . '_image';
        $inputId = $this->getAttributeId();

        $html = Html::activeHiddenInput($this->model, $this->attribute, $options);
        $html .= '<div id="' . $fileId . '" class="upload">';
        $html .= '<img style="display:' . ($this->model->image ? "" : "none") . ';" class="file" src="' . ($this->model->image ? $this->model->image->url : "") . '">';
        $html .= '
        <i class="select fa fa-' . ($isImage ? 'picture-o' : 'file') . '"></i>
        <i style="display:none;" class="uploading fa fa-spinner fa-spin"></i>
        ';
        $html .= '<input type="file" class="input" name="' . $fileId . '"></div>';
        $this->parts['{input}'] = $html;
        $js = "
            var {$fileId} = $('#{$fileId}');
            var {$fileId}_model = $('#{$inputId}');

            {$fileId}.find('.input').fileupload({
            url: '" . Url::to(['/api/file/file', 'prefix' => $prefix, 'name' => $fileId]) . "',
            start: function() {
                {$fileId}.find('.file').hide();
                {$fileId}.find('.select').hide();
                {$fileId}.find('.uploading').show();
            },
            success: function (result) {
                var json=eval('(' + result + ')');
                {$fileId}_model.val(json.id);
                {$fileId}.find('.file').attr('src', json.url);
                {$fileId}.find('.file').show();
                Message.success('文件已上传，保存数据后生效！');
            },
            error: function (xhr, status) {
                {$fileId}.find('.select').show();
                if (typeof xhr.responseJSON === 'undefined') {
                    Message.error('文件上传失败');
                } else {
                    Message.error(xhr.responseJSON.message);
                }
            },
            complete: function() {
                {$fileId}.find('.uploading').hide();
            }
          });
          ";
        $this->form->view->registerJs($js, View::POS_END);
        return $this;
    }

    /*
     * 为一个属性添加两个input，以数组提交
     */
    public function textUrl($options = [])
    {
        $this->adjustLabelFor($options);//为label设置for属性，需要设置$options['id']

        $value = $this->getAttributeValue();

        $html = '<div class="row">';
        $option1 = [
            'class' => 'form-control',
            'value' => isset($value[0]) ? $value[0] : null,
            'placeholder' => '输入链接的文字'
        ];
        $html .= '<div class="col-lg-6">' . Html::activeTextInput($this->model, $this->attribute . '[]', $option1) . '</div>';
        $option2 = [
            'class' => 'form-control',
            'value' => isset($value[1]) ? $value[1] : null,
            'placeholder' => '输入链接的地址，一般以http开始'
        ];
        $html .= '<div class="col-lg-6">' . Html::activeTextInput($this->model, $this->attribute . '[]', $option2) . '</div>';
        $html .= '</div>';

        $this->parts['{input}'] = $html;
        return $this;
    }
}
