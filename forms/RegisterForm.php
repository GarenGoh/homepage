<?php
namespace app\forms;

use app\models\User;
use Yii;

class RegisterForm extends BaseForm
{
    public $email;
    public $username;
    public $password;
    public $rePassword;
    public $is_agree;

    public function rules()
    {
        $requireFields = ['email', 'password', 'username', 'rePassword'];

        $rules = [
            [$requireFields, 'required'],
            [$requireFields, 'filter', 'filter' => 'trim'],
            [$requireFields, 'filter', 'filter' => 'strip_tags'],
            ['password', 'compare', 'compareAttribute' => 'rePassword', 'message' => '两次输入的密码不一致！'],
            ['is_agree', 'required', 'message' => '请阅读注册协议，同意协议后方可注册。'],
        ];

        return $rules;
    }

    public function submit()
    {
        if ($this->validate()) {
            $user = new User();
            $attributes = $this->getAttributes();
            $attributes['role_id'] = User::ROLE_MEMBER;
            $attributes['created_at'] = time();
            $attributes['is_enable'] = User::BOOLEAN_YES;

            if (!Yii::$app->userService->save($user, $attributes)) {
                $this->addErrors($user->getErrors());
                return false;
            } else {
                Yii::$app->userService->login($user, true);
                return true;
            }
        } else {
            return false;
        }
    }
}

?>
