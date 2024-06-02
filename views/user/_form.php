<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

/* @var $model fat2fast\auth\models\form\UserForm */

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
if(empty($model->roles)){
    $model->roles = [
      'User'
    ];
}
?>

<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form'],]); ?>
        <div class="card">

            <div class="card-body">

                <?php
                if(Yii::$app->user->identity->isSystemAdmin) {
                    echo $form->field($model, 'username')->textInput([
                        'maxlength' => true,
                        'autocomplete' => 'new-username',
                        'class' => 'form-control',
                        'readonly' => !Yii::$app->user->identity->isSystemAdmin
                    ])->label(Yii::t('authz', 'Username'), ['class' => 'form-label']) ;
                }
                ?>
                <?php echo $form->field($model, 'full_name')->textInput()->label(Yii::t('authz', 'Full Name')) ?>

                <?php echo $form->field($model, 'email')->textInput()->label(Yii::t('authz', 'Email')) ?>

                <?php
                if(Yii::$app->user->identity->isSystemAdmin){
                    echo $form->field($model, 'roles')->widget(Select2::class, [
                        'data' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
                        'options' => [
                            'multiple' => true,
                            'placeholder' => Yii::t('authz', 'Select'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ])->label(Yii::t('authz', 'Roles'));
                }
                ?>
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
