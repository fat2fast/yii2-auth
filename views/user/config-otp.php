<?php

use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\Writer\PngWriter;
use fat2fast\otp\widgets\OtpInit;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \fat2fast\auth\models\form\UserOtpForm */
$this->title = Yii::t('app', 'Profile: {name}', [
    'name' => $model->username,
]);
$model->use_totp = 1;
$this->params['breadcrumbs'][] = $this->title;

$controllerId = $this->context->uniqueId . '/';
$form = ActiveForm::begin([
    'id' => 'otp_form_id',
]);
?>
<div class="user-view">
    <?php
    if (Yii::$app->user->identity->force_change_pwd) {
        echo Html::tag('span', '<strong>Thông báo!</strong> Vui lòng thay đổi mật khẩu mới để tiếp tục sử dụng hệ thống', [
            'class' => 'alert alert-danger fade in alert-dismissible show']);
    }
    ?>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('app', 'Config Two-factor Authentication') ?></h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-5">
                            <?php echo $form->field($model, 'secret')->widget(
                                OtpInit::class, [
                                'component' => 'otp',

                                // link text
                                'link' => false,

                                'QrParams' => [
                                    // pixels width
                                    'size' => 200,

                                    // margin around QR-code
                                    'margin' => 10,

                                    // Path to logo on image
//                                    'logo' => Yii::getAlias("@app/web/icon.png"),

                                    // Width logo on image
                                    'logoWidth' => 50,

                                    // RGB color
                                    'foregroundColor' => [0, 0, 0],

                                    // RGB color
                                    'backgroundColor' => [255, 255, 255],

                                    // Qulity of QR: LOW, MEDIUM, HIGHT, QUARTILE
                                    'level' => ErrorCorrectionLevelInterface::HIGH,

                                    // Image format: PNG, JPG, SVG, EPS
                                    'type' => PngWriter::class,

                                    // Locale
                                    'encoding' => 'UTF-8',

                                    // Text on image under QR code
                                    'label' => '',

                                    // by default image create and save at Yii::$app->runtimePath . '/temporaryQR/'
//                            'outfile' => '/tmp/'.uniqid(),

                                    // save or delete after generate
                                    'save' => false,
                                ]
                            ])->label(false); ?>
                            <?php echo $form->field($model, 'use_totp')->hiddenInput(['value' => $model->use_totp])->label(false) ?>
                        </div>
                        <div class="col-md-6 "
                             style="background: #fafafa; border: 1px solid gray; word-wrap: break-word;">
                            <p><?php echo Yii::t("app", "Or enter this code into your authentication app") ?></p>
                            <p><?php echo Yii::t("app", "Account : ") ?><?php echo Yii::t("app", "Pandora Portal") ?></p>
                            <p>
                                <?php echo Yii::t("app", "Key : ") . '<b>' . nl2br(Html::encode($model->secret)) . '</b>' ?>
                            </p>
                            <p>
                                <?php echo Yii::t("app", "Time based : Yes") ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p>
                                <?php echo $form->field($model, 'otp')->textInput(['type' => 'number'])->label(Yii::t("app", "Enter Code")) ?>
                            </p>
                            <p>
                                <?php echo Html::submitButton('<i class="fa fa-key" aria-hidden="true"></i> ' . Yii::t('app', 'Register with two-factor app'), [
                                    'class' => 'btn btn-success btn-submit',
                                    'title' => Yii::t('app', 'Register with two-factor app'),
                                ]) ?>
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
