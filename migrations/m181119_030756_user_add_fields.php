<?php

use yii\db\Migration;

class m181119_030756_user_add_fields extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'open_id', 'varchar(40) comment "openID" after email');
        $this->addColumn('user', 'daily_type', 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "日报状态" after is_enable');
        $this->createIndex('open_id', 'user', 'open_id', true);
    }

    public function down()
    {
        $this->dropColumn('user', 'open_id');
        $this->dropColumn('user', 'daily_type');

        return true;
    }
}
