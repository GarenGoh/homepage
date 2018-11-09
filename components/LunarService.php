<?php
namespace app\components;
/*
 * 农历 节气 节日
 */
class LunarService
{
    var $MIN_YEAR = 1891;
    var $MAX_YEAR = 2100;
    var $lunarInfo = [
        [0, 2, 9, 21936], [6, 1, 30, 9656], [0, 2, 17, 9584], [0, 2, 6, 21168], [5, 1, 26, 43344], [0, 2, 13, 59728],
        [0, 2, 2, 27296], [3, 1, 22, 44368], [0, 2, 10, 43856], [8, 1, 30, 19304], [0, 2, 19, 19168], [0, 2, 8, 42352],
        [5, 1, 29, 21096], [0, 2, 16, 53856], [0, 2, 4, 55632], [4, 1, 25, 27304], [0, 2, 13, 22176], [0, 2, 2, 39632],
        [2, 1, 22, 19176], [0, 2, 10, 19168], [6, 1, 30, 42200], [0, 2, 18, 42192], [0, 2, 6, 53840], [5, 1, 26, 54568],
        [0, 2, 14, 46400], [0, 2, 3, 54944], [2, 1, 23, 38608], [0, 2, 11, 38320], [7, 2, 1, 18872], [0, 2, 20, 18800],
        [0, 2, 8, 42160], [5, 1, 28, 45656], [0, 2, 16, 27216], [0, 2, 5, 27968], [4, 1, 24, 44456], [0, 2, 13, 11104],
        [0, 2, 2, 38256], [2, 1, 23, 18808], [0, 2, 10, 18800], [6, 1, 30, 25776], [0, 2, 17, 54432], [0, 2, 6, 59984],
        [5, 1, 26, 27976], [0, 2, 14, 23248], [0, 2, 4, 11104], [3, 1, 24, 37744], [0, 2, 11, 37600], [7, 1, 31, 51560],
        [0, 2, 19, 51536], [0, 2, 8, 54432], [6, 1, 27, 55888], [0, 2, 15, 46416], [0, 2, 5, 22176], [4, 1, 25, 43736],
        [0, 2, 13, 9680], [0, 2, 2, 37584], [2, 1, 22, 51544], [0, 2, 10, 43344], [7, 1, 29, 46248], [0, 2, 17, 27808],
        [0, 2, 6, 46416], [5, 1, 27, 21928], [0, 2, 14, 19872], [0, 2, 3, 42416], [3, 1, 24, 21176], [0, 2, 12, 21168],
        [8, 1, 31, 43344], [0, 2, 18, 59728], [0, 2, 8, 27296], [6, 1, 28, 44368], [0, 2, 15, 43856], [0, 2, 5, 19296],
        [4, 1, 25, 42352], [0, 2, 13, 42352], [0, 2, 2, 21088], [3, 1, 21, 59696], [0, 2, 9, 55632], [7, 1, 30, 23208],
        [0, 2, 17, 22176], [0, 2, 6, 38608], [5, 1, 27, 19176], [0, 2, 15, 19152], [0, 2, 3, 42192], [4, 1, 23, 53864],
        [0, 2, 11, 53840], [8, 1, 31, 54568], [0, 2, 18, 46400], [0, 2, 7, 46752], [6, 1, 28, 38608], [0, 2, 16, 38320],
        [0, 2, 5, 18864], [4, 1, 25, 42168], [0, 2, 13, 42160], [10, 2, 2, 45656], [0, 2, 20, 27216], [0, 2, 9, 27968],
        [6, 1, 29, 44448], [0, 2, 17, 43872], [0, 2, 6, 38256], [5, 1, 27, 18808], [0, 2, 15, 18800], [0, 2, 4, 25776],
        [3, 1, 23, 27216], [0, 2, 10, 59984], [8, 1, 31, 27432], [0, 2, 19, 23232], [0, 2, 7, 43872], [5, 1, 28, 37736],
        [0, 2, 16, 37600], [0, 2, 5, 51552], [4, 1, 24, 54440], [0, 2, 12, 54432], [0, 2, 1, 55888], [2, 1, 22, 23208],
        [0, 2, 9, 22176], [7, 1, 29, 43736], [0, 2, 18, 9680], [0, 2, 7, 37584], [5, 1, 26, 51544], [0, 2, 14, 43344],
        [0, 2, 3, 46240], [4, 1, 23, 46416], [0, 2, 10, 44368], [9, 1, 31, 21928], [0, 2, 19, 19360], [0, 2, 8, 42416],
        [6, 1, 28, 21176], [0, 2, 16, 21168], [0, 2, 5, 43312], [4, 1, 25, 29864], [0, 2, 12, 27296], [0, 2, 1, 44368],
        [2, 1, 22, 19880], [0, 2, 10, 19296], [6, 1, 29, 42352], [0, 2, 17, 42208], [0, 2, 6, 53856], [5, 1, 26, 59696],
        [0, 2, 13, 54576], [0, 2, 3, 23200], [3, 1, 23, 27472], [0, 2, 11, 38608], [11, 1, 31, 19176], [0, 2, 19, 19152],
        [0, 2, 8, 42192], [6, 1, 28, 53848], [0, 2, 15, 53840], [0, 2, 4, 54560], [5, 1, 24, 55968], [0, 2, 12, 46496],
        [0, 2, 1, 22224], [2, 1, 22, 19160], [0, 2, 10, 18864], [7, 1, 30, 42168], [0, 2, 17, 42160], [0, 2, 6, 43600],
        [5, 1, 26, 46376], [0, 2, 14, 27936], [0, 2, 2, 44448], [3, 1, 23, 21936], [0, 2, 11, 37744], [8, 2, 1, 18808],
        [0, 2, 19, 18800], [0, 2, 8, 25776], [6, 1, 28, 27216], [0, 2, 15, 59984], [0, 2, 4, 27424], [4, 1, 24, 43872],
        [0, 2, 12, 43744], [0, 2, 2, 37600], [3, 1, 21, 51568], [0, 2, 9, 51552], [7, 1, 29, 54440], [0, 2, 17, 54432],
        [0, 2, 5, 55888], [5, 1, 26, 23208], [0, 2, 14, 22176], [0, 2, 3, 42704], [4, 1, 23, 21224], [0, 2, 11, 21200],
        [8, 1, 31, 43352], [0, 2, 19, 43344], [0, 2, 7, 46240], [6, 1, 27, 46416], [0, 2, 15, 44368], [0, 2, 5, 21920],
        [4, 1, 24, 42448], [0, 2, 12, 42416], [0, 2, 2, 21168], [3, 1, 22, 43320], [0, 2, 9, 26928], [7, 1, 29, 29336],
        [0, 2, 17, 27296], [0, 2, 6, 44368], [5, 1, 26, 19880], [0, 2, 14, 19296], [0, 2, 3, 42352], [4, 1, 24, 21104],
        [0, 2, 10, 53856], [8, 1, 30, 59696], [0, 2, 18, 54560], [0, 2, 7, 55968], [6, 1, 27, 27472], [0, 2, 15, 22224],
        [0, 2, 5, 19168], [4, 1, 25, 42216], [0, 2, 12, 42192], [0, 2, 1, 53584], [2, 1, 21, 55592], [0, 2, 9, 54560]
    ];

