<?php
namespace app\controllers;

use Yii;

class SearchController extends BaseController
{
    public function actionIndex()
    {
        $keyword = Yii::$app->request->get('keyword');
        return $this->render('index', ['keyword' => $keyword]);
    }
}

?>