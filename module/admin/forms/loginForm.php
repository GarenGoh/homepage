<?php
namespace app\module\admin\forms;

use yii\base\Model;
use Yii;
use app\models\User;

class LoginForm extends Model
{
    public $account;
    public $password;
    public $is_remember = true;

    public $_user = false;

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->userService->login($this->getUser());
        } else {
            return false;
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByAccount($this->account);
        }
        return $this->_user;
    }

    public function rules()
    {
        $requireFields = ['account', 'password'];

        $rules = [
            [$requireFields, 'required'],//必需的
            [$requireFields, 'filter', 'filter' => 'trim'],//去除前后的空格
            [$requireFields, 'filter', 'filter' => 'strip_tags'],//去除html标签
            ['account', 'validateAccount'],
            ['password', 'validatePassword'],
        ];

        return $rules;
    }

    public function validatePassword($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();

        if (!Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
            $this->addError($attribute, '密码错误');
        }
    }

    public function validateAccount($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if (!$user) {
            $this->addError($attribute, '帐号不存在');
            return;
        }

        if (!Yii::$app->userService->isRoot($user->id)) {
            $this->addError($attribute, '非管理员禁止登录');
            return;
        }

        if (!$user->is_enable) {
            $this->addError($attribute, '帐号已禁用');
            return;
        }
    }
}

?>
