<?php

namespace app\controllers;

use app\helpers\AppHelper;
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

        $articles = $query->offset($pageSize * $page)
            ->orderBy(['id' => SORT_DESC])
            ->limit($pagination->limit)
            ->all();
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'is_end' => $totalCount <= ($page * $pageSize) ? true : false,
            'html' => $this->renderPartial('_index-article', ['articles' => $articles, 'is_first_page' => 0]),
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
        $url = "http://www.runoob.com/?s=php++函数&page=1";

        $request = Yii::$app->httpClient->createRequest()
            ->setOptions(['timeout' => 5])
            //->setContent(json_encode($post, JSON_UNESCAPED_UNICODE))
            ->setUrl($url)
            //->setHeaders(['Content-type' => 'application/json'])
            ->setMethod('GET');

        $content = Yii::$app->httpClient->send($request);

        preg_match_all("/h2.+(http.+?\.html)[\s\S]{1,50}PHP\<\/em\>\ (.+?\(\))\<em\>函数/", $content->content, $data);

        $arr = array_combine($data[2], $data[1]);
        foreach ($arr as $key => $value) {
            $data = [];
            $data['name'] = $key;

            $request = Yii::$app->httpClient->createRequest()
                ->setOptions(['timeout' => 5])
                ->setUrl($value)
                ->setMethod('GET');
            $content2 = Yii::$app->httpClient->send($request);
            preg_match_all("/h2.+(http.+?\.html)[\s\S]{1,50}PHP\<\/em\>\ (.+?\(\))\<em\>函数/", $content2->content, $data2);

            $str = str_replace('>', '&gt', $content2->content);
            $str = str_replace('<', '&lt', $str);
            echo '<pre>';print_r(
                $value
            );echo '</pre>';

            echo '<pre>';
            print_r(
                $str
            );
            echo '</pre>';
            exit;
            $data['name'] = $key;
        }

        $str = str_replace('>', '&gt', $data[0]);
        $str = str_replace('<', '&lt', $str);
        echo '<pre>';
        print_r(
            $str
        );
        echo '</pre>';
        exit;

    }
}
