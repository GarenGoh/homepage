<?php
$config = [
    'id' => 'app',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone'=>'Asia/Shanghai',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'fuck you!',
            //'enableCsrfValidation' => false,  //是否开启Csrf验证,如果开启就需要在<head>中使用Html::csrfMetaTags(),否则会报错"您提交的数据无法被验证"
        ],
        'user' => [
            'class' => 'app\components\WebUser',//调用重写的can()
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',//设置404等错误页面。
        ],
            /*
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],*/
    ],
    /*
    'params' => $params,*/
];
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}
$config = yii\helpers\ArrayHelper::merge(
    $config,
    require(__DIR__ . '/common.php'),
    require(__DIR__ . '/config.php')
);
return $config;
