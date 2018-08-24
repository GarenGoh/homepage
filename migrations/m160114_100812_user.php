<?php

use app\migrations\BaseMigration;
use app\models\User;

class m160114_100812_user extends BaseMigration
{
    public function up()
    {
        $this->createTable('user', [
            'id'                => "int(10)         UNSIGNED NOT NULL AUTO_INCREMENT",
            'username'          => "varchar(50)              NOT NULL COMMENT '用户名'",
            'name'              => "varchar(50)                  NULL COMMENT '姓名'",
            'email'             => "varchar(50)              NOT NULL COMMENT '邮箱'",
            'is_email_enable'   => "tinyint(1)      UNSIGNED NOT NULL DEFAULT 0 COMMENT '邮箱可用'",
            'mobile'            => "varchar(11)                  NULL COMMENT '手机'",
            'is_mobile_enable'  => "tinyint(1)      UNSIGNED NOT NULL DEFAULT 0 COMMENT '手机可用'",
            'password_hash'     => "varchar(64)              NOT NULL COMMENT 'hash密码'",
            'role_id'           => "int(6)          UNSIGNED NOT NULL COMMENT '分类ID'",
            'avatar_id'         => "int(10)         UNSIGNED NOT NULL DEFAULT 0 COMMENT '头像ID'",
            'is_enable'         => "tinyint         UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否可用'",
            'created_at'        => "int(10)         UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间'",
            'logged_at'         => "int(10)         UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录时间'",
            'PRIMARY KEY `id`(`id`)'
        ], '用户表');
        $this->create();
    }

    public function down()
    {
        $this->dropTable('user');
        return true;
    }

    public function create()
    {
        $adminUser =
            [
                'role_id' => User::ROLE_MANAGER,
                'username' => 'admin',
                'name' => 'Admin',
                'password' => '1111',
                'email' => '11111111@qq.com',
                'is_enable' => User::BOOLEAN_YES,
            ];

            $user = new User();
            $result = Yii::$app->userService->save($user, $adminUser);
            if (!$result) {
                var_dump($user->getErrors());
            }
    }
}
