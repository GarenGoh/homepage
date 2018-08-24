<?php
namespace app\forms;

use yii\web\UploadedFile;

/**
 * 上传文件
 */
class UploadForm extends BaseForm
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }
}

?>
