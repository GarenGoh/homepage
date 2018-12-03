<?php
namespace app\components;

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
        $id = Yii::$app->user->getId();
        return $id ? $id : 0;
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
        if($old_user){
            if($old_user->email != $email){
                $old_user->email = $email;
                if($old_user->save()){
                    return '更新邮箱成功!';
                }else{
                    return $old_user->getFirstError();
                }
            }else{
                return '与之前的邮箱相同,不需要修改!';
            }
        }else {
            $user = new User();
            $user->email = $email;
            $user->open_id = $open_id;
            if ($user->save()) {
                return '首次设置邮箱成功!';
            } else {
                return $user->getFirstError();
            }
        }
    }
}

?>
