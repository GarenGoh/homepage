<?php

use app\migrations\BaseMigration;

class m160505_095448_advert extends BaseMigration
{
    public function up()
    {
        $this->createTable('advert', [
        'id'            => 'int(10)        UNSIGNED NOT NULL AUTO_INCREMENT',
        'position_id'   => "tinyint(4)     UNSIGNED NOT NULL COMMENT '位置ID'",
        'image_id'      => "int(10)        UNSIGNED NOT NULL DEFAULT 0 COMMENT '封面ID'",
        'title'         => "varchar(200)            NOT NULL COMMENT '标题'",
        'link'          => "varchar(255)                NULL COMMENT '链接'",
        'summary'       => "text                        NULL COMMENT '摘要'",
        'target'        => "varchar(20)                 NULL COMMENT '打开方式'",
        'created_at'    => "int(10)        UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间'",
        'weight'        => "int(10)        UNSIGNED NOT NULL DEFAULT 0 COMMENT '权重'",
        'is_enable'     => "tinyint(1)     UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否可用'",
        'PRIMARY KEY `id`(`id`)'
    ], '广告位表');
    }

    public function down()
    {
        $this->dropTable('advert');

        return true;
    }
}