    /**
     * 将阳历转换为阴历
     * @param year integer 公历-年
     * @param month integer 公历-月
     * @param date integer 公历-日
     */
    function convertSolarToLunar($year, $month, $date)
    {
        if ($year < $this->MIN_YEAR) {
            return [];
        }

        if ($year == $this->MIN_YEAR && $month <= 2 && $date <= 9) {
            return [1891, '正月', '初一', '辛卯', 1, 1, '兔'];
        }
        $yearData = $this->lunarInfo[ $year - $this->MIN_YEAR ];

        //距离正月初一的天数
        $days = $this->getDaysBetweenSolar($year, $month, $date, $yearData[1], $yearData[2]);

        return $this->getLunarByBetween($year, $days);
    }

    function convertSolarMonthToLunar($year, $month)
    {
        $yearData = $this->lunarInfo[ $year - $this->MIN_YEAR ];
        if ($year == $this->MIN_YEAR && $month <= 2 && $date <= 9) return [1891, '正月', '初一', '辛卯', 1, 1, '兔'];
        $month_days_ary = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $dd = $month_days_ary[ $month ];
        if ($this->isLeapYear($year) && $month == 2) $dd++;
        $lunar_ary = [];
        for ($i = 1; $i < $dd; $i++) {
            $days = $this->getDaysBetweenSolar($year, $month, $i, $yearData[1], $yearData[2]);
            $array = $this->getLunarByBetween($year, $days);
            $array[] = $year . '-' . $month . '-' . $i;
            $lunar_ary[ $i ] = $array;
        }

        return $lunar_ary;
    }

