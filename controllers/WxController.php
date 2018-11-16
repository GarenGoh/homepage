<?php
namespace app\controllers;

use app\helpers\AppHelper;
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
                        'denyCallback' => function() {
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

    public function init(){
        $this->enableCsrfValidation = false;
    }

    public function actionEvent()
    {
        Yii::$app->request->enableCsrfValidation = false;
        $get = Yii::$app->request->getQueryParams();
        $post = Yii::$app->request->getBodyParams();

        Yii::$app->response->format = Response::FORMAT_JSON;
        $signature = $get['signature'] ?? '';
        $echostr = $get['echostr'] ?? '';
        $timestamp = $get['timestamp'] ?? '';
        $nonce = $get['nonce'] ?? '';
        $token = '9292';

        AppHelper::log('test', '$get', $get);
        AppHelper::log('test', '$post', $post);


        return '你好!';
    }
}

?>
