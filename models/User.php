<?php

namespace app\models;

use app\helpers\AppHelper;
use yii\helpers\Html;
use yii\validators\EmailValidator;
use \yii\web\IdentityInterface;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $name
 * @property string $email
 * @property string $open_id
 * @property integer $is_email_enable
 * @property string $mobile
 * @property integer $is_mobile_enable
 * @property string $password_hash
 * @property integer $role_id
 * @property integer $avatar_id
 * @property integer $is_enable
 * @property integer $created_at
 * @property integer $logged_at
 */
class User extends BaseActiveRecord implements IdentityInterface
{
    const ROLE_MEMBER = 0;
    const ROLE_MANAGER = 1;
    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => '角色ID',
            'username' => '用户名',
            'password' => '登录密码',
            'password_hash' => '密码',
            'name' => '姓名',
            'email' => '邮箱',
            'mobile' => '手机号码',
            'created_at' => '注册时间',
            'logged_at' => '最近登录',
            'is_enable' => '帐号可用',
            'avatar_id' => '头像ID',
            'avatar' => '头像'
        ];
    }

    private static $_mobilePattern = '/^1[3-9]{1}[0-9]{9}$/';

    public function rules()
    {
        $filterFields = [
            'password', 'name', 'email', 'mobile'
        ];

        return [
            [$filterFields, 'filter', 'filter' => function ($value) {
                return Html::encode(trim($value));//去除左右的空格，并将html标签转换为转义字符
            }],
            ['email', 'required'],
            ['role_id', 'default', 'value' => self::ROLE_MEMBER],
            ['role_id', 'in', 'range' => [self::ROLE_MEMBER, self::ROLE_MANAGER]],
            ['email', 'filter', 'filter' => 'strtolower'],//转换为小写
            ['email', 'email', 'when' => function () {
                AppHelper::log('test', '', Yii::$app->params['defaultAvatarIds']);
                return !empty($this->email) && !$this->hasErrors();
            }],
            ['email', 'unique', 'when' => function () {//邮箱必须是独一无二的
                return !empty($this->email) && !$this->hasErrors();
            }],
            ['mobile', 'unique', 'when' => function () {//手机必须是独一无二的
                return !empty($this->mobile) && !$this->hasErrors();
            }],
            ['mobile', 'match', 'message' => '手机号格式不对！', 'pattern' => self::$_mobilePattern, 'when' => function () {
                return !empty($this->mobile) && !$this->hasErrors();//对比$_mobilePattern
            }],
            ['username', 'default', 'value' => function () {
                return Yii::$app->snowflake->generateID();
            }],
            ['username', 'string', 'length' => [3, 64], 'encoding' => 'utf-8'],
            ['username', 'unique', 'when' => function () {
                return !$this->hasErrors();
            }],
            ['mobile', 'unique', 'when' => function () {
                return !$this->hasErrors() && !empty($this->mobile);
            }],
            ['password', 'default', 'value' => (string)rand(10000, 99999)],
            ['password', 'string', 'length' => [4, 50]],
            ['is_enable', 'default', 'value' => self::BOOLEAN_YES],
            [['is_email_enable', 'is_mobile_enable'], 'default', 'value' => self::BOOLEAN_NO],
            ['created_at', 'default', 'value' => time()],
            ['avatar_id', 'default', 'value' => array_keys(Yii::$app->params['defaultAvatarIds'])[ rand(0, count(Yii::$app->params['defaultAvatarIds']) -1)]],
            [['is_email_enable', 'is_mobile_enable', 'role_id', 'avatar_id', 'is_enable', 'created_at', 'logged_at'], 'integer'],
            [['username', 'name', 'email'], 'string', 'max' => 50],
            [['open_id'], 'string', 'max' => 40],
            [['mobile'], 'string', 'max' => 11],
            [['password_hash'], 'string', 'max' => 64],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['open_id'], 'unique'],
        ];
    }


    /* IdentityInterface 实现开始 */

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return self::find()->andWhere(['id' => $id])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::find()->andWhere(['access_token' => $token]);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        $key = 'Fuck the user ---> ' . $this->id;

        return md5($key);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /* IdentityInterface 实现结束 */


    public function beforeSave($insert)
    {
        if (!$insert) {
            $this->role_id = $this->getOldAttribute('role_id');
        }

        if ($this->password) {
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        }

        return parent::beforeSave($insert);
    }

    public static function findByAccount($account)
    {
        $attributeName = 'username';

        //如果符合手机好的样式，$attributeName = 'mobile'
        if (preg_match(self::$_mobilePattern, $account)) {
            $attributeName = 'mobile';
        }

        //验证是否是可用的邮箱，如果是，则$attributeName = 'email'
        $validator = new EmailValidator();
        if ($validator->validate($account, $error)) {
            $attributeName = 'email';
        }

        $where = [$attributeName => $account];

        return static::find()->andWhere($where)->one();
    }

    public static function getRoleMap($status = null)
    {
        $map = [
            static::ROLE_MEMBER => '成员',
            static::ROLE_MANAGER => '管理',
        ];

        return !empty($status) && $map[ $status ] ? $map[ $status ] : $map;
    }

    public function getAvatar()
    {
        return $this->avatar_id ? Yii::$app->fileService->search(['id' => $this->avatar_id])->one() : "";
    }
}
