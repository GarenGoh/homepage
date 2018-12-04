<?php

class m181204_073249_daily_info extends \app\migrations\BaseMigration
{
    public function up()
    {
        $this->createTable('daily_info', [
            'id'                => "int(10)         UNSIGNED NOT NULL AUTO_INCREMENT",
            'user_id'           => "int(10)         UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID'",
            'email'             => "varchar(50)              NOT NULL COMMENT '邮箱'",
            'email_password'    => "varchar(50)              NOT NULL COMMENT '邮箱密码'",
            'created_at'        => "int(10)         UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间'",
            'PRIMARY KEY `id`(`id`)',
            'UNIQUE KEY `user_id`(`user_id`)'
        ], '日报信息表');
    }

    public function down()
    {
        $this->dropTable('daily_info');
        return true;
    }
}
