<?php

use yii\helpers\Url;

$this->title = "用户登录";
$this->params['pageId'] = 'user_login';
?>
<div class="login-form">
    <form style="" method="post" action="<?= Url::to(['site/login']) ?>">
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input class="text-input" type="text" name="account" value="<?= $model->account ?>" required="true"
               placeholder="用户名/邮箱/手机">
        <input class="text-input" type="password" name="password" value="<?= $model->password ?>" required="true"
               placeholder="密码">
        <input class="ch-input" name="is_remember" type="checkbox" value="1" checked> 30天内免登陆
        <button class="login-button" type="submit">提交</button>
    </form>
    <div class="back">
        <a href="<?= Url::to(['site/register']) ?>"><
            <我要注册
        </a>
    </div>
</div>
