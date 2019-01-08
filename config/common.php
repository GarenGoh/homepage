<?php
Yii::setAlias('@frontend', dirname(__DIR__) . '/frontend');
Yii::setAlias('@admin', dirname(__DIR__) . '/module/admin');
define('ADMIN_NAME', 'admin');

$config = [
    'language' => 'zh-CN',
    'modules' => [
        /*'test' => [
            'class' => 'app\test\Module'
        ],*/
        ADMIN_NAME => [
            'class' => 'app\module\admin\Admin',
        ],
        'api' => [
            'class' => 'app\module\api\Api',
        ],
    ],
    'params' => require(__DIR__ . '/params.php'),
    'components' => [
        /*
         * 语言转换组件
         * 默认@app，@yii可以直接使用
         * 消息所在路径格式参考@yii：vendor/yiisoft/yii2/messages（如：@app/messages……）
         *通过函数：Yii::t()转换，如Yii::t('admin','Update',[],'zh-CN')
         * 参考@yii：vendor/yiisoft/yii2/i18n
         */
        'i18n' => [
            'translations' => [
                'cn*' => [  //添加一个admin类，一般默认的@app就能满足要求了。此配置只为学习,
                    'class' => 'yii\i18n\PhpMessageSource',//必需
                    'sourceLanguage' => 'en-US',  //原始语言
                    'basePath' => '@admin/messages',  //路径
                    'fileMap' => [  //定义指向的文件，对应上面的‘cn*’，所以下面的键都要以cn开头
                        'cn1' => 'cn1.php',
                        'cn2' => 'cn2.php',
                        'cn3' => 'admin.php',
                        'cn4' => 'cnvi.php',
                    ]
                ],
                'test' => [
                    'class' => 'yii\i18n\PhpMessageSource',//必需
                    'sourceLanguage' => 'en-US',  //原始语言
                ]
            ],
        ],
        'mailers' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,//这句一定有，false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.exmail.qq.com',//每种邮箱的host配置不一样
                'username' => '',
                'password' => '',
                'port' => '465',
                'encryption' => 'ssl',
            ]
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,  //false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
            'transport' => [
//                'class' => 'Swift_SmtpTransport',
//                'host' => 'smtp.exmail.qq.com',  //每种邮箱的host配置不一样
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com',  //每种邮箱的host配置不一样
                'port' => '465',
                'encryption' => 'ssl',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'httpClient' => [
            'class' => 'yii\httpclient\Client'
        ],
        'snowflake' => [
            'class' => 'garengoh\snowflake\Snowflake',
            'epoch_offset' => 1514736000000,   //2018-01-01
        ],
        'userService' => [
            'class' => 'app\components\UserService'
        ],
        'articleService' => [
            'class' => 'app\components\ArticleService'
        ],
        'fileService' => [
            'class' => 'app\components\FileService'
        ],
        'advertService' => [
            'class' => 'app\components\AdvertService'
        ],
        'noteService' => [
            'class' => 'app\components\NoteService'
        ],
        'lunarService' => [
            'class' => 'app\components\LunarService'
        ],
        'wxService' => [
            'class' => 'app\components\WxService'
        ],
        'dailyService' => [
            'class' => 'app\components\DailyService',
        ],
    ]
];

return $config;
?>
