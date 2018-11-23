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

    public function wxRegister($message, $email)
    {

    }
}

?>
