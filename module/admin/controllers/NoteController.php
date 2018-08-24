<?php
namespace app\module\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\Note;

class NoteController extends BaseController
{
    public function actionIndex()
    {
        $sort = Yii::$app->request->get('sort');
        $orderBy = $this->sort($sort);
        $query = Yii::$app->noteService->search()
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

    private function loadNote()
    {
        $id = Yii::$app->request->getQueryParam('id');
        if ($id) {
            $note = Yii::$app->noteService->search(['id' => $id])->one();
            if ($note) {
                return $note;
            } else {
                $this->error('没有该便签');
                $this->goBack();
            }
        } else {
            $this->error('缺少参数：id');
            $this->goBack();
        }
    }

    public function actionDelete()
    {
        $note = $this->loadNote();

        $result = Yii::$app->noteService->delete($note);
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
        $note = $this->loadNote();

        if (Yii::$app->request->isPost) {
            $attributes = Yii::$app->request->getBodyParam('Note');
            $result = Yii::$app->noteService->save($note, $attributes);
            if ($result) {
                $this->success('修改成功！');
                $this->goBack();
            } else {
                $this->error('修改失败！');
                $this->goBack();
            }
        }
        return $this->render('update', [
            'model' => $note
        ]);
    }

    public function actionCreate()
    {
        $note = new Note();

        if (Yii::$app->request->isPost) {
            $attributes = Yii::$app->request->getBodyParam('Note');
            $result = Yii::$app->noteService->save($note, $attributes);
            if ($result) {
                $this->success('修改成功！');
                $this->goBack();
            } else {
                $this->error('修改失败！');
                $this->goBack();
            }
        }
        return $this->render('update', [
            'model' => $note
        ]);
    }
}

?>
