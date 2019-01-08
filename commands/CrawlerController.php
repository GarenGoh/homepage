<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class CrawlerController extends Controller
{
    /**
     * 爬取菜鸟教程数据
     */
    public function actionRunoob()
    {

        $url = "http://www.runoob.com/?s=php++函数&page=1";

        Yii::$app->httpClient->setTransport('yii\httpclient\CurlTransport');
        $request = Yii::$app->httpClient->createRequest()
            ->setOptions(['timeout' => 5])
            //->setContent(json_encode($post, JSON_UNESCAPED_UNICODE))
            ->setUrl($url)
            //->setHeaders(['Content-type' => 'application/json'])
            ->setMethod('GET');

        $content = Yii::$app->httpClient->send($request);

        preg_match_all("/h2.+(http.+?\.html)[\s\S]+?PHP\<\/em\>\ (.+?)\<em\>函数/",$content->content, $data );

        $arr = array_combine($data[2], $data[1]);
        foreach ($arr as $key => $value){
            echo $key,"====>",$value,"\n";
        }
    }

}
