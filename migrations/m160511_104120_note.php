<?php

use app\migrations\BaseMigration;

class m160511_104120_note extends BaseMigration
{
    public function up()
    {
        $this->createTable('note', [
            'id'            => 'int(10)        UNSIGNED NOT NULL AUTO_INCREMENT',
            'title'         => "varchar(200)            NOT NULL COMMENT '标题'",
            'content'       => "text                        NULL COMMENT '内容'",
            'keywords'      => "varchar(255)                NULL COMMENT '关键词'",
            'created_at'    => "int(10)        UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间'",
            'weight'        => "int(10)        UNSIGNED NOT NULL DEFAULT 0 COMMENT '权重'",
            'is_enable'     => "tinyint(1)     UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否可用'",
            'PRIMARY KEY `id`(`id`)'
        ], '便签表');
    }

    public function down()
    {
        $this->dropTable('note');

        return true;
    }
}
