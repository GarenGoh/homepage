<?php
namespace app\components;

use app\helpers\Tools;
use app\helpers\Url;
use app\models\Advert;
use Yii;
use yii\base\Component;
use yii\console\controllers\HelpController;

class AdvertService extends Component
{
    public function search($where = [])
    {
        $fields = ['id', 'category'];
        $query = Advert::find();
        foreach ($fields as $f) {
            if (isset($where[$f])) {
                $query->andFilterWhere([$f => $where[$f]]);
            }
        }

        if (!Url::isAdmin()) {
            $query->andWhere(['is_enable' => 1]);
        }
        return $query;
    }

    public function delete(Advert $advert)
    {
        return $advert->delete();
    }

    public function save(Advert $advert, $attributes)
    {
        if ($attributes) {
            if (isset($attributes['summary']) && $attributes['summary']) {
                $attributes['summary'] = Tools::string($attributes['summary'], 60);
            }
            $advert->setAttributes($attributes, false);
        }

        return $advert->save();
    }
}

?>
