<?php
namespace app\module\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\Advert;

class AdvertController extends BaseController
{
    public function actionIndex()
    {
        $sort = Yii::$app->request->get('sort');
        $orderBy = $this->sort($sort);
        $query = Yii::$app->advertService->search()
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

    private function loadAdvert()
    {
        $id = Yii::$app->request->getQueryParam('id');
        if ($id) {
            $advert = Yii::$app->advertService->search(['id' => $id])->one();
            if ($advert) {
                return $advert;
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
        $advert = $this->loadAdvert();

        $result = Yii::$app->advertService->delete($advert);
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
        $advert = $this->loadAdvert();

        if (Yii::$app->request->isPost) {
            $attributes = Yii::$app->request->getBodyParam('Advert');
            $result = Yii::$app->advertService->save($advert, $attributes);
            if ($result) {
                $this->success('修改成功！');
                $this->goBack();
            } else {
                $this->error('修改失败！');
                $this->goBack();
            }
        }
        return $this->render('update', [
            'model' => $advert
        ]);
    }

    public function actionCreate()
    {
        $advert = new Advert();

        if (Yii::$app->request->isPost) {
            $attributes = Yii::$app->request->getBodyParam('Advert');
            $result = Yii::$app->advertService->save($advert, $attributes);
            if ($result) {
                $this->success('修改成功！');
                $this->goBack();
            } else {
                $this->error('修改失败！');
                $this->goBack();
            }
        }
        return $this->render('update', [
            'model' => $advert
        ]);
    }
}

?>
