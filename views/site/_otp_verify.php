<?php

use yii\bootstrap5\ActiveForm;

?>
<div class="modal modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'data-pjax' => 'true',
                    ],
                    'action' => ['site/login-otp'],
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'horizontalCssClasses' => [
                            'label' => 'form-label mform-label',
                            'wrapper' => 'col-sm-12'
                        ]
                    ]
                ]); ?>
                <h5 class="mtext-heading3 fw-bold text-mprimary-500 mb-3">
                    Nhập OTP </h5>
                <p class="mtext-paragraph fw-normal text-mbw-600">
                    Hệ thống đã gửi OTP đến số điện thoại bạn cung cấp, vui lòng kiểm tra tin nhắn trên điện thoại của
                    bạn </p>
                <?= \yii\helpers\Html::activeHiddenInput($model, 'phoneNumber')?>
                <?= $form->field($model, 'otpToken')->textInput(['placeholder' => Yii::t('yii2-authz', 'Vui lòng nhập mã OTP')]); ?>
                    <?= $form->field($model, 'reCaptcha')->widget(
                    \himiklab\yii2\recaptcha\ReCaptcha3::className(),
                        [
                            'siteKey' => Yii::$app->params['reCaptcha']['site_key_v3'],
                            'action' => 'login',
                        ]
                    ) ->label(false) ?>
                <button type="submit" class="mbtn mbtn-primary mb-3 w-100">Xác nhận</button>
                <div class="mtext-heading5 fw-normal text-mbw-600 text-center" id="otp-countdown-wrapper">
                    Mã OTP sẽ hết hạn trong <span class="text-merror fw-bold" id="otp-countdown"></span>
                </div>
                <div class="mtext-heading5 fw-normal text-mbw-600 text-center mb-3 d-none" id="otp-expired-wrapper">
                    Mã OTP đã hết hạn <a class="fw-semibold" href="#">Lấy mã OTP mới</a>
                </div>
                <?php ActiveForm::end() ?>
                <script>
                    var timeInSecs;
                    var ticker;

                    function startTimer(secs) {
                        timeInSecs = parseInt(secs);
                        ticker = setInterval("tick()", 1000);
                    }

                    function tick( ) {
                        var secs = timeInSecs;
                        if (secs > 0) {
                            timeInSecs--;
                        }
                        else {
                            document.getElementById("otp-countdown-wrapper").classList.add("d-none");
                            document.getElementById("otp-expired-wrapper").classList.remove("d-none");
                            clearInterval(ticker);
                        }

                        var mins = Math.floor(secs/60);
                        secs %= 60;
                        var pretty = ( (mins < 10) ? "0" : "" ) + mins + ":" + ( (secs < 10) ? "0" : "" ) + secs;

                        document.getElementById("otp-countdown").innerHTML = pretty;
                        console.log(pretty)
                    }
                    var countdownSecounds = <?= $model->remainSeconds ?>;
                    if (countdownSecounds > 0) {
                        startTimer(countdownSecounds);
                    }
                </script>
            </div>
        </div>
    </div>
</div>

<?php
if ($showModal) {
    $js = <<<JS
    var myModal = new bootstrap.Modal(document.getElementById('loginModal'));
    myModal.show();
    JS;
    $this->registerJs($js, \yii\web\View::POS_HEAD);
}
