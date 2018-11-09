<?php
namespace app\components;

use app\helpers\Url;
use app\models\Article;
use Yii;
use yii\base\Component;

class ArticleService extends Component
{
    /**
     * @param array $where
     * @return \yii\db\ActiveQuery
     */
    public function search($where = [])
    {
        $fields = ['id', 'category', 'read_count'];
        $query = Article::find();
        foreach ($fields as $f) {
            if (isset($where[$f])) {
                $query->andFilterWhere([$f => $where[$f]]);
            }
        }

        $likeFields = ['title', 'keywords'];
        foreach ($likeFields as $lf) {
            if (isset($where[$lf]) && $where[$lf]) {
                $query->andWhere(['like', $lf, "%{$where[$lf]}%", false]);
            }
        }
        if (!Url::isAdmin()) {
            $query->andWhere(['is_enable' => 1]);
        }
        return $query;
    }

    public function delete(Article $article)
    {
        return $article->delete();
    }

    public function save(Article $article, $attributes)
    {
        if ($attributes) {
            $article->setAttributes($attributes, false);
        }

        return $article->save();
    }
}

?>
