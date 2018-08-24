<?php
namespace app\migrations;

use yii\db\Migration;

abstract class BaseMigration extends Migration
{
    public function createTable($tableName,$content = [],$tableNotes = null)
    {
        if($tableName && $content) {
            $tableOptions = null;

            if ($this->db->driverName === 'mysql') {
                $tableOptions = "CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM COMMENT='{$tableNotes}'";
            }
            $tableName = "{{%$tableName}}";
            parent::createTable( $tableName,$content,$tableOptions);
        }
    }

    public function dropTable($tableName)
    {
        $tableName = "{{%$tableName}}";
        parent::dropTable($tableName);
        return true;
    }
}
