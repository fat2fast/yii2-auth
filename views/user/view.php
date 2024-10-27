<?php

use fat2fast\auth\components\Helper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \fat2fast\auth\models\User */
$this->title = Yii::t('authz', 'Profile: {name}', [
    'name' => $model->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('authz', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$controllerId = '/'.$this->context->uniqueId . '/';
$temp = $controllerId . 'config-otp';
?>
<div class="user-view">
    <div class="row">
        <?php
        if (Yii::$app->user->identity->force_change_pwd) {
            echo Html::tag('span', '<strong>Thông báo!</strong> Vui lòng thay đổi mật khẩu mới để tiếp tục sử dụng hệ thống', [
                'class' => 'alert alert-danger fade in alert-dismissible show']);
        }
        ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <p>
                        <?php
                        if (Helper::checkRoute($controllerId . 'update')) {
                            echo Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('authz', 'Edit'), ['update', 'id' => $model->id], [
                                    'class' => 'btn btn-primary',
                                ]) . ' ';
                        }
                        ?>
                        <?php
                        if (Helper::checkRoute($controllerId . 'change-password')&& (Yii::$app->user->identity->id == $model->id)) {
                            echo Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('authz', 'Change password'), ['change-password', 'id' => $model->id], [
                                    'class' => 'btn btn-primary',
                                ]) . ' ';
                        }
                        ?>
                        <?php
                        if (Helper::checkRoute($controllerId . 'reset-password') && (Yii::$app->user->identity->isSystemAdmin) && (Yii::$app->user->identity->id != $model->id)) {
                            echo Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('authz', 'Reset password'), ['reset-password', 'id' => $model->id], [
                                    'class' => 'btn btn-primary',
                                ]) . ' ';
                        }
                        ?>
                        <?php

                        if (!$model->use_totp) {
                            if (Helper::checkRoute($controllerId . 'config-otp') && (Yii::$app->user->identity->id == $model->id)) {
                                echo Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('authz', 'Config Two-factor Authentication'), ['config-otp', 'id' => $model->id], [
                                        'class' => 'btn btn-primary',
                                    ]) . ' ';
                            }
                        } else if (Helper::checkRoute($controllerId . 'disable-otp')) {
                            echo Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('authz', 'Disable Two-factor Authentication'), ['disable-otp', 'id' => $model->id], [
                                    'class' => 'btn btn-danger',
                                ]) . ' ';
                        }

                        ?>
                    </p>
                    <?=
                    DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'username',
                                'value' => $model->username != null ? $model->username : '',
                                'label' => Yii::t('authz', 'Username'),
                            ],
                            [
                                'attribute' => 'full_name',
                                'value' => $model->full_name != null ? $model->full_name : '',
                                'label' => Yii::t('authz', 'Full Name'),
                            ],
                            [
                                'attribute' => 'created_at',
                                'value' => $model->created_at != null ? date('d-m-Y', $model->created_at) : '',
                                'label' => Yii::t('authz', 'Create Date'),
                            ],
                            'email:email',
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => $model->getStatusLabel($model->status),
                                'label' => Yii::t('authz', 'Status'),
                            ],
                            [
                                'attribute' => 'use_totp',
                                'format' => 'raw',
                                'value' => $model->getTotpLabel(),
                                'label' => Yii::t('authz', 'Two-factor Authentication'),
                            ],
                            [
                                'label' => Yii::t('authz', 'Roles'),
                                'value' => $model->displayRoles,
                                'format' => 'raw',
                            ],
                        ],
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
