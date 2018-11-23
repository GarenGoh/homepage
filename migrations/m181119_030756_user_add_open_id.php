<?php

use yii\db\Migration;

class m181119_030756_user_add_open_id extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'open_id', 'varchar(40) comment "openID" after email');
        $this->createIndex('open_id', 'user', 'open_id', true);
    }

    public function down()
    {
        $this->dropColumn('user', 'open_id');

        return true;
    }
}
