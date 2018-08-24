<?php
use app\models\User;
use yii\helpers\Url;

return [
    'user' => [
        'name' => '用户',
        'icon' => 'users',
        'url' => Url::to(['user/index']),
        'children' => [
            'user' => [
                'name' => '用户信息',
                'icon' => 'users',
                'url' => Url::to(['user/index'])
            ]
        ]
    ]
];
