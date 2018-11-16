<?php
namespace app\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    public static function convertXmlToArray($xml)
    {
        if (!is_object($xml)) {
            $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        $result = (array)$xml;
        foreach ($result as $key => $value) {
            if (is_object($value)) {
                $result[$key] = static::convertXmlToArray($value);
            }
        }
        return $result;
    }

    protected static $_target = false;
}
