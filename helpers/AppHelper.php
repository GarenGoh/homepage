<?php

namespace app\helpers;

use yii\log\FileTarget;
use yii\web\Request;

class AppHelper
{
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

    public static function getFakeUserAgent()
    {
        $userAgents = [
            'Mozilla/5.0 (Linux; Android 11; Mi 10 Pro Build/RKQ1.200710.002; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/83.0.4103.106 Mobile Safari/537.36 MMWEBID/6956 MicroMessenger/7.0.18.1740(0x27001235) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; MI 9 Build/QKQ1.190825.002; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/5570 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 8.1.0; MI PLAY Build/O11019; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/1543 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; VCE-AL00 Build/HUAWEIVCE-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/7286 MicroMessenger/7.0.18.1740(0x27001235) Process/tools WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; ELE-AL00 Build/HUAWEIELE-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/7742 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f2a) NetType/4G Language/zh_CN', 'Mozilla/5.0 (Linux; Android 9; Redmi Note 8 Pro Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/5947 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.14(0x17000e2e) NetType/4G Language/zh_CN', 'Mozilla/5.0 (Linux; Android 10; LYA-AL00 Build/HUAWEILYA-AL00L; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/9500 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f2a) NetType/WIFI Language/zh_CN', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f2a) NetType/4G Language/zh_CN', 'Mozilla/5.0 (Linux; Android 10; LIO-AN00 Build/HUAWEILIO-AN00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/3740 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f29) NetType/WIFI Language/zh_CN', 'Mozilla/5.0 (Linux; Android 10; Mi9 Pro 5G Build/QKQ1.190825.002; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/8957 MicroMessenger/7.0.18.1740(0x27001265) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; ELE-AL00 Build/HUAWEIELE-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/6769 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; EML-AL00 Build/HUAWEIEML-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/3848 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f2a) NetType/4G Language/zh_CN', 'Mozilla/5.0 (Linux; Android 10; COL-AL10 Build/HUAWEICOL-AL10; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/3642 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; TAS-AN00 Build/HUAWEITAS-AN00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/4294 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; LYA-AL00 Build/HUAWEILYA-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/5273 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; LYA-AL00 Build/HUAWEILYA-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/9587 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; SPN-AL00 Build/HUAWEISPN-AL0002; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/9265 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; TAS-AN00 Build/HUAWEITAS-AN00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/178 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 9; vivo X21UD Build/PKQ1.180819.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/3556 MicroMessenger/7.0.17.1720(0x2700113F) Process/tools WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 9; EML-AL00 Build/HUAWEIEML-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045317 Mobile Safari/537.36 MMWEBID/2446 MicroMessenger/7.0.17.1720(0x2700113F) Process/tools WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; EML-AL00 Build/HUAWEIEML-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/6037 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; ELE-AL00 Build/HUAWEIELE-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/394 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 9; ELE-AL00 Build/HUAWEIELE-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2580 MMWEBSDK/200601 Mobile Safari/537.36 MMWEBID/654 MicroMessenger/7.0.16.1700(0x2700103E) Process/toolsmp WeChat/arm32 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 9; CLT-AL00 Build/HUAWEICLT-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/6774 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; LIO-AN00 Build/HUAWEILIO-AN00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/980 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_4 like Mac OS X) AppleWebKit/603.3.8 (KHTML, like Gecko) Mobile/14G61 MicroMessenger/7.0.2(0x17000222) NetType/WIFI Language/zh_CN', 'Mozilla/5.0 (Linux; Android 10; CDY-AN00 Build/HUAWEICDY-AN00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2580 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/2612 MicroMessenger/7.0.18.1740(0x27001236) Process/toolsmp WeChat/arm32 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.12(0x17000c33) NetType/WIFI Language/zh_CN', 'Mozilla/5.0 (Linux; Android 10; LYA-AL00 Build/HUAWEILYA-AL00L; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/5884 MicroMessenger/7.0.18.1740(0x2700128D) Process/tools WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_1_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f2a) NetType/4G Language/zh_CN', 'Mozilla/5.0 (Linux; Android 9; PACM00 Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045330 Mobile Safari/537.36 MMWEBID/9272 MicroMessenger/7.0.16.1700(0x2700103E) Process/tools WeChat/arm32 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; ELS-AN10 Build/HUAWEIELS-AN10; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/3293 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; ELS-AN00 Build/HUAWEIELS-AN00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/6955 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.14(0x17000e2e) NetType/WIFI Language/zh_CN', 'Mozilla/5.0 (Linux; Android 10; MI 8 Build/QKQ1.190828.002; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/6558 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 10; EVR-AN00 Build/HUAWEIEVR-AN00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045329 Mobile Safari/537.36 MMWEBID/7979 MicroMessenger/7.0.18.1740(0x27001237) Process/tools WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_1_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f2a) NetType/4G Language/zh_CN', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f2a) NetType/2G Language/zh_CN', 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f2a) NetType/WIFI Language/zh_CN', 'Mozilla/5.0 (Linux; Android 10; CLT-AL00 Build/HUAWEICLT-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/4326 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E302 MicroMessenger/7.0.15(0x17000f2a) NetType/4G Language/zh_CN', 'Mozilla/5.0 (Linux; Android 9; PACM00 Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045330 Mobile Safari/537.36 MMWEBID/7057 MicroMessenger/7.0.16.1700(0x2700103E) Process/tools WeChat/arm32 NetType/WIFI Language/zh_CN ABI/arm64', 'Mozilla/5.0 (Linux; Android 9; PAAM00 Build/PKQ1.190414.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/9756 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/4G Language/zh_CN ABI/arm64', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/7.0.15(0x17000f2a) NetType/WIFI Language/zh_CN', 'Mozilla/5.0 (Linux; Android 10; M2007J1SC Build/QKQ1.200419.002; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.62 XWEB/2581 MMWEBSDK/200801 Mobile Safari/537.36 MMWEBID/2470 MicroMessenger/7.0.18.1740(0x27001237) Process/toolsmp WeChat/arm64 NetType/WIFI Language/zh_CN ABI/arm64'
        ];

        return $userAgents[array_rand($userAgents)];
    }

    public static function getFakeIp($i = 0)
    {
        $i++;
        if ($i > 20) {
            return '123.54.' . rand(1, 255) . '.' . rand(1, 255);
        }

        $ips = file_get_contents(\Yii::getAlias("@app/data/ip.txt"));
        $ips = explode("\n", $ips);
        $ip = $ips[array_rand($ips)];
        $ip_nums = explode('.', $ip);

        if (count($ip_nums) != 4) {
            return self::getFakeIp($i);
        }
        $ip_str = '';
        foreach ($ip_nums as $ip_num){
            if(is_numeric($ip_num)){
                $ip_str .= $ip_num . '.';
            }elseif(strpos($ip_num, '/')){
                list($min, $max) = explode('/', $ip_num);
                $ip_str .= rand($min, $max) . '.';
            }else{
                return self::getFakeIp($i);
            }
        }

        return trim($ip_str, '.');
    }
}
