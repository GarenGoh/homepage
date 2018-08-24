<?php
namespace app\controllers;

use Yii;

class NoteController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $note = Yii::$app->noteService->search(['id' => $id])->one();
        return $this->render('view', [
            'model' => $note
        ]);
    }
}

?>
