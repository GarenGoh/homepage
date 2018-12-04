<?php

namespace app\Behaviors;

use app\helpers\AppHelper;
use app\models\DailyInfo;
use app\models\User;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class UserBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert'
        ];
    }

    public function afterInsert($event)
    {
        /**
         * @var $user User
         */
        $user = $event->sender;
        if($user->open_id){
            $d_info = new DailyInfo();
            $d_info->email = $user->email;
            $d_info->user_id = $user->id;
            $d_info->created_at = time();
            if($d_info->save()){
                AppHelper::log('user', 'after_insert_daily_save_fail', $d_info->getFirstError());
            }
        }
    }

    public function beforeUpdate($event)
    {

        /**
         * @var $user User
         */
        $user = $event->sender;
        $oldAttributes = $user->getOldAttributes();


        if($oldAttributes['open_id'] != $user->open_id){
            $d_info = \Yii::$app->dailyService->search(['user_id' => $user->id])
                ->limit(1)
                ->one();
            if(!$d_info){
                $d_info = new DailyInfo();
            }

            $d_info->email = $user->email;
            $d_info->user_id = $user->id;
            $d_info->created_at = time();
            if($d_info->save()){
                AppHelper::log('user', 'before_update_daily_save_fail', $d_info->getFirstError());
            }
        }
    }
}
