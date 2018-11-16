<?php
namespace app\controllers;

use app\helpers\AppHelper;
use app\helpers\ArrayHelper;
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

        $message = Yii::$app->wxService->getMessage();
        $toUserName = ArrayHelper::getValue($message, 'ToUserName');
        $fromUserName = ArrayHelper::getValue($message, 'FromUserName');
        $type = ArrayHelper::getValue($message, 'MsgType');
        $contents = '大家好!';
        $time = time();

        $replay = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
        $result = sprintf($replay, $toUserName, $fromUserName, $time, $type, $contents);
        echo $result;
        AppHelper::log('test', '$result', $result);

        exit;
    }


}

?>
