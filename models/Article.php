<?php
namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
 * @property integer $image_id
 * @property integer $read_count
 * @property string $title
 * @property string $category
 * @property string $keywords
 * @property string $summary
 * @property string $source
 * @property string $content
 * @property integer $is_enable
 * @property integer $creator_id
 * @property integer $created_at
 * @property integer $is_hot
 * @property file $image
 */
class Article extends BaseActiveRecord
{
    const CATEGORY_PHP = "php";
    const CATEGORY_LINUX = "linux";
    const CATEGORY_DB = "db";
    const CATEGORY_FRONTEND = "frontend";
    const CATEGORY_LEARN = "learn";

    public static function tableName()
    {
        return '{{%article}}';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'is_enable' => '是否可用',
            'created_at' => '创建时间',
            'creator_id' => '创建者',
            'content' => '内容',
            'category' => '分类',
            'keywords' => '关键词',
            'summary' => '简介',
            'read_count' => '阅读数',
            'is_hot' => '是否热点',
            'image_id' => '封面ID',
            'image' => '封面',
            'source' => '来源',
        ];
    }

    public static function getCategoryMap($status = null)
    {

        $map = [
            static::CATEGORY_PHP => 'PHP',
            static::CATEGORY_LINUX => 'Linux',
            static::CATEGORY_DB => '数据库',
            static::CATEGORY_FRONTEND => '前端',
            static::CATEGORY_LEARN => '学无止境'
        ];
        return !is_null($status) && $map[$status] ? $map[$status] : $map;
    }

    public function rules()
    {
        return [
            [['is_hot', 'read_count', 'image_id'], 'default', 'value' => 0],
            ['category', 'default', 'value' => static::CATEGORY_LEARN],
            [['is_enable'], 'default', 'value' => 1],
            [['title'], 'required'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 200],
            [['keywords'], 'string', 'max' => 100],
            ['created_at', 'default', 'value' => time()],
            ['creator_id', 'default', 'value' => Yii::$app->userService->getId()],
            ['source', 'filter', 'filter' => function () {
                return is_array($this->source) ? implode('#o#', $this->source) : $this->source;
            }]
        ];
    }

    /*
     * 返回文章的封面图，它是一个File类的实例
     */
    public function getImage()
    {
        return $this->image_id ? Yii::$app->fileService->search(['id' => $this->image_id])->one() : "";
    }

    /*
     * 返回当前文章的URL
     */
    public function getUrl()
    {
        return Url::to(['/article/view', 'id' => $this->id]);// ‘/article’中的‘/’不能少，否则后台跳转失败
    }

    /*
     * 将文章来源拆分成一个包含名字和URL的数组
     */
    public function afterFind()
    {
        $this->source = $this->source ? explode('#o#', $this->source) : [];
    }
}

?>
