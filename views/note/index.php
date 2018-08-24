<?php

use yii\data\ActiveDataProvider;

$this->params['pageId'] = 'note-page';
$this->title = '便签--一个字条,一个知识';
$query = Yii::$app->noteService->search()
    ->orderBy(['weight' => SORT_DESC, 'id' => SORT_DESC]);
$provider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => [
        'pageSize' => 24
    ]
]);
$notes = $provider->getModels();
?>
<ul class="note-blocks phone-hide">
    <?php
    foreach ($notes as $n) {
        ?>
        <li>
            <?= $n->title ?>
            <div class="popup">
                <p>
                    <?= nl2br($n->content) ?>
                </p>
            </div>
        </li>
    <?php } ?>
</ul>
<?php foreach ($notes as $n) { ?>
    <div class="phone-note pc-hide">
        <h4 class="top">
            <?= $n->title ?>
        </h4>
        <p class="body">
            <?= nl2br($n->content) ?>
        </p>
    </div>
<?php } ?>
