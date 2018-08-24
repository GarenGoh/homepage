<?php
namespace app\controllers;

use app\models\Article;
use Yii;

class ArticleController extends BaseController
{
    public function actionPhp()
    {
        return $this->render('index', [
            'category' => Article::CATEGORY_PHP
        ]);
    }

    public function actionLinux()
    {
        return $this->render('index', [
            'category' => Article::CATEGORY_LINUX
        ]);
    }

    public function actionDb()
    {
        return $this->render('index', [
            'category' => Article::CATEGORY_DB
        ]);
    }

    public function actionFrontend()
    {
        return $this->render('index', [
            'category' => Article::CATEGORY_FRONTEND
        ]);
    }

    public function actionLearn()
    {
        return $this->render('index', [
            'category' => Article::CATEGORY_LEARN
        ]);
    }

    public function actionHot_tag()
    {
        $tag = Yii::$app->request->get('tag');
        return $this->render('index', [
            'tag' => $tag
        ]);
    }

    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $article = Yii::$app->articleService->search(['id' => $id])->one();
        $article->read_count++;
        $article->save();
        return $this->render('view', [
            'model' => $article
        ]);
    }

    public function actionTest()
    {
        return $this->renderPartial('test');
    }
}

?>
