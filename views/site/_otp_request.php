<?php


use yii\bootstrap5\ActiveForm;


$form = ActiveForm::begin([
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
]);
?>
<?= $form->field($model, 'phoneNumber')
    ->textInput(['placeholder' => Yii::t('yii2-authz', 'Số diện thoại. ví dụ 0909123123')]); ?>
    <button type="submit" class="mbtn mbtn-lg mbtn-primary w-100">
        Đăng nhập
    </button>
<?php ActiveForm::end(); ?>
