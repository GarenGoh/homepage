<?php

namespace app\components;

use Yii;
use app\models\User;
use \yii\web\User as BaseWebUser;

class WebUser extends BaseWebUser
{
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        /* @var $user \app\models\User */

        $user = $this->getIdentity();

        if (!$user) {
            return false;
        }

        if (Yii::$app->userService->isRoot($user->id)) {
            return true;
        }

        if (($permissionName == User::ROLE_MEMBER && $user->role_id == User::ROLE_MEMBER)
            || ($permissionName == User::ROLE_MANAGER && $user->role_id == User::ROLE_MANAGER)
        ) {
            return true;
        } else {
            return false;
        }
    }

}
