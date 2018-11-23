<?php
namespace app\controllers;

use app\helpers\AppHelper;
use app\helpers\ArrayHelper;
use app\helpers\SnowFlake;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class WxController extends BaseController
{
    public function behaviors()
    {
        $rules[] = ['allow' => true];

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => array_merge($rules, [
                    [
                        'allow' => true,
                        'actions' => ['options']
                    ],
                    [
                        'allow' => false,
                        'denyCallback' => function () {
                            if (\Yii::$app->user->getId()) {
                                throw new ForbiddenHttpException('您无此操作的权限');
                            } else {
                                throw new UnauthorizedHttpException('请登录后操作');
                            }
                        }
                    ]
                ])
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ]
            ]
        ];
    }

    public function init()
    {
        $this->enableCsrfValidation = false;
    }

    public function actionEvent()
    {
        $get = Yii::$app->request->getQueryParams();
        $post = Yii::$app->request->getRawBody();
        AppHelper::log('test', '$get', $get);
        AppHelper::log('test', '$post', $post);


        //return -1 ^ (-1 << 41);

        $sf = new SnowFlake();
        return $sf->generateID();


        $message = Yii::$app->wxService->getMessage();
        $content = ArrayHelper::getValue($message, 'Content');
        $content = "我的邮箱:1382342@qq.com";

        $len = mb_strpos($content, ':');
        if(!$len){
            $len = mb_strpos($content, '：');
        }

        if($len && $len<= 6){
            $str = mb_substr($content, 0, $len);
            if($str == "我的邮箱"){
                $email = mb_substr($content, $len+1);
                Yii::$app->userService->wxRegister($message, $email);
            }
        }


    }


}

?>
