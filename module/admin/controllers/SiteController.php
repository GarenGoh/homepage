<?php

namespace app\module\admin\controllers;

use Yii;
use app\module\admin\forms\LoginForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class SiteController extends BaseController
{
    public $layout = 'main';

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [ //合并父类中的behaviors()
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => false,
                        'roles' => ['?']  //?代表游客
                    ],
                    [
                        'allow' => false,
                        'actions' => ['login'],
                        'roles' => ['@'],//@代表登录了的任意用户
                        'denyCallback' => function () {
                            $this->redirect(['site/index']);
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?']
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get']
                ]
            ],
        ]);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        if (Yii::$app->request->isPost) {
            $model->setAttributes(Yii::$app->request->post(), false);
            if ($model->login()) {
                $this->redirect(['site/index']);
            } else {
                Yii::$app->session->setFlash('error');
            }
        }
        return $this->renderPartial('login', [
            'model' => $model
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionFlush()
    {
        Yii::$app->cache->flush();
        $this->goBack();
    }
}
