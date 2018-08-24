<?php
namespace app\helpers;

use yii\base\Object;

class Tools
{
    /*
     * 随机生成一个由大小写字母及书记组成的字符串；
     * $length 生成字符串的长度；
     */
    public static function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }

    /*
     * *****生成缩略图*****
     * 只考虑jpg,png,gif格式
     * $srcImgPath 源图象路径
     * $targetImgPath 目标图象路径
     * $targetW 目标图象宽度
     * $targetH 目标图象高度
     */
    public static function makeThumbnail($srcImgPath, $targetImgPath, $targetW, $targetH)
    {
        $imgSize = GetImageSize($srcImgPath);
        $imgType = $imgSize[2];
        //@ 使函数不向页面输出错误信息
        switch ($imgType) {
            case 1:
                $srcImg = @ImageCreateFromGIF($srcImgPath);
                break;
            case 2:
                $srcImg = @ImageCreateFromJpeg($srcImgPath);
                break;
            case 3:
                $srcImg = @ImageCreateFromPNG($srcImgPath);
                break;
        }
        //取源图象的宽高
        $srcW = ImageSX($srcImg);
        $srcH = ImageSY($srcImg);
        if ($srcW > $targetW || $srcH > $targetH) {
            $targetX = 0;
            $targetY = 0;
            if ($srcW > $srcH) {
                $finaW = $targetW;
                $finalH = round($srcH * $finaW / $srcW);
                $targetY = floor(($targetH - $finalH) / 2);
            } else {
                $finalH = $targetH;
                $finaW = round($srcW * $finalH / $srcH);
                $targetX = floor(($targetW - $finaW) / 2);
            }
            //function_exists 检查函数是否已定义
            //ImageCreateTrueColor 本函数需要GD2.0.1或更高版本
            if (function_exists("ImageCreateTrueColor")) {
                $targetImg = ImageCreateTrueColor($targetW, $targetH);
            } else {
                $targetImg = ImageCreate($targetW, $targetH);
            }
            $targetX = ($targetX < 0) ? 0 : $targetX;
            $targetY = ($targetX < 0) ? 0 : $targetY;
            $targetX = ($targetX > ($targetW / 2)) ? floor($targetW / 2) : $targetX;
            $targetY = ($targetY > ($targetH / 2)) ? floor($targetH / 2) : $targetY;
            //背景白色
            $white = ImageColorAllocate($targetImg, 255, 255, 255);
            ImageFilledRectangle($targetImg, 0, 0, $targetW, $targetH, $white);
            /*
                   PHP的GD扩展提供了两个函数来缩放图象：
                   ImageCopyResized 在所有GD版本中有效，其缩放图象的算法比较粗糙，可能会导致图象边缘的锯齿。
                   ImageCopyResampled 需要GD2.0.1或更高版本，其像素插值算法得到的图象边缘比较平滑，该函数的速度比ImageCopyResized慢。
            */
            if (function_exists("ImageCopyResampled")) {
                ImageCopyResampled($targetImg, $srcImg, $targetX, $targetY, 0, 0, $finaW, $finalH, $srcW, $srcH);
            } else {
                ImageCopyResized($targetImg, $srcImg, $targetX, $targetY, 0, 0, $finaW, $finalH, $srcW, $srcH);
            }
            switch ($imgType) {
                case 1:
                    ImageGIF($targetImg, $targetImgPath);
                    break;
                case 2:
                    ImageJpeg($targetImg, $targetImgPath);
                    break;
                case 3:
                    ImagePNG($targetImg, $targetImgPath);
                    break;
            }
            ImageDestroy($srcImg);
            ImageDestroy($targetImg);
        } else //不超出指定宽高则直接复制
        {
            copy($srcImgPath, $targetImgPath);
            ImageDestroy($srcImg);
        }
    }

    public static function dev($content, $end = 0)
    {
        echo '<pre>';
        print_r($content);
        echo '</pre>';
        if ($end == 0) {
            exit;
        }
    }

    public static function convert($num, $adv, $number = '')
    {
        if ($num >= 0) {
            $arr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
                'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
                'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            ];
            $rem = $num % $adv;
            $number = $arr[$rem] . $number;
            $int = floor($num / $adv);
            if ($int >= $adv) {
                $number = self::convert($int, $adv, $number);
            } elseif ($int > 0) {
                $number = $arr[$int] . $number;
            }
        } else {
            $num = str_replace("-", "", $num);
            $number = '-' . self::convert($num, $adv, $number);
        }
        return $number;
    }

    /*
     * 冒泡排序
     * 循环 (n-1)+(n-2)...1 次,及:
     * (n-1)*n/2
     */
    public static function bubbleSort($arr)
    {
        $len = count($arr);
        //该层循环控制 需要冒泡的轮数
        for ($i = 1; $i < $len; $i++) { //该层循环用来控制每轮 冒出一个数 需要比较的次数
            for ($k = 0; $k < $len - $i; $k++) {
                if ($arr[$k] > $arr[$k + 1]) {
                    $tmp = $arr[$k + 1];
                    $arr[$k + 1] = $arr[$k];
                    $arr[$k] = $tmp;
                }
            }
        }
        return $arr;
    }

    /*
     * 选择排序
     * 循环 (n-1)+(n-2)...1 次,及:
     * (n-1)*n/2
     */
    public static function selectSort($arr)
    {
        //双重循环完成，外层控制轮数，内层控制比较次数
        $len = count($arr);
        for ($i = 0; $i < $len - 1; $i++) {
            //先假设最小的值的位置
            $p = $i;

            for ($j = $i + 1; $j < $len; $j++) {
                //$arr[$p] 是当前已知的最小值
                if ($arr[$p] > $arr[$j]) {
                    //比较，发现更小的,记录下最小值的位置；并且在下次比较时采用已知的最小值进行比较。
                    $p = $j;
                }
            }
            //已经确定了当前的最小值的位置，保存到$p中。如果发现最小值的位置与当前假设的位置$i不同，则位置互换即可。
            if ($p != $i) {
                $tmp = $arr[$p];
                $arr[$p] = $arr[$i];
                $arr[$i] = $tmp;
            }
        }
        //返回最终结果
        return $arr;
    }

    /*
     * 插入排序
     * 原数组正好倒序时,循环次数最多:
     * (n-1)*n/2
     * 原数组正好顺序时,循环次数最少:
     * (n-1)
     */
    public static function insertSort($arr)
    {
        $len = count($arr);
        for ($i = 1; $i < $len; $i++) {
            $tmp = $arr[$i];
            //内层循环控制，比较并插入
            for ($j = $i - 1; $j >= 0; $j--) {
                if ($tmp < $arr[$j]) {
                    //发现插入的元素要小，交换位置，将后边的元素与前面的元素互换
                    $arr[$j + 1] = $arr[$j];
                    $arr[$j] = $tmp;
                } else {
                    //如果碰到不需要移动的元素，由于是已经排序好是数组，则前面的就不需要再次比较了。
                    break;
                }
            }
        }
        return $arr;
    }

    /*
     * 快速排序
     * 原数组本身顺序越整齐,循环次数越多. 最多 (n-1)*n/2
     *
     * php sort()函数采用此排序方法.
     */
    public static function quickSort($arr)
    {
        //先判断是否需要继续进行
        $length = count($arr);
        if ($length <= 1) {
            return $arr;
        }
        //选择第一个元素作为基准
        $base_num = $arr[0];
        //遍历除了标尺外的所有元素，按照大小关系放入两个数组内
        //初始化两个数组
        $left_array = array();  //小于基准的
        $right_array = array();  //大于基准的
        for ($i = 1; $i < $length; $i++) {
            if ($base_num > $arr[$i]) {
                //放入左边数组
                $left_array[] = $arr[$i];
            } else {
                //放入右边
                $right_array[] = $arr[$i];
            }
        }
        //再分别对左边和右边的数组进行相同的排序处理方式递归调用这个函数
        $left_array = self::quicksort($left_array);
        $right_array = self::quicksort($right_array);
        //合并
        return array_merge($left_array, array($base_num), $right_array);
    }

    public static function mb_substr_replace($str, $replace, $start, $length, $encoding = 'utf-8')
    {
        $lstr = mb_substr($str, 0, $start, $encoding);
        $rstr = mb_substr($str, ($start + $length), null, $encoding);
        return $lstr . $replace . $rstr;
    }

    /*
     * 截取字符串,适用于中英文.
     */
    public static function string($string, $length, $suffix = '...')
    {
        $string = strip_tags($string);
        $total = mb_strlen($string, 'utf-8');
        if ($length >= $total) {
            return $string;
        } else {
            return mb_substr($string, 0, $length, 'utf-8') . $suffix;
        }
    }
}

?>
