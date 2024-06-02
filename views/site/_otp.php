<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap5\ActiveForm */

/* @var $model app\models\LoginForm */

// $this->title = 'Đăng nhập';
// $this->params['breadcrumbs'][] = $this->title;
?>
<?php \yii\widgets\Pjax::begin() ?>
<?= $this->render("_otp_request", ['model' => $requestOtpModel]) ?>
<?php
if (Yii::$app->user->isGuest) {
    echo $this->render("_otp_verify", ['model' => $verifyOtpModel, 'showModal' => $showModal]);
} else {
    echo $this->render("_otp_success", ['model' => $verifyOtpModel, 'showModal' => $showModal]);
}
?>
<?php \yii\widgets\Pjax::end() ?>
