<?php
namespace app\models;

use Yii;

class File extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%file}}';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '文件名',
            'old_name' => '旧文件名',
            'size' => '文件大小',
            'type' => '文件类型',
            'prefix' => '路径前缀',
        ];
    }

    public function rules()
    {
        return [
            [['size'], 'default', 'value' => 0],
            [['name', 'type', 'prefix'], 'required'],
            [['name', 'old_name', 'type', 'prefix'], 'string', 'max' => 200],
        ];
    }

    public function getKey()
    {
        return $this->prefix . $this->name;
    }

    public function getUrl()
    {
        return Yii::$app->params['uploadDir'] . $this->getKey();
    }
}

?>
