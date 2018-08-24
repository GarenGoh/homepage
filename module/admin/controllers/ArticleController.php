<?php
namespace app\module\admin\controllers;

use app\models\Article;
use Yii;
use yii\data\ActiveDataProvider;

class ArticleController extends BaseController
{
    public function actionIndex()
    {
        $sort = Yii::$app->request->get('sort');
        $orderBy = $this->sort($sort);
        $query = Yii::$app->articleService->search()
            ->orderBy($orderBy);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function loadArticle()
    {
        $id = Yii::$app->request->getQueryParam('id');
        if ($id) {
            $article = Yii::$app->articleService->search(['id' => $id])->one();
            if ($article) {
                return $article;
            } else {
                $this->error('没有该文章');
                $this->goBack();
            }
        } else {
            $this->error('缺少参数：id');
            $this->goBack();
        }
    }

    public function actionDelete()
    {
        $article = $this->loadArticle();

        $result = Yii::$app->articleService->delete($article);
        if ($result) {
            $this->success('删除成功！');
            $this->goBack();
        } else {
            $this->error('删除失败！');
            $this->goBack();
        }
    }

    public function actionUpdate()
    {
        $article = $this->loadArticle();

        if (Yii::$app->request->isPost) {
            $attributes = Yii::$app->request->getBodyParam('Article');
            $result = Yii::$app->articleService->save($article, $attributes);
            if ($result) {
                $this->success('修改成功！');
                $this->goBack();
            } else {
                $this->error('修改失败！');
                $this->goBack();
            }
        }
        return $this->render('update', [
            'model' => $article
        ]);
    }

    public function actionCreate()
    {
        $article = new Article();

        if (Yii::$app->request->isPost) {
            $attributes = Yii::$app->request->getBodyParam('Article');
            $result = Yii::$app->articleService->save($article, $attributes);
            if ($result) {
                $this->success('修改成功！');
                $this->goBack();
            } else {
                $this->error('修改失败！');
                $this->goBack();
            }
        }
        return $this->render('update', [
            'model' => $article
        ]);
    }
}

?>
