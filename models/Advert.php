<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%advert}}".
 *
 * @property integer $id
 * @property integer $position_id
 * @property integer $image_id
 * @property string $title
 * @property string $link
 * @property string $target
 * @property string $summary
 * @property integer $is_enable
 * @property integer $weight
 * @property integer $created_at
 * @property file $image
 */
class Advert extends BaseActiveRecord
{
    const POSITION_HOME = 0;
    const POSITION_OTHER = 1;

    const TARGET_BLANK = '_blank';
    const TARGET_SELF = '_self';

    public static function tableName()
    {
        return '{{%advert}}';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'is_enable' => '是否可用',
            'created_at' => '创建时间',
            'position_id' => '位置ID',
            'position' => '位置',
            'summary' => '简介',
            'image_id' => '封面ID',
            'image' => '封面',
            'link' => '链接',
            'target' => '打开方式',
            'weight' => '权重'
        ];
    }

    public static function getPositionMap($status = null)
    {

        $map = [
            static::POSITION_HOME => '首页',
            static::POSITION_OTHER => '其他',
        ];
        return !is_null($status) && $map[$status] ? $map[$status] : $map;
    }

    public function rules()
    {
        return [
            ['position_id', 'default', 'value' => static::POSITION_HOME],
            [['is_enable', 'weight'], 'default', 'value' => 1],
            [['title'], 'required'],
            [['summary'], 'string'],
            [['title'], 'string', 'max' => 200],
            ['created_at', 'default', 'value' => time()]
        ];
    }

    /*
     * 返回文章的封面图，它是一个File类的实例
     */
    public function getImage()
    {
        return $this->image_id ? Yii::$app->fileService->search(['id' => $this->image_id])->one() : "";
    }

    public static function getTargetMap($target = false)
    {
        $arr = [
            static::TARGET_BLANK => '新窗口',
            static::TARGET_SELF => '当前窗口',
        ];

        if ($target === false) {
            return $arr;
        } else {
            return isset($arr[$target]) ? $arr[$target] : '';
        }
    }
}

?>
