<?php
namespace app\components;

use app\helpers\Url;
use app\models\Note;
use Yii;
use yii\base\Component;

class NoteService extends Component
{
    public function search($where = [])
    {
        $fields = ['id'];
        $query = Note::find();
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

    public function delete(Note $note)
    {
        return $note->delete();
    }

    public function save(Note $note, $attributes)
    {
        if ($attributes) {
            $note->setAttributes($attributes, false);
        }

        return $note->save();
    }
}

?>
