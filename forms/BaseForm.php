<?php
namespace app\forms;

use yii\base\Model;

abstract class BaseForm extends Model
{
    public function getFirstError($attribute = null)
    {
        if ($attribute) {
            return parent::getFirstError($attribute);
        } else {
            $errors = $this->getErrors();
            $errors = array_shift($errors);
            return array_shift($errors);
        }
    }
}
