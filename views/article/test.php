<?php
use app\helpers\Tools;

?>
<html>
<head>
    <title>学习与测试</title>
</head>
<body>
<div style="background: #9cb2dd;border-radius:3px;">
    <h3>语言切换：
        <small>配置在common.php</small>
    </h3>
    <?php
    Tools::dev(Yii::t('yii', 'Update'), 1);
    Tools::dev(Yii::t('cn2', 'cn'), 1);
    Tools::dev(Yii::t('cn4', 'vi', [], 'vi'), 1);
    ?>
</div>
<div style="background: #96ddb8;border-radius:3px;">
    <h3>十进制换算为其他（2-62）进制：</h3>
    <?php
    Tools::dev(Tools::convert(312312312, 32), 1);
    Tools::dev(Tools::convert(31123, 2), 1);
    Tools::dev(Tools::convert(42342, 16), 1);
    ?>
</div>
<div style="background: #ddb39e;border-radius:3px;">
    <h3>PHP数组排序算法：</h3>
    <?php
    $arr = [1, 43, 54, 62, 21];
    echo "<h4>冒泡排序：</h4>";
    Tools::dev(Tools::bubbleSort($arr), 1);
    echo "<h4>选择排序：</h4>";
    Tools::dev(Tools::selectSort($arr), 1);
    echo "<h4>插入排序：</h4>";
    Tools::dev(Tools::insertSort($arr), 1);
    echo "<h4>快速排序：</h4>";
    Tools::dev(Tools::quickSort($arr), 1);
    ?>
</div>
</body>
</html>
test2