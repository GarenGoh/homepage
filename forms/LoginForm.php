<?php

namespace app\forms;

use app\models\User;
use Yii;
use yii\web\Cookie;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends BaseForm
{
    public $account;
    public $password;
    public $is_remember = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
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

    public function validateAccount($attribute)
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addError($attribute, '没有该账号！');
            return;
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $user = $this->getUser();
            if (!$user || !Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
                $this->addError($attribute, '账号或密码错误！');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByAccount($this->account);
        }

        return $this->_user;
    }

    public function submit()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            //$result = Yii::$app->userService->login($user, $this->is_remember);//wuqiang
            $result = Yii::$app->user->login($user, $this->is_remember ? 14 * 24 * 3600 : 0);
            if ($result) {
                $time = time() + (14 * 24 * 3600);
                $usernameCookie = new Cookie();
                $usernameCookie->name = 'account';
                $usernameCookie->value = $this->account;
                $usernameCookie->expire = $time;

                $passwordCookie = new Cookie();
                $passwordCookie->name = 'account_token';
                $passwordCookie->value = $this->password;
                $passwordCookie->expire = $time;

                Yii::$app->response->cookies->add($usernameCookie);
                Yii::$app->response->cookies->add($passwordCookie);

            }
            return $result;
        } else {
            return false;
        }
    }
}
