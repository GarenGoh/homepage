<?php
namespace app\module\admin\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

abstract class BaseController extends Controller
{
    protected function success($message)
    {
        Yii::$app->session->setFlash('admin_success_flash_message', $message);
    }

    protected function error($message)
    {
        Yii::$app->session->setFlash('admin_error_flash_message', $message);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_MANAGER]
                    ]
                ],
            ]
        ];
    }

    public function goBack($defaultUrl = null)
    {
        $returnUrl = Yii::$app->request->get('return_url', null);
        if ($returnUrl) {
            $this->redirect($returnUrl);
        } elseif (Yii::$app->request->getReferrer()) {
            $this->redirect(Yii::$app->request->getReferrer());
        } else {
            $this->redirect("/" . ADMIN_NAME);
        }
        Yii::$app->end();
    }

    public function sort($sort, $desc = 'id')
    {
        $fieldSort = [];
        if ($sort) {
            $start = substr($sort, 0, 1);
            if ($start == '-') {
                $sortsc = SORT_DESC;
                $field = substr($sort, 1);
            } else {
                $sortsc = SORT_ASC;
                $field = $sort;
            }
            $fieldSort = [$field => $sortsc];
        }
        $idSort = [$desc => SORT_DESC];

        return array_merge($fieldSort, $idSort);
    }
}
