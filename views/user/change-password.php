<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var fat2fast\auth\models\form\UserChangePassword $model */
/** @var yii\widgets\ActiveForm $form */
$this->title = Yii::t('authz', 'Change password');
$this->params['breadcrumbs'][] = ['label' => Yii::t('authz', 'Users'), 'url' => ['view','id' => $model->user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <?php
    if (Yii::$app->user->identity->force_change_pwd) {
        echo Html::tag('span', '<strong>Thông báo!</strong> Vui lòng thay đổi mật khẩu mới để tiếp tục sử dụng hệ thống', [
            'class' => 'alert alert-danger fade in alert-dismissible show']);
    }
    ?>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form'],]); ?>
        <div class="card">

            <div class="card-body">

                <?php echo $form->field($model,'old_password')->label(Yii::t("authz",'Old Password'))->passwordInput()?>

                <div class="panel panel-warning">
                    <div class="panel-body">
                        <?php echo $form->field($model,'new_password')->passwordInput([
                            'maxlength'    => true,
                            'autocomplete' => 'new-password',
                        ])->label(Yii::t("authz",'New Password'))?>

                        <?php echo $form->field($model,'retype_password')->passwordInput([
                            'maxlength'    => true,
                            'autocomplete' => 'new-password',
                        ])->label(Yii::t("authz",'Retype Password'))?>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end mb-3">
            <button type="submit" class="btn btn-primary w-sm">Gửi</button>
        </div>
        <?php ActiveForm::end(); ?>
        <!-- end card -->

    </div>
    <!-- end col -->
</div>
