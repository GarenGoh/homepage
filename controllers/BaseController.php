<?php
namespace app\controllers;

use yii\web\Controller;
use Yii;

class BaseController extends Controller
{
    public function success($message)
    {
        Yii::$app->session->setFlash('app_success_flash_message', $message);
    }

    public function error($message)
    {
        Yii::$app->session->setFlash('app_error_flash_message', $message);
    }
}

?>
