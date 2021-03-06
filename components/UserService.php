<?php
namespace app\components;

use app\models\DailyInfo;
use app\models\User;
use Yii;
use yii\base\Component;

class UserService extends Component
{
    public $rootIds = [];

    public function save(User $user, array $attributes = [])
    {
        if ($attributes) {
            if (isset($attributes['password'])) {
                $user->password = $attributes['password'];
            }
            $user->setAttributes($attributes, false);
        }
        if ($user->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function login(User $user, $isRemember = false)
    {

        $result = Yii::$app->user->login($user, $isRemember ? 14 * 24 * 3600 : 0);

        if ($result) {
            $user->logged_at = time();
            $user->save();
        }

        return $result;
    }

    /**
     * @param array $where
     * @return \yii\db\ActiveQuery
     */
    public function search($where = [])
    {
        $query = User::find();
        if (isset($where['id']) && $where['id']) {
            $query->andFilterWhere(['id' => $where['id']]);
        }

        return $query;
    }

    public function isRoot($userId)
    {
        if (in_array($userId, $this->rootIds)) {
            return true;
        }
    }

    public function delete(User $user)
    {
        return $user->delete();
    }

    public function getId()
    {
        if(Yii::$app->id != "app-console"){
            $id = Yii::$app->user->getId();

            return (int)$id;
        }

        return 0;
    }

    public function wxRegister($open_id, $email)
    {
        /**
         * @var $old_user User
         */
        $old_user = $this->search()
            ->andWhere(['open_id' => $open_id])
            ->limit(1)
            ->one();
        if ($old_user) {
            if ($old_user->email != $email) {
                $old_user->email = $email;
                if ($old_user->save()) {
                    return '更新邮箱成功!';
                } else {
                    return $old_user->getFirstError();
                }
            } else {
                return '与之前的邮箱相同,不需要修改!';
            }
        } else {
            $old_user = $this->search()
                ->andWhere(['email' => $email])
                ->limit(1)
                ->one();
            if ($old_user) {
                Yii::$app->redis->setex($open_id . '_user_id', 3600, $old_user->id);
                return '该邮箱已注册!请在一小时内回复【密码:123454321】绑定微信!';
            } else {
                $id = Yii::$app->snowflake->generateID();
                $user = new User();
                $user->email = $email;
                $user->open_id = $open_id;
                $user->username = $id;
                $user->password = $id;
                if ($user->save()) {
                    return '首次设置邮箱成功!';
                } else {
                    return $user->getFirstError();
                }
            }
        }
    }

    public function setEmailPassword($open_id, $e_password)
    {
        /**
         * @var $old_user User
         */
        $user = $this->search()
            ->andWhere(['open_id' => $open_id])
            ->limit(1)
            ->one();
        if ($user) {
            /**
             * @var $d_info DailyInfo
             */
            $d_info = Yii::$app->dailyService->search(['user_id' => $user->id])
                ->limit(1)
                ->one();
            $d_info->email_password = $e_password;
            if($d_info->save()){
                return "设置能够邮箱密码成功!";
            }else{
                return $d_info->getFirstError();
            }

        } else {
            return "你还没有设置邮箱!";
        }
    }

    public function validatePassword($open_id, $password)
    {
        $user_id = Yii::$app->redis->get($open_id . '_user_id');
        /**
         * @var $user User
         */
        $user = Yii::$app->userService->search()
            ->andWhere(['id' => $user_id])
            ->limit(1)
            ->one();
        if ($user && Yii::$app->security->validatePassword($password, $user->password_hash)) {
            $user->open_id = $open_id;
            if($user->save()){
                return '绑定成功!';
            }else{
                return $user->getFirstError();
            }
        }else{
            return '与设置邮箱的时间间隔太长或密码错误!';
        }
    }

    public function updateUser($open_id, array $params)
    {
        /**
         * @var $user User
         */
        $user = Yii::$app->userService->search()
            ->andWhere(['open_id' => $open_id])
            ->limit(1)
            ->one();
        if($user){
            $user->setAttributes($params, false);
            if($user->save()){
                return "修改成功!";
            }else{
                return $user->getFirstError();
            }
        }else{
            return "没有找到你的信息,你可能需要先设置邮箱。";
        }
    }
}

?>
