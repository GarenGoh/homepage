<?php

use app\migrations\BaseMigration;

class m160425_062334_file extends BaseMigration
{
    public function up()
    {
        $this->createTable('file', [
            'id'         => 'int(10)      UNSIGNED NOT NULL AUTO_INCREMENT',
            'name'       => "varchar(200)          NOT NULL COMMENT '文件名'",
            'old_name'   => "varchar(200)          NOT NULL COMMENT '旧文件名'",
            'size'       => "varchar(200)          NOT NULL COMMENT '文件大小'",
            'type'       => "varchar(200)          NOT NULL COMMENT '文件类型'",
            'prefix'     => "varchar(200)              NULL COMMENT '路径前缀'",
            'PRIMARY KEY `id`(`id`)'
        ], "文件表");

        $this->create();
    }

    public function create() {
        $userAvatarIds = Yii::$app->params['defaultAvatarIds'];
        foreach($userAvatarIds as $k => $v) {
            $file = new \app\models\File();
            $file->id = $k;
            $file->name = $v;
            $file->old_name = $v;
            $file->size = '10';
            $file->type = 'image/jpeg';
            $file->prefix = 'user/';
            $file->save();
        }
    }
    public function down()
    {
        $this->dropTable('file');

        return true;
    }
}
