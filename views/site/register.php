<?php
use \yii\helpers\Url;

$this->title = "用户注册";
$this->params['pageId'] = 'user_register';
?>
<div class="login-form">
    <form method="post" action="<?= Url::to(['site/register']) ?>">
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input class="text-input" type="text" name="username" value="<?= $model->username ?>" required="true"
               placeholder="账号">
        <input class="text-input" type="email" name="email" value="<?= $model->email ?>" required="true"
               placeholder="邮箱">
        <input class="text-input" type="password" name="password" value="<?= $model->password ?>" required="true"
               placeholder="密码">
        <input class="text-input" type="password" name="rePassword" value="<?= $model->rePassword ?>" required="true"
               placeholder="确认密码">
        <input class="ch-input" name="is_agree" type="checkbox" value="1" checked> 同意<a href="<?= Url::to(['#']) ?>"
                                                                                        target="_blank">《用户注册协议》</a>
        <button class="login-button" type="submit">提交</button>
    </form>
    <div class="back">
        <a href="<?= Url::to(['site/login']) ?>"><
            <返回登录
        </a>
    </div>
</div>