    /**
     * 将阴历转换为阳历
     * @param year integer 阴历-年
     * @param month 阴历-月，闰月处理：例如如果当年闰五月，那么第二个五月就传六月，相当于阴历有13个月，只是有的时候第13个月的天数为0
     * @param date 阴历-日
     */
    function convertLunarToSolar($year, $month, $date)
    {
        $yearData = $this->lunarInfo[ $year - $this->MIN_YEAR ];
        $between = $this->getDaysBetweenLunar($year, $month, $date);
        $res = mktime(0, 0, 0, $yearData[1], $yearData[2], $year);
        $res = date('Y-m-d', $res + $between * 24 * 60 * 60);
        $day = explode('-', $res);
        $year = $day[0];
        $month = $day[1];
        $day = $day[2];

        return [$year, $month, $day];
    }

    /**
     * 判断是否是闰年
     * @param year integer
     */
    function isLeapYear($year)
    {
        return (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0));
    }

    /**
     * 获取干支纪年
     * @param year integer|string
     */
    function getLunarYearName($year)
    {
        $sky = ['庚', '辛', '壬', '癸', '甲', '乙', '丙', '丁', '戊', '己'];
        $earth = ['申', '酉', '戌', '亥', '子', '丑', '寅', '卯', '辰', '巳', '午', '未'];
        $year = (string)$year;

        return $sky[ $year{3} ] . $earth[ $year % 12 ];
    }

    /**
     * 根据阴历年获取生肖
     * @param year integer 阴历年
     */
    function getYearZodiac($year)
    {
        $zodiac = ['猴', '鸡', '狗', '猪', '鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊'];

        return $zodiac[ $year % 12 ];
    }

    /**
     * 获取阳历月份的天数
     * @param year integer 阳历-年
     * @param month 阳历-月
     */
    function getSolarMonthDays($year, $month)
    {
        $monthHash = [
            '1' => 31,
            '2' => $this->isLeapYear($year) ? 29 : 28,
            '3' => 31,
            '4' => 30,
            '5' => 31,
            '6' => 30,
            '7' => 31,
            '8' => 31,
            '9' => 30,
            '10' => 31,
            '11' => 30,
            '12' => 31
        ];

        return $monthHash["$month"];
    }

    /**
     * 获取阴历月份的天数
     * @param year integer 阴历-年
     * @param month 阴历-月，从一月开始
     */
    function getLunarMonthDays($year, $month)
    {
        $monthData = $this->getLunarMonths($year);

        return $monthData[ $month - 1 ];
    }

    /**
     * 获取阴历每月的天数的数组
     * @param year integer
     */
    function getLunarMonths($year)
    {
        $yearData = $this->lunarInfo[ $year - $this->MIN_YEAR ];
        $leapMonth = $yearData[0];
        $bit = decbin($yearData[3]);
        $bitArray = [];
        for ($i = 0; $i < strlen($bit); $i++) {
            $bitArray[ $i ] = substr($bit, $i, 1);
        }

        for ($k = 0, $klen = 16 - count($bitArray); $k < $klen; $k++) {
            array_unshift($bitArray, '0');
        }

        $bitArray = array_slice($bitArray, 0, ($leapMonth == 0 ? 12 : 13));

        for ($i = 0; $i < count($bitArray); $i++) {
            $bitArray[ $i ] = $bitArray[ $i ] + 29;
        }

        return $bitArray;
    }

    /**
     * 获取农历每年的天数
     * @param year integer 农历年份
     */
    function getLunarYearDays($year)
    {
        $monthArray = $this->getLunarYearMonths($year);
        $len = count($monthArray);
        return $monthArray[ $len - 1 ] == 0 ? $monthArray[ $len - 2 ] : $monthArray[ $len - 1 ];
    }

    /**
     * 某一年($year)阴历每月月底据该年正月初一多少天
     * @param $year integer
     * @return array
     */
    function getLunarYearMonths($year)
    {
        $monthData = $this->getLunarMonths($year);

        $res = [];
        $yearData = $this->lunarInfo[ $year - $this->MIN_YEAR ];
        $len = ($yearData[0] == 0 ? 12 : 13);
        for ($i = 0; $i < $len; $i++) {
            $temp = 0;
            for ($j = 0; $j <= $i; $j++) $temp += $monthData[ $j ];
            array_push($res, $temp);
        }

        return $res;
    }

    /**
     * 获取闰月
     * @param year integer 阴历年份
     */
    function getLeapMonth($year)
    {
        $yearData = $this->lunarInfo[ $year - $this->MIN_YEAR ];

        return $yearData[0];
    }

    /**
     * 计算阴历日期与正月初一相隔的天数
     * @param year integer
     * @param month
     * @param date
     */
    function getDaysBetweenLunar($year, $month, $date)
    {
        $yearMonth = $this->getLunarMonths($year);
        $res = 0;
        for ($i = 1; $i < $month; $i++) $res += $yearMonth[ $i - 1 ];
        $res += $date - 1;

        return $res;
    }

    /**
     * 计算2个阳历日期之间的天数
     * 一般用于计算某一年从正月初一到被查询的那天已经过了多少天.
     * 例如:要查询2018年10月29日对应的阴历(2018年春节的阳历是2月16日)
     * year=2018;
     * end_month=10;
     * end_date=29;
     * begin_month=2;
     * begin_date=16;
     * @param year integer 阳历年
     * @param end_month integer 结束-阳历月(被查询的日期-月)
     * @param end_date integer 结束-阳历日(被查询的日期-日)
     * @param begin_month integer 开始-阳历月 (这一年阴历正月对应的阳历月)
     * @param begin_date integer 开始-阳历日 (这一年阴历正月初一对应的阳历日)
     */
    function getDaysBetweenSolar($year, $end_month, $end_date, $begin_month, $begin_date)
    {
        $a = mktime(0, 0, 0, $end_month, $end_date, $year);
        $b = mktime(0, 0, 0, $begin_month, $begin_date, $year);

        return ceil(($a - $b) / 24 / 3600);
    }

    /**
     * 根据距离正月初一的天数计算阴历日期
     * @param year integer 阳历年
     * @param days integer 天数
     */
    function getLunarByBetween($year, $days)
    {
        $lunarArray = [];
        $t = 0;
        $e = 0;
        $leapMonth = 0;
        if ($days == 0) {
            array_push($lunarArray, $year, '正月', '初一');
            $t = 1;
            $e = 1;
        } else {
            //解决元旦到春节这段时间的年份差
            $year = $days > 0 ? $year : ($year - 1);

            //阴历每月月底据该年正月初一多少天
            $yearMonth = $this->getLunarYearMonths($year);

            //获取闰月
            $leapMonth = $this->getLeapMonth($year);

            $days = $days > 0 ? $days : ($this->getLunarYearDays($year) + $days);
            for ($i = 0; $i < 13; $i++) {
                if ($days == $yearMonth[ $i ]) {
                    $t = $i + 2;
                    $e = 1;
                    break;
                } else if ($days < $yearMonth[ $i ]) {
                    $t = $i + 1;
                    $e = $days - (empty($yearMonth[ $i - 1 ]) ? 0 : $yearMonth[ $i - 1 ]) + 1;
                    break;
                }
            }
            $m = ($leapMonth != 0 && $t == $leapMonth + 1) ?
                ('闰' . $this->getCapitalNum($t - 1, true)) :
                $this->getCapitalNum(($leapMonth != 0 && $leapMonth + 1 < $t ? ($t - 1) : $t), true);
            array_push($lunarArray, $year, $m, $this->getCapitalNum($e, false));
        }

        $lunarArray[] = $this->getLunarYearName($year);// 天干地支
        array_push($lunarArray, $t, $e);
        $lunarArray[] = $this->getYearZodiac($year);// 12生肖
        $lunarArray[] = $leapMonth;// 闰几月

        return $lunarArray;
    }

    /**
     * 获取数字的阴历叫法
     * @param num 数字
     * @param isMonth 是否是月份的数字
     */
    function getCapitalNum($num, $isMonth)
    {
        $isMonth = $isMonth || false;
        $dateHash = [
            '0' => '', '1' => '一', '2' => '二', '3' => '三', '4' => '四', '5' => '五',
            '6' => '六', '7' => '七', '8' => '八', '9' => '九', '10' => '十 '
        ];
        $monthHash = [
            '0' => '', '1' => '正月', '2' => '二月', '3' => '三月', '4' => '四月', '5' => '五月',
            '6' => '六月', '7' => '七月', '8' => '八月', '9' => '九月', '10' => '十月',
            '11' => '冬月', '12' => '腊月'
        ];
        $res = '';
        if ($isMonth) $res = $monthHash[ $num ];
        else {
            if ($num <= 10) $res = '初' . $dateHash[ $num ];
            else if ($num > 10 && $num < 20) $res = '十' . $dateHash[ $num - 10 ];
            else if ($num == 20) $res = "二十";
            else if ($num > 20 && $num < 30) $res = "廿" . $dateHash[ $num - 20 ];
            else if ($num == 30) $res = "三十";
        }

        return $res;
    }

    /*
     * 节气通用算法
     */
    function getJieQi($_year, $month, $day)
    {
        $year = substr($_year, -2) + 0;
        $coefficient = [
            [5.4055, 2019, -1],//小寒
            [20.12, 2082, 1],//大寒
            [3.87],//立春
            [18.74, 2026, -1],//雨水
            [5.63],//惊蛰
            [20.646, 2084, 1],//春分
            [4.81],//清明
            [20.1],//谷雨
            [5.52, 1911, 1],//立夏
            [21.04, 2008, 1],//小满
            [5.678, 1902, 1],//芒种
            [21.37, 1928, 1],//夏至
            [7.108, 2016, 1],//小暑
            [22.83, 1922, 1],//大暑
            [7.5, 2002, 1],//立秋
            [23.13],//处暑
            [7.646, 1927, 1],//白露
            [23.042, 1942, 1],//秋分
            [8.318],//寒露
            [23.438, 2089, 1],//霜降
            [7.438, 2089, 1],//立冬
            [22.36, 1978, 1],//小雪
            [7.18, 1954, 1],//大雪
            [21.94, 2021, -1]//冬至
        ];
        $term_name = [
            "小寒", "大寒", "立春", "雨水", "惊蛰", "春分", "清明", "谷雨",
            "立夏", "小满", "芒种", "夏至", "小暑", "大暑", "立秋", "处暑",
            "白露", "秋分", "寒露", "霜降", "立冬", "小雪", "大雪", "冬至"
        ];
        $idx1 = ($month - 1) * 2;
        $_leap_value = floor(($year - 1) / 4);
        $day1 = floor($year * 0.2422 + $coefficient[ $idx1 ][0]) - $_leap_value;
        if (isset($coefficient[ $idx1 ][1]) && $coefficient[ $idx1 ][1] == $_year) $day1 += $coefficient[ $idx1 ][2];
        $day2 = floor($year * 0.2422 + $coefficient[ $idx1 + 1 ][0]) - $_leap_value;
        if (isset($coefficient[ $idx1 + 1 ][1]) && $coefficient[ $idx1 + 1 ][1] == $_year){
            $day1 += $coefficient[ $idx1 + 1 ][2];
        }
        $data = [];
        if ($day < $day1) {
            $data['name1'] = $term_name[ $idx1 - 1 ];
            $data['name2'] = $term_name[ $idx1 - 1 ] . '后';
        } else if ($day == $day1) {
            $data['name1'] = $term_name[ $idx1 ];
            $data['name2'] = $term_name[ $idx1 ];
        } else if ($day > $day1 && $day < $day2) {
            $data['name1'] = $term_name[ $idx1 ];
            $data['name2'] = $term_name[ $idx1 ] . '后';
        } else if ($day == $day2) {
            $data['name1'] = $term_name[ $idx1 + 1 ];
            $data['name2'] = $term_name[ $idx1 + 1 ];
        } else if ($day > $day2) {
            $data['name1'] = $term_name[ $idx1 + 1 ];
            $data['name2'] = $term_name[ $idx1 + 1 ] . '后';
        }

        return $data;
    }

    /*
     * 获取节日：特殊的节日只能修改此函数来计算
     */
    function getFestival($today, $nl_info = false, $config = 1)
    {
        if ($config == 1) {
            $arr_lunar = [
                '01-01' => '春节', '01-15' => '元宵节', '02-02' => '二月二', '05-05' => '端午节', '07-07' => '七夕节',
                '08-15' => '中秋节', '09-09' => '重阳节', '12-08' => '腊八节', '12-23' => '小年'
            ];
            $arr_solar = [
                '01-01' => '元旦', '02-14' => '情人节', '03-12' => '植树节', '04-01' => '愚人节', '05-01' => '劳动节',
                '06-01' => '儿童节', '10-01' => '国庆节', '10-31' => '万圣节', '12-24' => '平安夜', '12-25' => '圣诞节'
            ];
        }//需要不同节日的，用不同的$config,然后配置$arr_lunar和$arr_solar
        $festivals = [];
        list($y, $m, $d) = explode('-', $today);
        if (!$nl_info) $nl_info = $this->convertSolarToLunar($y, intval($m), intval($d));
        if ($nl_info[7] > 0 && $nl_info[7] < $nl_info[4]) $nl_info[4] -= 1;
        $md_lunar = substr('0' . $nl_info[4], -2) . '-' . substr('0' . $nl_info[5], -2);
        $md_solar = substr_replace($today, '', 0, 5);
        isset($arr_lunar[ $md_lunar ]) ? array_push($festivals, $arr_lunar[ $md_lunar ]) : '';
        isset($arr_solar[ $md_solar ]) ? array_push($festivals, $arr_solar[ $md_solar ]) : '';
        $glweek = date("w", strtotime($today));  //0-6
        if ($m == 5 && ($d > 7) && ($d < 15) && ($glweek == 0)) array_push($festivals, "母亲节");
        if ($m == 6 && ($d > 14) && ($d < 22) && ($glweek == 0)) array_push($festivals, "父亲节");
        $jieqi = $this->getJieQi($y, $m, $d);
        if ($jieqi) array_push($festivals, $jieqi);

        return implode('/', $festivals);
    }

    /*
    * 获取当前时间属于哪个时辰
    @param int $time 时间戳
    */
    function getTheHour($h)
    {
        $d = $h;
        if ($d == 23 || $d == 0) {
            return '子时';
        } else if ($d == 1 || $d == 2) {
            return '丑时';
        } else if ($d == 3 || $d == 4) {
            return '寅时';
        } else if ($d == 5 || $d == 6) {
            return '卯时';
        } else if ($d == 7 || $d == 8) {
            return '辰时';
        } else if ($d == 9 || $d == 10) {
            return '巳时';
        } else if ($d == 11 || $d == 12) {
            return '午时';
        } else if ($d == 13 || $d == 14) {
            return '未时';
        } else if ($d == 15 || $d == 16) {
            return '申时';
        } else if ($d == 17 || $d == 18) {
            return '酉时';
        } else if ($d == 19 || $d == 20) {
            return '戌时';
        } else if ($d == 21 || $d == 22) {
            return '亥时';
        }
    }
}