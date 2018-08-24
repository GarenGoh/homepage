<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%note}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $keywords
 * @property integer $is_enable
 * @property integer $weight
 * @property integer $created_at
 */
class Note extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%note}}';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'is_enable' => '是否可用',
            'created_at' => '创建时间',
            'keywords' => '关键词',
            'content' => '内容',
            'weight' => '权重'
        ];
    }

    public function rules()
    {
        return [
            [['is_enable', 'weight'], 'default', 'value' => 1],
            [['title'], 'required'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 200],
            ['created_at', 'default', 'value' => time()]
        ];
    }
}

?>
