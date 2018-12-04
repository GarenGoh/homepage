<?php

namespace app\controllers;

use Yii;
use app\forms\LoginForm;
use yii\data\Pagination;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\forms\RegisterForm;

class SiteController extends BaseController
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\PageCache',
                'only' => ['index'],
                'duration' => 24 * 3600,//0为永久
                'enabled' => YII_ENV_PROD,
                'dependency' => [
                    'class' => 'yii\caching\DbDependency',
                    'sql' => 'SELECT COUNT(*) FROM article'
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionArticle()
    {
        $page = Yii::$app->request->get('page', 0);
        $pageSize = 10;
        $query = Yii::$app->articleService->search();
        $totalCount = $query->count();
        $pagination = new Pagination([
            'totalCount' => $totalCount,
        ]);
        $pagination->setPageSize($pageSize);
        $pagination->setPage($page);

        $articles = $query->offset($pageSize*$page)
            ->orderBy(['id' => SORT_DESC])
            ->limit($pagination->limit)
            ->all();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'is_end' => $totalCount<=($page*$pageSize)?true:false,
            'html' => $this->renderPartial('_index-article',['articles' => $articles, 'is_first_page' => 0]),
            'page' => $page
        ];
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        if (Yii::$app->request->isPost) {
            $attributes = Yii::$app->request->getBodyParams();
            $model->setAttributes($attributes);

            if ($model->submit()) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['status' => true];
                } else {
                    $this->success('帐号注册成功！');
                    return $this->goBack();
                }
            } else {
                $message = $model->getFirstError();
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->statusCode = 400;
                    return ['status' => false, 'message' => $message];
                }
                $this->error($message);
            }
        }
        print_r($model);
        exit;
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if (Yii::$app->request->isPost) {
            $model->setAttributes(Yii::$app->request->post());
            if ($model->submit()) {
                return $this->goBack();
            } else {
                $message = $model->getFirstError();
                $this->error($message);
            }
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionD()
    {
        $mail = Yii::$app->mailer->compose();
        $mail->setTo('qiang.wu@alpha-car.cn');    //接收人邮箱
        $mail->setSubject("主体2");    //邮件标题
        $mail->setHtmlBody("发送内容发送内容发送内容");    //发送内容(可写HTML代码)
        if ($mail->send()) {
            echo "成功";
        } else {
            echo "失败";
        }
    }
}
