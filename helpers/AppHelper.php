<?php

namespace app\helpers;

use yii\log\FileTarget;
use yii\web\Request;

class AppHelper
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

    /**
     * @return FileTarget
     */
    public static function getTarget()
    {
        if (static::$_target === false) {
            $target = new FileTarget();
            $target->dirMode = 0777;
            $target->fileMode = 0777;
            static::$_target = $target;
        }

        return static::$_target;
    }

    protected static $_log_id = false;

    public static function getLogId()
    {
        if (static::$_log_id === false) {
            static::$_log_id = uniqid();
        }
        return static::$_log_id;
    }

    public static function log($category, $title, $log = '')
    {
        if (is_array($log)) {
            $log = json_encode($log, JSON_UNESCAPED_UNICODE);
        } elseif (is_object($log)) {
            $log = serialize($log);
        }

        $file = \Yii::getAlias('@runtime') . '/' . $category . '.log.' . date('Ymd');

        $id = \Yii::$app->userService->getId();

        $log = sprintf("[%s][%s][%s][%s] %s\n", date('Y-m-d H:i:s'), static::getLogId(), $id, $title, strval($log));

        $new = !file_exists($file);
        @file_put_contents($file, $log, FILE_APPEND);

        if ($new) {
            @chmod($file, 0777);
        }
    }

    public static function lock($key, $ttl = 5)
    {
        $result = \Yii::$app->redis->executeCommand('SETNX', [$key, time() + $ttl]);
        if ($result) {
            \Yii::$app->redis->executeCommand('EXPIRE', [$key, $ttl]);
        }
        return $result;
    }

    public static function unLock($key)
    {
        return \Yii::$app->redis->executeCommand('DEL', [$key]);
    }

    public static function checkLimit($key, $ttl = 3600, $step = 60)
    {
        $time = (int)\Yii::$app->redis->executeCommand('GET', [$key]);
        if ($time && (time() - $time < $ttl)) {
            $arr = [
                'prev' => date('Y-m-d H:i:s', $time),
                'now' => date('Y-m-d H:i:s', time()),
            ];
            // AppHelper::log('check-limit', $key, $arr);

            if ($step) {
                \Yii::$app->redis->executeCommand('SETEX', [$key, 2 * $ttl, $time + $step]);
            }

            if (YII_ENV_PROD) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    public static function setLimit($key, $ttl = 3600)
    {
        $ttl = 2 * $ttl;
        \Yii::$app->redis->executeCommand('SETEX', [$key, $ttl, time()]);
    }

    public static function writeArray($name, $array, $isBak = true)
    {
        $file = $name . '.php';
        $new = \Yii::getAlias('@runtime/' . $file);
        $bak = \Yii::getAlias('@runtime/' . time() . '_' . $file);
        if (file_exists($new) && $isBak) {
            copy($new, $bak);
        }

        $string = '<?php return ' . var_export($array, true) . ";";
        $fp = fopen($new, 'w+');
        fwrite($fp, $string);
        fclose($fp);
    }

    public static function readArray($name)
    {
        $file = \Yii::getAlias('@runtime/' . $name . '.php');
        if (!file_exists($file)) {
            return [];
        }
        return include \Yii::getAlias('@runtime/' . $name . '.php');
    }

    public static function getMobileProvince($mobile)
    {
        if ($mobile) {
            $url = sprintf('https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query=%s&resource_id=6004&ie=utf8&format=json', $mobile);
            try {
                \Yii::$app->httpClient->transport = 'yii\httpclient\CurlTransport';
                $content = \Yii::$app->httpClient->createRequest()
                    ->setUrl($url)
                    ->setMethod('GET')
                    ->setOptions(['timeout' => 1])
                    ->send()
                    ->getContent();

                if ($content && $content = iconv('gbk', 'utf-8', $content)) {
                    $json = json_decode($content, true);
                    if (is_array($json) && $json && isset($json['data']) && isset($json['data'][0])) {
                        $result = [
                            'province' => $json['data'][0]['prov'],
                            'city' => $json['data'][0]['city']
                        ];
                        if (empty($result['province'])) {
                            $result['province'] = $result['city'];
                            $result['city'] = '';
                        }
                        return $result;
                    }
                }
            } catch (\Exception $e) {
                AppHelper::log('get-mobile-info-error', $mobile, $e->getTraceAsString());
            }
        }

        return false;
    }

    public static $_cities = false;

    public static function getAreaInfo($areaId)
    {
        $map = [
            'province' => '',
            'city' => '',
            'area' => ''
        ];

        if (!$areaId || strlen($areaId) != 6) {
            return $map;
        }

        if (static::$_cities === false) {
            static::$_cities = include \Yii::getAlias('@app/data/cities.php');
        }

        $provinceId = substr($areaId, 0, 2) . '0000';

        if (isset(static::$_cities[$provinceId])) {
            $map['province'] = static::$_cities[$provinceId]['name'];
        } else {
            return $map;
        }

        $cityId = substr($areaId, 0, 4) . '00';
        if (isset(static::$_cities[$provinceId]['children'][$cityId])) {
            $map['city'] = static::$_cities[$provinceId]['children'][$cityId]['name'];
        } else {
            return $map;
        }

        if (isset(static::$_cities[$provinceId]['children'][$cityId]['children'][$areaId])) {
            $map['area'] = static::$_cities[$provinceId]['children'][$cityId]['children'][$areaId]['name'];
        }

        return $map;
    }

    public static function getAreaId($country = null, $province = null, $city = null)
    {
        if (static::$_cities === false) {
            static::$_cities = include \Yii::getAlias('@app/data/cities.php');
        }

        if ($country && $country != '中国') {
            return 440000;
        }

        $return = 0;
        $list = [];
        if ($province) {
            foreach (static::$_cities as $id => $item) {
                if (strpos($item['name'], $province) !== false) {
                    $return = $id;
                    $list = isset($item['children']) ? $item['children'] : [];
                    break;
                }
            }
        }

        if ($city && $list) {
            foreach ($list as $id => $item) {
                if (strpos($item['name'], $city) !== false) {
                    $return = $id;
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * 获取用户IP
     * @return array|null|string
     */
    public static function getUserIp()
    {
        if (\Yii::$app->request instanceof Request) {
            $headers = \Yii::$app->request->headers;
            $ip = $headers->get('X-real-ip');
            if (!$ip || $ip == null || strlen($ip) == 0 || strtolower($ip) == 'unknown') {
                $ip = $headers->get('x-forwarded-for');
            }

            if (!$ip || $ip == null || strlen($ip) == 0 || strtolower($ip) == 'unknown') {
                $ip = $headers->get('Proxy-Client-IP');
            }

            if (!$ip || $ip == null || strlen($ip) == 0 || strtolower($ip) == 'unknown') {
                $ip = $headers->get('WL-Proxy-Client-IP');
            }

            if ($ip) {
                return $ip;
            } else {
                return \Yii::$app->request->getUserIP();
            }
        } else {
            return '127.0.0.1';
        }
    }
}
