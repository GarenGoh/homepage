<?php

use app\migrations\BaseMigration;

class m160413_074341_article extends BaseMigration
{
    public function up()
    {
        $this->createTable('article', [
            'id'            => 'int(10)        UNSIGNED NOT NULL AUTO_INCREMENT',
            'image_id'      => "int(10)        UNSIGNED NOT NULL DEFAULT 0 COMMENT '封面ID'",
            'category'      => "text                        NULL COMMENT '分类'",
            'title'         => "varchar(200)            NOT NULL COMMENT '标题'",
            'keywords'      => "varchar(255)                NULL COMMENT '关键词'",
            'source'        => "varchar(255)                NULL COMMENT '来源'",
            'summary'       => "text                        NULL COMMENT '摘要'",
            'content'       => "text                        NULL COMMENT '内容'",
            'read_count'    => "int(10)        UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读次数'",
            'creator_id'    => "int(10)        UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户ID'",
            'created_at'    => "int(10)        UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间'",
            'is_hot'        => "tinyint(1)     UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否热点'",
            'is_enable'     => "tinyint(1)     UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否可用'",
            'PRIMARY KEY `id`(`id`)'
        ], '文章表');
    }

    public function down()
    {
        $this->dropTable('article');

        return true;
    }

}
