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
        $post = Yii::$app->request->getBodyParams();
        AppHelper::log('test', '$get', $get);
        AppHelper::log('test', '$post', $post);

        $openid = $get['openid'] ?? '';
        $time = time();
        $str =
            "<xml>
                <ToUserName>< ![CDATA[wq188226814] ]></ToUserName>
                <FromUserName>< ![CDATA[$openid] ]></FromUserName>
                <CreateTime>{$time}</CreateTime>
                <MsgType>< ![CDATA[text] ]></MsgType>
                <Content>< ![CDATA[大家好] ]></Content>
                <MsgId>1234567890123456</MsgId>
            </xml>";

        return $str;
    }


}

?>
