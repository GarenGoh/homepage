<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "daily_info".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $email
 * @property string $email_password
 * @property integer $created_at
 */
class DailyInfo extends BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'daily_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'integer'],
            [['email'], 'required'],
            [['email', 'email_password'], 'string', 'max' => 50],
            ['user_id', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'email' => '邮箱',
            'email_password' => '邮箱密码',
            'created_at' => '创建时间',
        ];
    }
}
