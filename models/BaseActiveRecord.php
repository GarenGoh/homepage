<?php
namespace app\models;
use Yii;
use yii\db\ActiveRecord;

abstract class BaseActiveRecord extends ActiveRecord
{
    const BOOLEAN_YES = 1;
    const BOOLEAN_NO = 0;

    public function getId()
    {
        return $this->id;
    }

    public static function getBooleanMap($status = null)
    {
        $map = [
            static::BOOLEAN_YES => '是',
            static::BOOLEAN_NO => '否',
        ];
        return !is_null($status) && $map[$status] ? $map[$status] : $map;
    }

    /*
     * 找出字符串中所有的数字；
     * 返回一个由该数字组成的字符串
     */
    public static function findNum($str = '')
    {
        $str = trim($str);
        if (empty($str)) {
            return '';
        }
        $temp = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
        $result = '';
        for ($i = 0; $i < strlen($str); $i++) {
            if (in_array($str[$i], $temp)) {
                $result .= $str[$i];
            }
        }
        return $result;
    }

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

?>
